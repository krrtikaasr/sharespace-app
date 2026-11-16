<?php
session_start();
require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

$userId   = current_user_id();
$donasiId = (int)($_GET['id'] ?? $_POST['donasi_id'] ?? 0);

/* ---------- Proses Tolak / Terima (dipanggil lewat fetch) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $aksi      = $_POST['aksi'] ?? '';
    $requestId = (int)($_POST['request_id'] ?? 0);
    $ok        = false;

    $tokenOk = hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '');
    if ($pdo && $userId && $tokenOk && in_array($aksi, ['tolak', 'terima'], true)) {
        try {
            $pdo->beginTransaction();

            // Pastikan donasi milik pengguna dan permintaan masih menunggu
            $stmt = $pdo->prepare('SELECT id, nama_barang FROM donations WHERE id = ? AND user_id = ?');
            $stmt->execute([$donasiId, $userId]);
            $don = $stmt->fetch();

            $stmt = $pdo->prepare("SELECT requester_id FROM requests WHERE id = ? AND donation_id = ? AND status = 'menunggu' FOR UPDATE");
            $stmt->execute([$requestId, $donasiId]);
            $req = $stmt->fetch();

            if ($don && $req) {
                $statusBaru = $aksi === 'tolak' ? 'ditolak' : 'diterima';
                $pdo->prepare('UPDATE requests SET status = ? WHERE id = ?')->execute([$statusBaru, $requestId]);

                if ($aksi === 'terima') {
                    $pdo->prepare("UPDATE donations SET status = 'atur_jadwal' WHERE id = ?")->execute([$donasiId]);
                }
                tambah_notifikasi($pdo, (int)$req['requester_id'],
                    'Permintaan ' . $don['nama_barang'] . ($aksi === 'terima' ? ' diterima' : ' ditolak'),
                    null, 'status-donasi.php');
                $pdo->commit();
                $ok = true;
            } else {
                $pdo->rollBack();
            }
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log($ex->getMessage());
        }
    }

    echo json_encode([
        'ok'       => $ok,
        'redirect' => $aksi === 'terima' ? 'atur-jadwal.php?id=' . $donasiId : 'status-donasi.php',
    ]);
    exit;
}

/* ---------- Data tampilan ---------- */
$donasi = null; $pemohon = []; $dbError = null;
if ($pdo && $userId && $donasiId) {
    try {
        $stmt = $pdo->prepare('SELECT id, nama_barang, jumlah, foto, status, created_at FROM donations WHERE id = ? AND user_id = ?');
        $stmt->execute([$donasiId, $userId]);
        $donasi = $stmt->fetch() ?: null;

        if ($donasi) {
            $stmt = $pdo->prepare(
                "SELECT r.id, r.pesan, r.jarak_km, u.id AS id_user, u.nama
                   FROM requests r JOIN users u ON u.id = r.requester_id
                  WHERE r.donation_id = ? AND r.status = 'menunggu' ORDER BY r.created_at ASC"
            );
            $stmt->execute([$donasiId]);
            $pemohon = $stmt->fetchAll();
        }
    } catch (Throwable $ex) { $dbError = $ex->getMessage(); }
}
$daftarSamping = ambil_donasi_user($pdo, $userId);
$idAktif = $donasiId;

$judulHalaman = 'Status & Riwayat';
$subjudul     = 'Aktivitas  ›  Donasi Saya  ›  ' . ($donasi['nama_barang'] ?? 'Detail');
$kembaliHref  = 'status-donasi.php';
$navAktif     = 'aktivitas';
include __DIR__ . '/includes/header.php';
?>

<!-- Notifikasi pop up penolakan (warna sama dengan tombol Tolak) -->
<div id="toast" role="alert" class="fixed top-4 left-1/2 -translate-x-1/2 w-[calc(100%-2rem)] max-w-md z-50 bg-[#FECACA] text-[#B91C1C] rounded-2xl px-4 py-3 shadow-lg flex items-center gap-2.5 text-xs lg:text-sm font-semibold -translate-y-24 opacity-0 transition-all duration-300 pointer-events-none">
    <?= icon('x', 'w-4 h-4 shrink-0') ?><span>Anda menolak permintaan ini!</span>
</div>

<?php if ($dbError): ?>
    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">Database bermasalah: <?= e($dbError) ?></div>
<?php endif; ?>

<div class="lg:flex lg:gap-8 lg:items-start">
    <?php include __DIR__ . '/includes/daftar_samping.php'; ?>

    <section class="flex-1 min-w-0">
        <?php if (!$donasi): ?>
            <p class="text-xs text-slate-400 text-center py-16">Data donasi tidak ditemukan.</p>
        <?php else: ?>
            <div class="bg-white border border-slate-300 lg:border-slate-200 rounded-[20px] lg:rounded-3xl overflow-hidden">
                <div class="p-3 lg:p-7 flex items-center gap-3.5 lg:gap-5 border-b border-slate-200">
                    <?= foto_atau_placeholder($donasi['foto'], $donasi['nama_barang'], 'w-[62px] h-[62px] lg:w-24 lg:h-24 rounded-xl lg:rounded-2xl shrink-0') ?>
                    <div class="min-w-0">
                        <h2 class="font-bold text-sm lg:text-2xl lg:font-extrabold truncate"><?= e($donasi['nama_barang']) ?></h2>
                        <p class="text-[11px] lg:text-sm text-slate-400 lg:text-slate-500 mt-0.5 lg:mt-1"><?= (int)$donasi['jumlah'] ?> unit • <?= e(waktu_relatif($donasi['created_at'])) ?></p>
                        <div class="mt-1.5 lg:mt-2.5"><?= badge_status($donasi['status']) ?></div>
                    </div>
                </div>

                <div class="p-3.5 lg:p-7">
                    <div class="flex items-center justify-between mb-3 lg:mb-5">
                        <h3 class="font-semibold lg:font-bold text-xs lg:text-lg">Verifikasi Pemohon</h3>
                        <span class="hidden lg:inline-flex px-3 py-1 rounded-full bg-slate-100 text-xs font-semibold text-slate-500"><?= count($pemohon) ?> permintaan</span>
                    </div>

                    <?php if (!$pemohon): ?>
                        <p class="text-[11px] lg:text-sm text-slate-400 text-center py-6">Belum ada permintaan menunggu untuk barang ini.</p>
                    <?php endif; ?>

                    <?php foreach ($pemohon as $pm): ?>
                        <div class="request-item mb-5" data-request-id="<?= (int)$pm['id'] ?>">
                            <div class="border border-slate-400 lg:border-slate-300 lg:bg-slate-50 rounded-2xl lg:rounded-[20px] p-3 lg:p-6">
                                <div class="flex gap-3 lg:gap-4 items-start">
                                    <div class="relative shrink-0">
                                        <span class="w-11 h-11 lg:w-16 lg:h-16 rounded-full flex items-center justify-center text-sm lg:text-lg font-bold <?= warna_avatar((int)$pm['id_user']) ?>"><?= e(inisial($pm['nama'])) ?></span>
                                        <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 lg:w-[22px] lg:h-[22px] rounded-full bg-[#2563EB] text-white flex items-center justify-center ring-2 ring-white"><?= icon('check', 'w-2.5 h-2.5 lg:w-3 lg:h-3') ?></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="font-bold text-xs lg:text-lg"><?= e($pm['nama']) ?></p>
                                            <span class="hidden lg:inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#D1FAE5] text-[#059669] text-xs font-semibold"><?= icon('check-circle', 'w-3.5 h-3.5') ?>Terverifikasi</span>
                                        </div>
                                        <?php if ($pm['jarak_km'] !== null): ?>
                                            <p class="text-[11px] lg:text-[13px] text-slate-500 flex items-center gap-1 mt-0.5 lg:mt-1"><?= icon('map-pin', 'w-3 h-3 lg:w-4 lg:h-4') ?><?= e(format_jarak($pm['jarak_km'])) ?> dari kamu</p>
                                        <?php endif; ?>
                                        <?php if ($pm['pesan']): ?>
                                            <p class="text-[11px] lg:text-sm text-slate-700 mt-1.5 lg:mt-3 lg:bg-white lg:border lg:border-slate-200 lg:rounded-2xl lg:px-4 lg:py-3 leading-snug"><?= e($pm['pesan']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-3 lg:gap-6 mt-4 lg:mt-5 px-1 lg:px-0">
                                <button type="button" data-aksi="tolak" class="aksi-btn flex-1 flex items-center justify-center gap-2 py-2.5 lg:py-3.5 rounded-lg lg:rounded-2xl bg-[#FECACA] text-[#B91C1C] text-sm lg:text-base font-medium lg:font-semibold hover:bg-[#FCA5A5] active:scale-95 transition disabled:opacity-60">
                                    <?= icon('x', 'w-4 h-4 lg:w-5 lg:h-5') ?> Tolak
                                </button>
                                <button type="button" data-aksi="terima" class="aksi-btn flex-1 flex items-center justify-center gap-2 py-2.5 lg:py-3.5 rounded-lg lg:rounded-2xl bg-[#059669] text-white text-sm lg:text-base font-medium lg:font-semibold hover:bg-[#047857] active:scale-95 transition disabled:opacity-60">
                                    <?= icon('check', 'w-4 h-4 lg:w-5 lg:h-5') ?> Terima
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="border-t border-slate-300 lg:border-slate-200 pt-3 lg:pt-5 mt-1 text-center">
                        <a href="atur-jadwal.php?id=<?= (int)$donasi['id'] ?>" class="inline-flex items-center gap-2 text-xs lg:text-sm font-medium lg:font-semibold text-[#2563EB] hover:underline">
                            <span class="hidden lg:inline"><?= icon('calendar', 'w-[18px] h-[18px]') ?></span>Atur Jadwal Jemput
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="lg:hidden mt-5"><?php include __DIR__ . '/includes/banner.php'; ?></div>
    </section>
</div>

<?php
$skripHalaman = '<script>
(function () {
    var donasiId = ' . (int)$donasiId . ';
    var csrf = ' . json_encode(csrf_token()) . ';
    var toast = document.getElementById("toast");
    document.querySelectorAll(".aksi-btn").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var item = btn.closest(".request-item");
            var aksi = btn.dataset.aksi;
            item.querySelectorAll(".aksi-btn").forEach(function (b) { b.disabled = true; });
            var data = new URLSearchParams({ aksi: aksi, donasi_id: donasiId, request_id: item.dataset.requestId, csrf: csrf });
            fetch("status-verifikasi.php", { method: "POST", body: data })
                .then(function (r) { return r.json(); })
                .then(function (h) {
                    if (!h.ok) { alert("Aksi gagal. Muat ulang halaman lalu coba lagi."); item.querySelectorAll(".aksi-btn").forEach(function (b) { b.disabled = false; }); return; }
                    if (aksi === "tolak") {
                        toast.classList.remove("-translate-y-24", "opacity-0");
                        setTimeout(function () { location.href = h.redirect; }, 1600);
                    } else { location.href = h.redirect; }
                })
                .catch(function () { alert("Koneksi bermasalah. Coba lagi."); item.querySelectorAll(".aksi-btn").forEach(function (b) { b.disabled = false; }); });
        });
    });
})();
</script>';
include __DIR__ . '/includes/footer.php';

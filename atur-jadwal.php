<?php
session_start();
require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

$userId   = current_user_id();
$donasiId = (int)($_GET['id'] ?? 0);
$donasi   = null; $penerima = null; $dbError = null;

if ($pdo && $userId && $donasiId) {
    try {
        $stmt = $pdo->prepare('SELECT id, nama_barang, jumlah, foto, status, created_at FROM donations WHERE id = ? AND user_id = ?');
        $stmt->execute([$donasiId, $userId]);
        $donasi = $stmt->fetch() ?: null;

        if ($donasi) {
            $stmt = $pdo->prepare(
                "SELECT r.jarak_km, u.id AS id_user, u.nama
                   FROM requests r JOIN users u ON u.id = r.requester_id
                  WHERE r.donation_id = ? AND r.status = 'diterima' ORDER BY r.created_at DESC LIMIT 1"
            );
            $stmt->execute([$donasiId]);
            $penerima = $stmt->fetch() ?: null;
        }
    } catch (Throwable $ex) { $dbError = $ex->getMessage(); }
}
$daftarSamping = ambil_donasi_user($pdo, $userId);
$idAktif = $donasiId;

$judulHalaman = 'Status & Riwayat';
$subjudul     = 'Aktivitas  ›  Donasi Saya  ›  ' . ($donasi['nama_barang'] ?? 'Atur Jadwal');
$kembaliHref  = 'status-donasi.php';
$navAktif     = 'aktivitas';
include __DIR__ . '/includes/header.php';
?>

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
                        <div class="mt-1.5 lg:mt-2.5"><?= badge_status('atur_jadwal') ?></div>
                    </div>
                </div>

                <!-- Stepper (desktop) -->
                <div class="hidden lg:grid grid-cols-3 px-7 pt-8 text-center text-[13px]">
                    <div class="relative">
                        <span class="absolute top-4 left-1/2 w-full h-[3px] rounded bg-[#059669]"></span>
                        <span class="relative z-10 mx-auto w-8 h-8 rounded-full bg-[#059669] text-white flex items-center justify-center"><?= icon('check', 'w-4 h-4') ?></span>
                        <p class="mt-2.5 font-semibold text-[#059669]">Permintaan diterima</p>
                    </div>
                    <div class="relative">
                        <span class="absolute top-4 left-1/2 w-full h-[3px] rounded bg-slate-300"></span>
                        <span class="relative z-10 mx-auto w-8 h-8 rounded-full bg-[#2563EB] ring-4 ring-blue-100 text-white font-bold text-sm flex items-center justify-center">2</span>
                        <p class="mt-2.5 font-bold text-[#2563EB]">Atur jadwal</p>
                    </div>
                    <div class="relative">
                        <span class="relative z-10 mx-auto w-8 h-8 rounded-full bg-white border-2 border-slate-300 text-slate-400 font-bold text-sm flex items-center justify-center">3</span>
                        <p class="mt-2.5 font-medium text-slate-400">Penjemputan</p>
                    </div>
                </div>

                <div class="p-3.5 lg:p-7">
                    <!-- Detail penjemputan (desktop) -->
                    <div class="hidden lg:block mb-6">
                        <h3 class="font-bold text-base mb-4">Detail Penjemputan</h3>
                        <div class="grid grid-cols-[296px_1fr] gap-6">
                            <div id="petaJemput" class="h-[232px] rounded-[18px] bg-gradient-to-br from-[#E0EAFB] to-[#D1E3F8] border border-blue-200 flex flex-col items-center justify-center text-center">
                                <span class="w-14 h-14 rounded-full bg-white/85 text-[#2563EB] flex items-center justify-center"><?= icon('map-pin', 'w-7 h-7') ?></span>
                                <p class="mt-3 text-[13px] font-semibold text-blue-900">Peta Google Maps</p>
                                <p class="text-xs text-slate-500">akan tampil di sini</p>
                            </div>
                            <dl class="divide-y divide-slate-100">
                                <div class="pb-4">
                                    <dt class="text-xs font-medium text-slate-400">Penerima</dt>
                                    <dd class="mt-2 flex items-center gap-2.5 font-bold text-[15px]">
                                        <?php if ($penerima): ?>
                                            <span class="w-7 h-7 rounded-full text-[11px] flex items-center justify-center <?= warna_avatar((int)$penerima['id_user']) ?>"><?= e(inisial($penerima['nama'])) ?></span><?= e($penerima['nama']) ?>
                                        <?php else: ?><span class="text-slate-400 font-medium">Belum ada penerima</span><?php endif; ?>
                                    </dd>
                                </div>
                                <div class="py-4">
                                    <dt class="text-xs font-medium text-slate-400">Jarak</dt>
                                    <dd class="mt-2 font-bold text-[15px]"><?= $penerima && $penerima['jarak_km'] !== null ? e(format_jarak($penerima['jarak_km'])) . ' dari kamu' : '-' ?></dd>
                                </div>
                                <div class="pt-4">
                                    <dt class="text-xs font-medium text-slate-400">Barang</dt>
                                    <dd class="mt-2 font-bold text-[15px]"><?= e($donasi['nama_barang']) ?> • <?= (int)$donasi['jumlah'] ?> unit</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <button type="button" id="btnJadwal" class="w-full py-3.5 lg:py-4 rounded-xl lg:rounded-2xl bg-[#2563EB] text-white text-sm lg:text-[17px] font-medium lg:font-semibold hover:bg-blue-700 active:scale-[0.98] transition flex items-center justify-center gap-2">
                        <span class="hidden lg:inline"><?= icon('calendar', 'w-[22px] h-[22px]') ?></span>Atur Jadwal Penjemputan
                    </button>
                </div>
            </div>
        <?php endif; ?>
        <div class="lg:hidden mt-5"><?php include __DIR__ . '/includes/banner.php'; ?></div>
    </section>
</div>

<!-- Panel peta (placeholder, nanti diisi Google Maps) -->
<div id="sheetJadwal" class="fixed inset-0 z-50 hidden items-end lg:items-center justify-center">
    <div id="sheetOverlay" class="absolute inset-0 bg-slate-900/40"></div>
    <div class="relative w-full max-w-lg bg-white rounded-t-3xl lg:rounded-3xl p-5 pb-8 lg:p-7">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-bold text-sm lg:text-base">Atur Jadwal Penjemputan</h3>
            <button type="button" id="btnTutup" aria-label="Tutup" class="text-slate-400 hover:text-slate-700"><?= icon('x', 'w-5 h-5') ?></button>
        </div>
        <!-- TODO: inisialisasi Google Maps pada #petaSheet -->
        <div id="petaSheet" class="h-44 lg:h-56 rounded-2xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-xs gap-1.5">
            <?= icon('map-pin', 'w-6 h-6') ?><span>Peta Google Maps akan tampil di sini</span>
        </div>
    </div>
</div>

<?php
$skripHalaman = '<script>
(function () {
    var sheet = document.getElementById("sheetJadwal");
    function buka() { sheet.classList.remove("hidden"); sheet.classList.add("flex"); }
    function tutup() { sheet.classList.add("hidden"); sheet.classList.remove("flex"); }
    var b = document.getElementById("btnJadwal"); if (b) b.addEventListener("click", buka);
    document.getElementById("btnTutup").addEventListener("click", tutup);
    document.getElementById("sheetOverlay").addEventListener("click", tutup);
})();
</script>';
include __DIR__ . '/includes/footer.php';

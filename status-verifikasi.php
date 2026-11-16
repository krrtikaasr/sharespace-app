<?php
session_start();
require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/includes/helpers.php';

$userId     = $_SESSION['user_id'] ?? null;
$donasiId   = (int)($_GET['id'] ?? $_POST['donasi_id'] ?? 0);

/* ---------- Proses tombol Tolak / Terima (dipanggil lewat fetch) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $aksi      = $_POST['aksi'] ?? '';
    $requestId = (int)($_POST['request_id'] ?? 0);
    $ok = false;

    if ($pdo && $userId && in_array($aksi, ['tolak', 'terima'], true)) {
        // Pastikan donasi ini memang milik pengguna yang login
        $cek = $pdo->prepare('SELECT id FROM donations WHERE id = ? AND user_id = ?');
        $cek->execute([$donasiId, $userId]);

        if ($cek->fetch()) {
            $statusBaru = $aksi === 'tolak' ? 'ditolak' : 'diterima';
            $pdo->prepare('UPDATE requests SET status = ? WHERE id = ? AND donation_id = ?')
                ->execute([$statusBaru, $requestId, $donasiId]);

            if ($aksi === 'terima') {
                $pdo->prepare("UPDATE donations SET status = 'atur_jadwal' WHERE id = ?")
                    ->execute([$donasiId]);
            }
            $ok = true;
        }
    }

    echo json_encode([
        'ok'       => $ok,
        'redirect' => $aksi === 'terima' ? 'atur-jadwal.php?id=' . $donasiId : 'status-donasi.php',
    ]);
    exit;
}

/* ---------- Ambil data untuk tampilan ---------- */
$donasi = null;
$daftarPemohon = [];

if ($pdo && $userId && $donasiId) {
    $stmt = $pdo->prepare(
        'SELECT id, nama_barang, jumlah, foto, status, created_at
           FROM donations WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$donasiId, $userId]);
    $donasi = $stmt->fetch() ?: null;

    if ($donasi) {
        $stmt = $pdo->prepare(
            "SELECT r.id, r.pesan, r.jarak_km, u.nama, u.foto
               FROM requests r JOIN users u ON u.id = r.requester_id
              WHERE r.donation_id = ? AND r.status = 'menunggu'
              ORDER BY r.created_at ASC"
        );
        $stmt->execute([$donasiId]);
        $daftarPemohon = $stmt->fetchAll();
    }
}

// ===== DATA DUMMY =====
if (!$donasi) {
    $donasi = [
        'id' => $donasiId ?: 1, 'nama_barang' => 'Lampu Belajar', 'jumlah' => 4,
        'foto' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=300&q=80',
        'status' => 'verified', 'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours')),
    ];
    $daftarPemohon = [[
        'id' => 1, 'nama' => 'Made Wijaya', 'foto' => 'https://i.pravatar.cc/150?img=12',
        'jarak_km' => 0.8, 'pesan' => 'Halo kak, saya otw ke lokasi yaa',
    ]];
}
// ======================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareSpace - Verifikasi Pemohon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white">

<div class="w-full max-w-[430px] mx-auto bg-white min-h-screen flex flex-col relative">

    <!-- Notifikasi pop up (warna sama dengan tombol Tolak) -->
    <div id="toast"
         class="fixed top-3 inset-x-4 mx-auto max-w-[398px] z-50 bg-[#FECACA] text-[#B91C1C] rounded-2xl px-4 py-3 shadow-lg flex items-center gap-2.5 text-xs font-semibold
                -translate-y-24 opacity-0 transition-all duration-300 pointer-events-none"
         role="alert">
        <?= icon('x', 'w-4 h-4 shrink-0') ?>
        <span>Anda menolak permintaan ini!</span>
    </div>

    <div class="px-5 pt-5 pb-28 flex-1">

        <div class="flex items-center gap-3 mb-4">
            <a href="status-donasi.php" aria-label="Kembali"
               class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-slate-50 transition">
                <?= icon('arrow-left', 'w-4 h-4') ?>
            </a>
            <h1 class="font-bold text-base text-slate-900">Status & Riwayat</h1>
        </div>

        <!-- Tab (tetap di "Donasi Saya"; klik tab lain kembali ke daftar) -->
        <div class="relative flex p-1 rounded-full border border-slate-400 mb-5">
            <span class="absolute top-1 bottom-1 left-1 w-[calc(50%-4px)] bg-[#2563EB] rounded-full"></span>
            <a href="status-donasi.php" class="relative z-10 flex-1 py-2.5 rounded-full text-xs font-medium text-white text-center">Donasi Saya</a>
            <a href="status-donasi.php" class="relative z-10 flex-1 py-2.5 rounded-full text-xs font-medium text-slate-900 text-center">Permintaan Saya</a>
        </div>

        <h2 class="font-bold text-xl text-slate-900 mb-3">Status Card</h2>

        <?php if (!$donasi): ?>
            <p class="text-xs text-slate-400 text-center py-10">Data donasi tidak ditemukan.</p>
        <?php else: ?>
        <div class="border border-slate-300 rounded-[20px] bg-white overflow-hidden">

            <!-- Info barang -->
            <div class="p-3 flex items-center gap-3.5 border-b border-slate-300">
                <?= foto_atau_placeholder($donasi['foto'], $donasi['nama_barang'], 'w-[62px] h-[62px] rounded-xl shrink-0') ?>
                <div class="min-w-0">
                    <h3 class="font-bold text-sm text-slate-900 truncate"><?= e($donasi['nama_barang']) ?></h3>
                    <p class="text-[11px] text-slate-400 mt-0.5"><?= (int)$donasi['jumlah'] ?> unit • <?= e(waktu_relatif($donasi['created_at'])) ?></p>
                    <div class="mt-1.5"><?= badge_status($donasi['status']) ?></div>
                </div>
            </div>

            <!-- Verifikasi pemohon -->
            <div class="p-3.5">
                <h4 class="font-semibold text-xs text-slate-900 mb-2.5">Verifikasi Pemohon</h4>

                <?php if (!$daftarPemohon): ?>
                    <p class="text-[11px] text-slate-400 text-center py-5">Belum ada permintaan untuk barang ini.</p>
                <?php endif; ?>

                <?php foreach ($daftarPemohon as $pm): ?>
                    <div class="request-item mb-4" data-request-id="<?= (int)$pm['id'] ?>">
                        <div class="border border-slate-400 rounded-2xl p-3 flex gap-3">
                            <div class="relative shrink-0">
                                <?= foto_atau_placeholder($pm['foto'], $pm['nama'], 'w-11 h-11 rounded-full') ?>
                                <span class="absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full bg-[#2563EB] text-white flex items-center justify-center ring-2 ring-white">
                                    <?= icon('check', 'w-2.5 h-2.5') ?>
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-xs text-slate-900"><?= e($pm['nama']) ?></p>
                                <?php if ($pm['jarak_km'] !== null): ?>
                                    <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                        <?= icon('map-pin', 'w-3 h-3') ?><?= e(number_format((float)$pm['jarak_km'], 1, ',', '.')) ?> km dari kamu
                                    </p>
                                <?php endif; ?>
                                <p class="text-[11px] text-slate-700 mt-1.5 leading-snug"><?= e($pm['pesan']) ?></p>
                            </div>
                        </div>

                        <div class="flex gap-3 mt-4 px-1">
                            <button type="button" data-aksi="tolak"
                                    class="aksi-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg bg-[#FECACA] text-[#B91C1C] text-sm font-medium hover:bg-[#FCA5A5] active:scale-95 transition">
                                <?= icon('x', 'w-4 h-4') ?> Tolak
                            </button>
                            <button type="button" data-aksi="terima"
                                    class="aksi-btn flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg bg-[#059669] text-white text-sm font-medium hover:bg-[#047857] active:scale-95 transition">
                                <?= icon('check', 'w-4 h-4') ?> Terima
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="border-t border-slate-300 pt-3 mt-1 text-center">
                    <a href="atur-jadwal.php?id=<?= (int)$donasi['id'] ?>" class="text-xs text-[#2563EB] hover:underline">Atur Jadwal Jemput</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/includes/navbar.php'; ?>
</div>

<script>
    const donasiId = <?= (int)$donasiId ?>;
    const toast = document.getElementById('toast');

    function tampilToast() {
        toast.classList.remove('-translate-y-24', 'opacity-0');
    }

    document.querySelectorAll('.aksi-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const item = btn.closest('.request-item');
            const aksi = btn.dataset.aksi;
            item.querySelectorAll('.aksi-btn').forEach(b => b.disabled = true);

            const data = new URLSearchParams({
                aksi, donasi_id: donasiId, request_id: item.dataset.requestId
            });

            let hasil = { redirect: aksi === 'terima' ? 'atur-jadwal.php?id=' + donasiId : 'status-donasi.php' };
            try {
                const res = await fetch('status-verifikasi.php', { method: 'POST', body: data });
                hasil = await res.json();
            } catch (err) { /* tetap lanjut agar alur tampilan bisa dicoba tanpa server database */ }

            if (aksi === 'tolak') {
                tampilToast();
                setTimeout(() => location.href = hasil.redirect, 1600);
            } else {
                location.href = hasil.redirect;
            }
        });
    });
</script>
</body>
</html>

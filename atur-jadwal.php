<?php
session_start();
require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/includes/helpers.php';

$userId   = $_SESSION['user_id'] ?? null;
$donasiId = (int)($_GET['id'] ?? 0);
$donasi   = null;

if ($pdo && $userId && $donasiId) {
    $stmt = $pdo->prepare(
        'SELECT id, nama_barang, jumlah, foto, status, created_at
           FROM donations WHERE id = ? AND user_id = ?'
    );
    $stmt->execute([$donasiId, $userId]);
    $donasi = $stmt->fetch() ?: null;
}

// ===== DATA DUMMY =====
if (!$donasi) {
    $donasi = [
        'id' => $donasiId ?: 1, 'nama_barang' => 'Lampu Belajar', 'jumlah' => 3,
        'foto' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=300&q=80',
        'status' => 'atur_jadwal', 'created_at' => date('Y-m-d H:i:s', strtotime('-4 hours')),
    ];
}
// ======================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareSpace - Atur Jadwal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white">

<div class="w-full max-w-[430px] mx-auto bg-white min-h-screen flex flex-col relative">

    <div class="px-5 pt-5 pb-28 flex-1">

        <div class="flex items-center gap-3 mb-4">
            <a href="status-donasi.php" aria-label="Kembali"
               class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-slate-50 transition">
                <?= icon('arrow-left', 'w-4 h-4') ?>
            </a>
            <h1 class="font-bold text-base text-slate-900">Status & Riwayat</h1>
        </div>

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
            <div class="p-3 flex items-center gap-3.5 border-b border-slate-300">
                <?= foto_atau_placeholder($donasi['foto'], $donasi['nama_barang'], 'w-[62px] h-[62px] rounded-xl shrink-0') ?>
                <div class="min-w-0">
                    <h3 class="font-bold text-sm text-slate-900 truncate"><?= e($donasi['nama_barang']) ?></h3>
                    <p class="text-[11px] text-slate-400 mt-0.5"><?= (int)$donasi['jumlah'] ?> unit • <?= e(waktu_relatif($donasi['created_at'])) ?></p>
                    <div class="mt-1.5"><?= badge_status('atur_jadwal') ?></div>
                </div>
            </div>

            <div class="p-3.5">
                <button type="button" id="btnJadwal"
                        class="w-full py-3.5 rounded-xl bg-[#2563EB] text-white text-sm font-medium hover:bg-blue-700 active:scale-[0.98] transition">
                    Atur Jadwal Penjemputan
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/includes/navbar.php'; ?>

    <!-- Placeholder integrasi Google Maps (diisi di tahap berikutnya) -->
    <div id="sheetJadwal" class="fixed inset-0 z-40 hidden">
        <div id="sheetOverlay" class="absolute inset-0 bg-slate-900/40"></div>
        <div class="absolute bottom-0 inset-x-0 mx-auto max-w-[430px] bg-white rounded-t-3xl p-5 pb-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-sm text-slate-900">Atur Jadwal Penjemputan</h3>
                <button type="button" id="btnTutup" aria-label="Tutup" class="text-slate-400 hover:text-slate-700"><?= icon('x', 'w-5 h-5') ?></button>
            </div>
            <!-- TODO: inisialisasi Google Maps pada #petaJemput -->
            <div id="petaJemput" class="h-44 rounded-2xl bg-slate-100 border border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-400 text-xs gap-1.5">
                <?= icon('map-pin', 'w-6 h-6') ?>
                <span>Peta Google Maps akan tampil di sini</span>
            </div>
        </div>
    </div>
</div>

<script>
    const sheet = document.getElementById('sheetJadwal');
    const buka  = () => sheet.classList.remove('hidden');
    const tutup = () => sheet.classList.add('hidden');
    document.getElementById('btnJadwal')?.addEventListener('click', buka);
    document.getElementById('btnTutup').addEventListener('click', tutup);
    document.getElementById('sheetOverlay').addEventListener('click', tutup);
</script>
</body>
</html>

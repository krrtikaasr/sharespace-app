<?php
session_start();
require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';

$userId   = current_user_id();
$donasiId = (int)($_GET['id'] ?? 0);
$barang   = null; $jumlahRequest = 0; $dbError = null;

if ($pdo && $userId && $donasiId) {
    try {
        try {
            $stmt = $pdo->prepare('SELECT id, nama_barang, jumlah, foto, status, created_at, kategori, kondisi, lokasi, deskripsi FROM donations WHERE id = ? AND user_id = ?');
            $stmt->execute([$donasiId, $userId]);
        } catch (Throwable $ex) { // kolom tambahan belum dibuat -> pakai kolom dasar
            $stmt = $pdo->prepare('SELECT id, nama_barang, jumlah, foto, status, created_at FROM donations WHERE id = ? AND user_id = ?');
            $stmt->execute([$donasiId, $userId]);
        }
        $barang = $stmt->fetch() ?: null;

        if ($barang) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM requests WHERE donation_id = ? AND status = 'menunggu'");
            $stmt->execute([$donasiId]);
            $jumlahRequest = (int)$stmt->fetchColumn();
        }
    } catch (Throwable $ex) { $dbError = $ex->getMessage(); }
}
$daftarSamping = ambil_donasi_user($pdo, $userId);
$idAktif = $donasiId;

$info = $barang ? [
    ['Kategori',  $barang['kategori'] ?? null, 'tag'],
    ['Kondisi',   $barang['kondisi'] ?? null,  'shield-check'],
    ['Lokasi',    $barang['lokasi'] ?? null,   'map-pin'],
    ['Diposting', waktu_relatif($barang['created_at']), 'calendar'],
] : [];

$judulHalaman = 'Detail Barang';
$subjudul     = 'Aktivitas  ›  Donasi Saya  ›  ' . ($barang['nama_barang'] ?? 'Detail');
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
        <?php if (!$barang): ?>
            <p class="text-xs text-slate-400 text-center py-16">Data barang tidak ditemukan.</p>
        <?php else: ?>
            <div class="lg:bg-white lg:border lg:border-slate-200 lg:rounded-3xl lg:p-7">
                <?= foto_atau_placeholder($barang['foto'], $barang['nama_barang'], 'w-full h-56 lg:h-[320px] rounded-3xl') ?>

                <div class="mt-4 flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 class="font-bold lg:font-extrabold text-xl lg:text-[28px]"><?= e($barang['nama_barang']) ?></h2>
                        <div class="mt-1.5 lg:mt-2.5"><?= badge_status($barang['status']) ?></div>
                    </div>
                    <span class="shrink-0 inline-flex items-center gap-1.5 bg-blue-50 text-[#2563EB] rounded-full px-3 py-1.5 text-xs lg:text-sm font-semibold"><?= icon('package', 'w-4 h-4') ?><?= (int)$barang['jumlah'] ?> unit</span>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 lg:gap-4 mt-5">
                    <?php foreach ($info as [$label, $nilai, $ikon]): if ($nilai === null || $nilai === '') continue; ?>
                        <div class="border border-slate-200 rounded-2xl p-3 lg:p-4 bg-white">
                            <div class="flex items-center gap-1.5 text-[11px] lg:text-xs text-slate-400"><?= icon($ikon, 'w-3.5 h-3.5') ?><?= e($label) ?></div>
                            <p class="text-xs lg:text-sm font-semibold mt-1 leading-snug"><?= e($nilai) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($barang['deskripsi'])): ?>
                    <h3 class="font-bold text-sm lg:text-base mt-6 mb-1.5">Deskripsi</h3>
                    <p class="text-xs lg:text-sm text-slate-600 leading-relaxed"><?= nl2br(e($barang['deskripsi'])) ?></p>
                <?php endif; ?>

                <div class="mt-6 border border-slate-200 rounded-2xl p-3.5 lg:p-5 flex items-center justify-between gap-3 bg-white">
                    <div>
                        <p class="text-xs lg:text-sm font-semibold">Permintaan masuk</p>
                        <p class="text-[11px] lg:text-[13px] text-slate-400 mt-0.5"><?= $jumlahRequest > 0 ? $jumlahRequest . ' orang ingin barang ini' : 'Belum ada yang meminta barang ini' ?></p>
                    </div>
                    <?php if ($jumlahRequest > 0): ?>
                        <a href="status-verifikasi.php?id=<?= (int)$barang['id'] ?>" class="px-3.5 lg:px-5 py-2 lg:py-2.5 rounded-lg lg:rounded-xl bg-[#2563EB] text-white text-xs lg:text-sm font-medium hover:bg-blue-700 active:scale-95 transition">Lihat</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php';

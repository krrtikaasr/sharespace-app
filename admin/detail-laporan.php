<?php
/* ============================================================
   detail-laporan.php  -  rincian satu laporan donasi (admin)
   Mode preview / database diatur di admin-layout.php
   ============================================================ */
require_once 'admin-layout.php';
require_once 'laporan-fungsi.php';

$id = (int) ($_GET['id'] ?? 0);
$r  = null;

if (MODE_PREVIEW) {
    foreach (data_contoh_laporan() as $p) {
        if ((int) $p['id'] === $id) { $r = $p; break; }
    }
} else {
    // Sesuaikan nama kolom/tabel dengan database Anda bila berbeda
    $r = ambil_satu(
        $conn,
        "SELECT p.id, i.nama_barang AS nama, i.kategori, i.deskripsi,
                COALESCE(i.lokasi, ud.lokasi) AS lokasi,
                p.created_at,
                COALESCE(p.status, 'menunggu') AS status,
                ud.nama AS donatur,
                up.nama AS peminta, up.email AS peminta_email
         FROM permintaan p
         JOIN items i       ON i.id = p.id_item
         LEFT JOIN users ud ON ud.id = i.user_id
         LEFT JOIN users up ON up.id = p.id_penerima
         WHERE p.id = ?",
        'i', $id
    );
}

if (!$r) {
    header('Location: kelola-laporan.php');
    exit;
}

$b = $badge_laporan[$r['status']] ?? $badge_laporan['menunggu'];

$rincian = [
    ['Nama Barang',   $r['nama']],
    ['Kategori',      $r['kategori'] ?? '-'],
    ['Tanggal Ajuan', tgl_panjang($r['created_at'])],
    ['Donatur',       $r['donatur'] ?? '-'],
    ['Penerima',      $r['peminta'] ?? '-'],
    ['Email Penerima', $r['peminta_email'] ?? '-'],
];

layout_awal('Detail Laporan', 'laporan');
?>

            <a href="kelola-laporan.php" class="inline-flex items-center gap-1 text-xs font-semibold text-slate-500 hover:text-blue-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                Kembali
            </a>

            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900 mt-3">Detail Laporan #<?= e(kode_laporan($r['id'])); ?></h1>
            <p class="text-sm text-slate-500 mt-2">Rincian transaksi donasi.</p>

            <div class="bg-white rounded-2xl border border-slate-200 mt-6 p-5 lg:p-6 max-w-4xl">
                <div class="flex items-center justify-between gap-3 pb-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-800">Riwayat Transaksi Donasi</h2>
                    <span class="inline-block whitespace-nowrap px-3 py-1 rounded-full text-xs font-semibold <?= $b[1]; ?>"><?= e($b[0]); ?></span>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-5 py-5">
                    <?php foreach ($rincian as $row): ?>
                        <div>
                            <dt class="text-xs text-slate-400"><?= e($row[0]); ?></dt>
                            <dd class="text-sm font-semibold text-slate-800 mt-0.5 break-words"><?= e($row[1]); ?></dd>
                        </div>
                    <?php endforeach; ?>
                    <div class="sm:col-span-3 pt-4 border-t border-slate-100">
                        <dt class="text-xs text-slate-400">Lokasi</dt>
                        <dd class="text-sm font-semibold text-slate-800 mt-0.5"><?= e($r['lokasi'] ?? '-'); ?></dd>
                    </div>
                </dl>

                <div class="bg-slate-50 rounded-xl px-4 py-3 text-sm text-slate-600">
                    <span class="font-semibold">Deskripsi:</span> <?= e($r['deskripsi'] ?? '-'); ?>
                </div>
            </div>

            <div class="mt-5">
                <button type="button" onclick="window.print()"
                        class="px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Unduh Laporan PDF</button>
                <p class="text-xs text-slate-400 mt-2">Pilih &ldquo;Simpan sebagai PDF&rdquo; pada jendela cetak.</p>
            </div>

<?php layout_akhir(); ?>
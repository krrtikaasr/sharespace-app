<?php
/* ============================================================
   data-donasi.php  -  detail satu donasi (admin)
   Dibuka dari tombol "Lihat" di kelola-donasi.php?id=...
   ============================================================ */
require_once 'admin-layout.php';

$id            = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$konfirmasi    = isset($_GET['konfirmasi']);
$url_detail    = 'data-donasi.php?id=' . $id;

/* ---------- Proses hapus donasi ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus' && $id > 0) {
    if (!MODE_PREVIEW && !hash_equals($csrf, $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die("Permintaan tidak valid.");
    }
    if (!MODE_PREVIEW) {
        $stmt = mysqli_prepare($conn, "DELETE FROM items WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: kelola-donasi.php?ok=hapus");
    exit;
}

/* ---------- Ambil data donasi ---------- */
$d = null;
if ($id > 0) {
    if (MODE_PREVIEW) {
        foreach (data_contoh_donasi() as $row) {
            if ((int) $row['id'] === $id) {
                $d = $row;
                break;
            }
        }
    } else {
        // Sesuaikan nama kolom dengan tabel items milik Anda bila berbeda
        $d = ambil_satu(
            $conn,
            "SELECT i.id, i.nama_barang AS nama, i.kategori, i.deskripsi, i.foto,
                    COALESCE(i.lokasi, u.lokasi) AS lokasi,
                    i.created_at,
                    COALESCE(i.status, 'tersedia') AS status,
                    u.nama AS donatur, u.email AS donatur_email
             FROM items i
             LEFT JOIN users u ON u.id = i.user_id
             WHERE i.id = ?",
            "i", $id
        );
    }
}

if (!$d) {
    http_response_code(404);
}

layout_awal('Donasi', 'donasi');
?>

            <a href="kelola-donasi.php" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Kembali ke Kelola Donasi
            </a>

            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900 mt-4">Donasi</h1>

            <?php if (!$d): ?>
                <div class="mt-6 bg-white rounded-2xl border border-slate-200 px-6 py-10 text-center text-sm text-slate-500">
                    Data donasi tidak ditemukan. Mungkin sudah dihapus.
                </div>
            <?php else:
                $habis = ($d['status'] === 'habis');
            ?>
                <!-- Ringkasan barang -->
                <div class="flex flex-wrap items-center gap-6 sm:gap-8 mt-6">
                    <?php if (!empty($d['foto'])): ?>
                        <img src="<?= e(FOLDER_FOTO . $d['foto']); ?>" alt="Foto <?= e($d['nama']); ?>"
                             class="w-44 h-44 sm:w-52 sm:h-52 rounded-2xl object-cover shrink-0 bg-slate-200">
                    <?php else: ?>
                        <div class="w-44 h-44 sm:w-52 sm:h-52 rounded-2xl bg-blue-600 flex items-center justify-center shrink-0" role="img" aria-label="Foto belum tersedia">
                            <svg class="w-14 h-14 text-white/80" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="1.8"/><path d="M21 16l-5-5-9 9"/></svg>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-semibold text-slate-800"><?= e($d['nama']); ?></h2>
                        <p class="text-slate-500 mt-1"><?= e($d['kategori']); ?></p>
                    </div>
                </div>

                <!-- Rincian -->
                <dl class="bg-white rounded-2xl border border-slate-200 mt-7 divide-y divide-slate-100 shadow-sm">
                    <div class="grid grid-cols-[8rem_1fr] sm:grid-cols-[11rem_1fr] gap-4 px-6 py-4 text-sm">
                        <dt class="font-semibold text-slate-700">Donatur</dt>
                        <dd class="text-slate-600">: <?= e($d['donatur'] ?? '-'); ?><?php if (!empty($d['donatur_email'])): ?> <span class="text-slate-400">(<?= e($d['donatur_email']); ?>)</span><?php endif; ?></dd>
                    </div>
                    <div class="grid grid-cols-[8rem_1fr] sm:grid-cols-[11rem_1fr] gap-4 px-6 py-4 text-sm">
                        <dt class="font-semibold text-slate-700">Diunggah pada</dt>
                        <dd class="text-slate-600">: <?= e(tgl_panjang($d['created_at'])); ?></dd>
                    </div>
                    <div class="grid grid-cols-[8rem_1fr] sm:grid-cols-[11rem_1fr] gap-4 px-6 py-4 text-sm">
                        <dt class="font-semibold text-slate-700">Lokasi</dt>
                        <dd class="text-slate-600">: <?= e($d['lokasi'] ?? '-'); ?></dd>
                    </div>
                    <div class="grid grid-cols-[8rem_1fr] sm:grid-cols-[11rem_1fr] gap-4 px-6 py-4 text-sm">
                        <dt class="font-semibold text-slate-700">Status</dt>
                        <dd class="text-slate-600">:
                            <?php if ($habis): ?>
                                <span class="inline-block px-3 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">Habis</span>
                            <?php else: ?>
                                <span class="inline-block px-3 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-600">Tersedia</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    <div class="grid grid-cols-[8rem_1fr] sm:grid-cols-[11rem_1fr] gap-4 px-6 py-4 text-sm">
                        <dt class="font-semibold text-slate-700">Deskripsi</dt>
                        <dd class="text-slate-600">: <?= e($d['deskripsi'] ?? '-'); ?></dd>
                    </div>
                </dl>

                <!-- Tombol hapus -->
                <div class="flex justify-end mt-8">
                    <?php if ($konfirmasi): ?>
                        <span class="px-8 py-2 rounded-lg border border-red-400 bg-red-200 text-sm font-semibold text-red-700">Hapus</span>
                    <?php else: ?>
                        <a href="<?= e($url_detail . '&konfirmasi=1'); ?>"
                           class="px-8 py-2 rounded-lg border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-red-500 hover:border-red-500 hover:text-white">Hapus</a>
                    <?php endif; ?>
                </div>

                <!-- Konfirmasi hapus -->
                <?php if ($konfirmasi): ?>
                    <form method="POST" action="<?= e($url_detail); ?>"
                          class="flex items-center justify-between gap-4 mt-4 px-5 py-3 border bg-red-200 border-red-400 text-red-950">
                        <input type="hidden" name="id" value="<?= (int) $d['id']; ?>">
                        <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                        <p class="text-sm font-semibold">
                            Anda yakin akan menghapus data donasi <?= e($d['nama']); ?> oleh <?= e($d['donatur'] ?? '-'); ?> (<?= e($d['donatur_email'] ?? '-'); ?>)?
                        </p>
                        <div class="flex items-center gap-4 shrink-0">
                            <button type="submit" name="aksi" value="hapus" title="Ya, hapus" aria-label="Ya, hapus" class="text-slate-800 hover:text-green-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <a href="<?= e($url_detail); ?>" title="Batal" aria-label="Batal" class="text-slate-800 hover:text-red-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

<?php layout_akhir(); ?>
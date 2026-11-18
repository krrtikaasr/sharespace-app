<?php
/* ============================================================
   kelola-donasi.php  -  daftar donasi (admin)
   Mode preview / database diatur di admin-layout.php
   ============================================================ */
require_once 'admin-layout.php';

function url_daftar($filter, $halaman, $tambah = []) {
    return 'kelola-donasi.php?' . http_build_query(array_merge(['status' => $filter, 'hal' => $halaman], $tambah));
}

/* ---------- Parameter halaman ---------- */
$filter = $_GET['status'] ?? 'semua';
if (!in_array($filter, ['semua', 'tersedia', 'habis'], true)) {
    $filter = 'semua';
}
$halaman_ini   = max(1, (int) ($_GET['hal'] ?? 1));
$konfirmasi_id = (int) ($_GET['konfirmasi'] ?? 0);
$ok            = $_GET['ok'] ?? '';

/* ---------- Proses hapus donasi ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    $id = (int) ($_POST['id'] ?? 0);

    if (!MODE_PREVIEW && !hash_equals($csrf, $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die("Permintaan tidak valid.");
    }

    if ($id > 0) {
        if (!MODE_PREVIEW) {
            $stmt = mysqli_prepare($conn, "DELETE FROM items WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        header("Location: " . url_daftar($filter, $halaman_ini, ['ok' => 'hapus']));
        exit;
    }
}

/* ---------- Ambil data donasi ---------- */
if (MODE_PREVIEW) {
    $semua = data_contoh_donasi();
    if ($filter !== 'semua') {
        $semua = array_values(array_filter($semua, function ($d) use ($filter) {
            return $d['status'] === $filter;
        }));
    }
    $total = count($semua);

    $total_halaman = max(1, (int) ceil($total / PER_HALAMAN));
    $halaman_ini   = min($halaman_ini, $total_halaman);
    $offset        = ($halaman_ini - 1) * PER_HALAMAN;

    $donasi = array_slice($semua, $offset, PER_HALAMAN);
} else {
    $where = "1=1";
    $types = '';
    $param = [];
    if ($filter !== 'semua') {
        $where .= " AND COALESCE(i.status, 'tersedia') = ?";
        $types .= 's';
        $param[] = $filter;
    }

    $total = (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM items i WHERE $where", $types, ...$param)['n'];

    $total_halaman = max(1, (int) ceil($total / PER_HALAMAN));
    $halaman_ini   = min($halaman_ini, $total_halaman);
    $offset        = ($halaman_ini - 1) * PER_HALAMAN;
    $batas         = PER_HALAMAN;

    // Sesuaikan nama kolom dengan tabel items milik Anda bila berbeda
    $donasi = ambil_semua(
        $conn,
        "SELECT i.id, i.nama_barang AS nama, i.kategori,
                COALESCE(i.lokasi, u.lokasi) AS lokasi,
                i.created_at,
                COALESCE(i.status, 'tersedia') AS status,
                u.nama AS donatur, u.email AS donatur_email
         FROM items i
         LEFT JOIN users u ON u.id = i.user_id
         WHERE $where
         ORDER BY i.created_at DESC, i.id DESC
         LIMIT ? OFFSET ?",
        $types . 'ii', ...array_merge($param, [$batas, $offset])
    );
}

/* ---------- Tampilan ---------- */
$pil = [
    'semua'    => ['Semua',    'border-blue-500 bg-blue-50 text-blue-600'],
    'tersedia' => ['Tersedia', 'border-green-500 bg-green-100 text-green-700'],
    'habis'    => ['Habis',    'border-red-500 bg-red-100 text-red-600'],
];
$pil_biasa = 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50';

layout_awal('Kelola Donasi', 'donasi');
?>

            <!-- Judul -->
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">Kelola Donasi</h1>
            <p class="text-sm text-slate-500 mt-2">Atur data donasi disini.</p>

            <?php if ($ok === 'hapus'): ?>
                <div class="mt-5 bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3">
                    Data donasi berhasil dihapus.<?= MODE_PREVIEW ? ' (mode preview, perubahan tidak disimpan)' : ''; ?>
                </div>
            <?php endif; ?>

            <!-- Filter status -->
            <div class="flex flex-wrap gap-3 mt-6">
                <?php foreach ($pil as $kunci => $info): ?>
                    <a href="<?= e(url_daftar($kunci, 1)); ?>"
                       class="px-6 py-1.5 rounded-full border text-sm font-medium <?= $filter === $kunci ? $info[1] : $pil_biasa; ?>">
                        <?= e($info[0]); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Tabel donasi -->
            <div class="bg-white rounded-2xl border border-slate-200 mt-6 overflow-x-auto">
                <table class="w-full text-left min-w-[820px]">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500">
                            <th class="px-5 py-4">Nama Barang</th>
                            <th class="px-3 py-4">Kategori</th>
                            <th class="px-3 py-4">Donatur</th>
                            <th class="px-3 py-4">Lokasi</th>
                            <th class="px-3 py-4">Tanggal Unggah</th>
                            <th class="px-3 py-4">Status</th>
                            <th class="px-3 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($donasi) === 0): ?>
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-400 border-t border-slate-100">Tidak ada donasi untuk filter ini.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($donasi as $d):
                            $habis             = ($d['status'] === 'habis');
                            $sedang_konfirmasi = ($konfirmasi_id === (int) $d['id']);
                        ?>
                            <tr id="d<?= (int) $d['id']; ?>" class="border-t border-slate-100">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-full border border-slate-300 text-[10px] font-semibold text-slate-500 flex items-center justify-center shrink-0">
                                            <?= e(inisial($d['nama'])); ?>
                                        </span>
                                        <p class="text-sm font-bold text-slate-800"><?= e($d['nama']); ?></p>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-sm text-slate-700"><?= e($d['kategori']); ?></td>
                                <td class="px-3 py-3 text-sm text-slate-700"><?= e($d['donatur'] ?? '-'); ?></td>
                                <td class="px-3 py-3 text-sm text-slate-700"><?= e($d['lokasi'] ?? '-'); ?></td>
                                <td class="px-3 py-3 text-sm text-slate-700 whitespace-nowrap"><?= e(tgl_panjang($d['created_at'])); ?></td>
                                <td class="px-3 py-3">
                                    <?php if ($habis): ?>
                                        <span class="inline-block whitespace-nowrap px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-600">Habis</span>
                                    <?php else: ?>
                                        <span class="inline-block whitespace-nowrap px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-600">Tersedia</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="data-donasi.php?id=<?= (int) $d['id']; ?>"
                                           class="px-3 py-1 rounded-md border border-slate-300 text-xs text-slate-500 hover:bg-green-500 hover:border-green-500 hover:text-white">Lihat</a>
                                        <a href="<?= e(url_daftar($filter, $halaman_ini, ['konfirmasi' => $d['id']]) . '#d' . (int) $d['id']); ?>"
                                           class="px-3 py-1 rounded-md border text-xs hover:bg-red-500 hover:border-red-500 hover:text-white <?= $sedang_konfirmasi ? 'bg-red-500 border-red-500 text-white' : 'border-slate-300 text-slate-500'; ?>">Hapus</a>
                                    </div>
                                </td>
                            </tr>

                            <?php if ($sedang_konfirmasi): ?>
                                <tr>
                                    <td colspan="7" class="p-0">
                                        <form method="POST" action="<?= e(url_daftar($filter, $halaman_ini)); ?>"
                                              class="flex items-center justify-between gap-4 px-5 py-3 border-y bg-red-200 border-red-400 text-red-950">
                                            <input type="hidden" name="id" value="<?= (int) $d['id']; ?>">
                                            <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                                            <p class="text-sm font-semibold">
                                                Anda yakin akan menghapus data donasi <?= e($d['nama']); ?> oleh <?= e($d['donatur'] ?? '-'); ?> (<?= e($d['donatur_email'] ?? '-'); ?>)?
                                            </p>
                                            <div class="flex items-center gap-4 shrink-0">
                                                <button type="submit" name="aksi" value="hapus" title="Ya, hapus" aria-label="Ya, hapus" class="text-slate-800 hover:text-green-700">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                                <a href="<?= e(url_daftar($filter, $halaman_ini)); ?>" title="Batal" aria-label="Batal" class="text-slate-800 hover:text-red-700">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
                                                </a>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-wrap items-center justify-between gap-3 mt-4 px-1">
                <p class="text-xs font-semibold text-slate-500">Menampilkan <?= count($donasi); ?> dari <?= number_format($total, 0, ',', '.'); ?></p>

                <div class="flex items-center gap-2 text-xs">
                    <?php if ($halaman_ini > 1): ?>
                        <a href="<?= e(url_daftar($filter, $halaman_ini - 1)); ?>" class="px-3 h-8 flex items-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">Sebelumnya</a>
                    <?php else: ?>
                        <span class="px-3 h-8 flex items-center rounded-md border border-slate-200 bg-white text-slate-300 cursor-not-allowed">Sebelumnya</span>
                    <?php endif; ?>

                    <?php foreach (daftar_halaman($halaman_ini, $total_halaman) as $n): ?>
                        <?php if ($n === '...'): ?>
                            <span class="min-w-[2rem] h-8 px-2 flex items-center justify-center rounded-md border border-slate-300 bg-white text-slate-500">...</span>
                        <?php else: ?>
                            <a href="<?= e(url_daftar($filter, $n)); ?>"
                               class="min-w-[2rem] h-8 px-2 flex items-center justify-center rounded-md border <?= $n === $halaman_ini ? 'border-blue-500 bg-blue-50 text-blue-600 font-semibold' : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50'; ?>">
                                <?= $n; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($halaman_ini < $total_halaman): ?>
                        <a href="<?= e(url_daftar($filter, $halaman_ini + 1)); ?>" class="px-3 h-8 flex items-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">Selanjutnya</a>
                    <?php else: ?>
                        <span class="px-3 h-8 flex items-center rounded-md border border-slate-200 bg-white text-slate-300 cursor-not-allowed">Selanjutnya</span>
                    <?php endif; ?>
                </div>
            </div>

<?php layout_akhir(); ?>
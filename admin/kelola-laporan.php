<?php
/* ============================================================
   kelola-laporan.php  -  daftar laporan donasi (admin)
   Mode preview / database diatur di admin-layout.php
   ============================================================ */
require_once 'admin-layout.php';
require_once 'laporan-fungsi.php';

function url_daftar($filter, $halaman, $tambah = []) {
    return 'kelola-laporan.php?' . http_build_query(array_merge(['status' => $filter, 'hal' => $halaman], $tambah));
}

/* ---------- Parameter halaman ---------- */
$filter = $_GET['status'] ?? 'semua';
if (!in_array($filter, ['semua', 'menunggu', 'terverifikasi', 'ditolak'], true)) {
    $filter = 'semua';
}
$dari          = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['dari'] ?? '') ? $_GET['dari'] : '';
$sampai        = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['sampai'] ?? '') ? $_GET['sampai'] : '';
$cari          = trim($_GET['q'] ?? '');
$halaman_ini   = max(1, (int) ($_GET['hal'] ?? 1));
$tambah_filter = array_filter(['dari' => $dari, 'sampai' => $sampai, 'q' => $cari], 'strlen');

/* ---------- Ambil data laporan + ringkasan ---------- */
if (MODE_PREVIEW) {
    $semua = data_contoh_laporan();

    $ringkas = ['total' => count($semua), 'terverifikasi' => 0, 'menunggu' => 0];
    foreach ($semua as $s) {
        if (isset($ringkas[$s['status']])) $ringkas[$s['status']]++;
    }

    $semua = array_values(array_filter($semua, function ($p) use ($filter, $dari, $sampai, $cari) {
        if ($filter !== 'semua' && $p['status'] !== $filter) return false;
        if ($dari !== ''   && $p['created_at'] < $dari)      return false;
        if ($sampai !== '' && $p['created_at'] > $sampai)    return false;
        if ($cari !== '' && stripos($p['nama'] . ' ' . $p['donatur'] . ' ' . $p['peminta'], $cari) === false) return false;
        return true;
    }));
    $total = count($semua);

    $total_halaman = max(1, (int) ceil($total / PER_HALAMAN));
    $halaman_ini   = min($halaman_ini, $total_halaman);
    $offset        = ($halaman_ini - 1) * PER_HALAMAN;

    $laporan = array_slice($semua, $offset, PER_HALAMAN);
} else {
    $ringkas = [
        'total'         => (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM permintaan")['n'],
        'terverifikasi' => (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM permintaan WHERE status = 'terverifikasi'")['n'],
        'menunggu'      => (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM permintaan WHERE COALESCE(status, 'menunggu') = 'menunggu'")['n'],
    ];

    $where = "1=1";
    $types = '';
    $param = [];
    if ($filter !== 'semua') {
        $where .= " AND COALESCE(p.status, 'menunggu') = ?";
        $types .= 's';
        $param[] = $filter;
    }
    if ($dari !== '')   { $where .= " AND DATE(p.created_at) >= ?"; $types .= 's'; $param[] = $dari; }
    if ($sampai !== '') { $where .= " AND DATE(p.created_at) <= ?"; $types .= 's'; $param[] = $sampai; }
    if ($cari !== '') {
        $where .= " AND (i.nama_barang LIKE ? OR ud.nama LIKE ? OR up.nama LIKE ?)";
        $types .= 'sss';
        $like = '%' . $cari . '%';
        array_push($param, $like, $like, $like);
    }

    $dasar = "FROM permintaan p
              JOIN items i       ON i.id = p.id_item
              LEFT JOIN users ud ON ud.id = i.user_id
              LEFT JOIN users up ON up.id = p.id_penerima
              WHERE $where";

    $total = (int) ambil_satu($conn, "SELECT COUNT(*) AS n $dasar", $types, ...$param)['n'];

    $total_halaman = max(1, (int) ceil($total / PER_HALAMAN));
    $halaman_ini   = min($halaman_ini, $total_halaman);
    $offset        = ($halaman_ini - 1) * PER_HALAMAN;
    $batas         = PER_HALAMAN;

    // Sesuaikan nama kolom/tabel dengan database Anda bila berbeda
    $laporan = ambil_semua(
        $conn,
        "SELECT p.id, i.nama_barang AS nama, i.kategori,
                COALESCE(i.lokasi, ud.lokasi) AS lokasi,
                p.created_at,
                COALESCE(p.status, 'menunggu') AS status,
                ud.nama AS donatur,
                up.nama AS peminta
         $dasar
         ORDER BY p.created_at DESC, p.id DESC
         LIMIT ? OFFSET ?",
        $types . 'ii', ...array_merge($param, [$batas, $offset])
    );
}

/* ---------- Tampilan ---------- */
$pil = [
    'semua'         => ['Semua',               'border-blue-500 bg-blue-50 text-blue-600'],
    'menunggu'      => ['Menunggu Verifikasi', 'border-amber-400 bg-amber-100 text-amber-700'],
    'terverifikasi' => ['Terverifikasi',       'border-green-500 bg-green-100 text-green-700'],
    'ditolak'       => ['Ditolak',             'border-red-500 bg-red-100 text-red-600'],
];
$pil_biasa = 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50';

layout_awal('Kelola Laporan', 'laporan');
?>

            <!-- Judul -->
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">Kelola Laporan</h1>
            <p class="text-sm text-slate-500 mt-2">Lihat riwayat donasi dan unduh laporannya.</p>

            <!-- Kartu ringkasan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                <?php foreach ([
                    ['Total Donasi',        $ringkas['total'],         'Seluruh donasi masuk'],
                    ['Terverifikasi',       $ringkas['terverifikasi'], 'Sudah lolos verifikasi'],
                    ['Menunggu Verifikasi', $ringkas['menunggu'],      'Perlu ditinjau admin'],
                ] as $k): ?>
                    <div class="bg-white rounded-2xl border border-slate-200 px-5 py-4">
                        <p class="text-xs font-semibold text-slate-500"><?= e($k[0]); ?></p>
                        <p class="text-3xl font-bold text-slate-900 mt-1"><?= number_format($k[1], 0, ',', '.'); ?></p>
                        <p class="text-xs text-slate-400 mt-0.5"><?= e($k[2]); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Filter status -->
            <div class="flex flex-wrap gap-3 mt-6">
                <?php foreach ($pil as $kunci => $info): ?>
                    <a href="<?= e(url_daftar($kunci, 1, $tambah_filter)); ?>"
                       class="px-6 py-1.5 rounded-full border text-sm font-medium <?= $filter === $kunci ? $info[1] : $pil_biasa; ?>">
                        <?= e($info[0]); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Filter tanggal & pencarian -->
            <form method="GET" action="kelola-laporan.php" class="flex flex-wrap items-end gap-3 mt-4">
                <input type="hidden" name="status" value="<?= e($filter); ?>">
                <label class="text-xs font-semibold text-slate-500">Dari
                    <input type="date" name="dari" value="<?= e($dari); ?>" class="block mt-1 px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm text-slate-700">
                </label>
                <label class="text-xs font-semibold text-slate-500">Sampai
                    <input type="date" name="sampai" value="<?= e($sampai); ?>" class="block mt-1 px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm text-slate-700">
                </label>
                <label class="text-xs font-semibold text-slate-500 flex-1 min-w-[180px]">Cari
                    <input type="search" name="q" value="<?= e($cari); ?>" placeholder="Nama barang / donatur / penerima"
                           class="block w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 bg-white text-sm text-slate-700">
                </label>
                <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">Terapkan</button>
                <?php if ($tambah_filter): ?>
                    <a href="<?= e(url_daftar($filter, 1)); ?>" class="px-3 py-2 text-sm text-slate-500 hover:text-slate-700">Reset</a>
                <?php endif; ?>
            </form>

            <!-- Tabel laporan -->
            <div class="bg-white rounded-2xl border border-slate-200 mt-6 overflow-x-auto">
                <table class="w-full text-left min-w-[860px]">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500">
                            <th class="px-5 py-4">No. Laporan</th>
                            <th class="px-3 py-4">Nama Barang</th>
                            <th class="px-3 py-4">Donatur</th>
                            <th class="px-3 py-4">Penerima</th>
                            <th class="px-3 py-4">Tanggal</th>
                            <th class="px-3 py-4">Status</th>
                            <th class="px-3 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($laporan) === 0): ?>
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-400 border-t border-slate-100">Tidak ada laporan untuk filter ini.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($laporan as $p):
                            $b = $badge_laporan[$p['status']] ?? $badge_laporan['menunggu'];
                        ?>
                            <tr class="border-t border-slate-100">
                                <td class="px-5 py-3 text-sm font-semibold text-slate-500 whitespace-nowrap"><?= e(kode_laporan($p['id'])); ?></td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-full border border-slate-300 text-[10px] font-semibold text-slate-500 flex items-center justify-center shrink-0">
                                            <?= e(inisial($p['nama'])); ?>
                                        </span>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800"><?= e($p['nama']); ?></p>
                                            <p class="text-xs text-slate-400"><?= e($p['kategori']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-sm text-slate-700"><?= e($p['donatur'] ?? '-'); ?></td>
                                <td class="px-3 py-3 text-sm text-slate-700"><?= e($p['peminta'] ?? '-'); ?></td>
                                <td class="px-3 py-3 text-sm text-slate-700 whitespace-nowrap"><?= e(tgl_panjang($p['created_at'])); ?></td>
                                <td class="px-3 py-3">
                                    <span class="inline-block whitespace-nowrap px-3 py-1 rounded-full text-xs font-semibold <?= $b[1]; ?>"><?= e($b[0]); ?></span>
                                </td>
                                <td class="px-3 py-3">
                                    <a href="detail-laporan.php?id=<?= (int) $p['id']; ?>"
                                       class="px-3 py-1 rounded-md border border-slate-300 text-xs text-slate-500 hover:bg-green-500 hover:border-green-500 hover:text-white">Lihat</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-wrap items-center justify-between gap-3 mt-4 px-1">
                <p class="text-xs font-semibold text-slate-500">Menampilkan <?= count($laporan); ?> dari <?= number_format($total, 0, ',', '.'); ?></p>

                <div class="flex items-center gap-2 text-xs">
                    <?php if ($halaman_ini > 1): ?>
                        <a href="<?= e(url_daftar($filter, $halaman_ini - 1, $tambah_filter)); ?>" class="px-3 h-8 flex items-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">Sebelumnya</a>
                    <?php else: ?>
                        <span class="px-3 h-8 flex items-center rounded-md border border-slate-200 bg-white text-slate-300 cursor-not-allowed">Sebelumnya</span>
                    <?php endif; ?>

                    <?php foreach (daftar_halaman($halaman_ini, $total_halaman) as $n): ?>
                        <?php if ($n === '...'): ?>
                            <span class="min-w-[2rem] h-8 px-2 flex items-center justify-center rounded-md border border-slate-300 bg-white text-slate-500">...</span>
                        <?php else: ?>
                            <a href="<?= e(url_daftar($filter, $n, $tambah_filter)); ?>"
                               class="min-w-[2rem] h-8 px-2 flex items-center justify-center rounded-md border <?= $n === $halaman_ini ? 'border-blue-500 bg-blue-50 text-blue-600 font-semibold' : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50'; ?>">
                                <?= $n; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($halaman_ini < $total_halaman): ?>
                        <a href="<?= e(url_daftar($filter, $halaman_ini + 1, $tambah_filter)); ?>" class="px-3 h-8 flex items-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">Selanjutnya</a>
                    <?php else: ?>
                        <span class="px-3 h-8 flex items-center rounded-md border border-slate-200 bg-white text-slate-300 cursor-not-allowed">Selanjutnya</span>
                    <?php endif; ?>
                </div>
            </div>

<?php layout_akhir(); ?>
<?php
/* ============================================================
   kelola-permintaan.php  -  daftar permintaan barang (admin)
   Mode preview / database diatur di admin-layout.php
   ============================================================ */
require_once 'admin-layout.php';

function url_daftar($filter, $halaman, $tambah = []) {
    return 'kelola-permintaan.php?' . http_build_query(array_merge(['status' => $filter, 'hal' => $halaman], $tambah));
}

// data contoh untuk mode preview
function data_contoh_permintaan() {
    $barang   = ['Kemeja Flanel', 'Buku Cerita Anak', 'Kipas Angin', 'Jaket Hoodie', 'Rak Buku', 'Sepatu Sekolah', 'Lampu Belajar', 'Selimut Tebal', 'Tas Ransel', 'Meja Lipat'];
    $kategori = ['Pakaian', 'Buku', 'Elektronik', 'Pakaian', 'Perabot', 'Pakaian', 'Elektronik', 'Perabot', 'Pakaian', 'Perabot'];
    $donatur  = ['Made Wijaya', 'Ni Luh Putri', 'Komang Ayu', 'Kadek Arya', 'Nadya Prameswari'];
    $peminta  = ['Rani Putri', 'Putu Dharma', 'Ayu Lestari', 'Wayan Sudira', 'Gede Pratama'];
    $lokasi   = ['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Denpasar'];
    $status   = ['terverifikasi', 'terverifikasi', 'ditolak', 'menunggu', 'menunggu', 'terverifikasi', 'ditolak', 'menunggu', 'terverifikasi', 'menunggu'];
    $hasil    = [];
    for ($i = 1; $i <= 23; $i++) {
        $k = ($i - 1) % 10;
        $p = $peminta[($i - 1) % count($peminta)];
        $hasil[] = [
            'id'            => $i,
            'nama'          => $barang[$k],
            'kategori'      => $kategori[$k],
            'donatur'       => $donatur[($i - 1) % count($donatur)],
            'peminta'       => $p,
            'peminta_email' => strtolower(str_replace(' ', '.', $p)) . '@gmail.com',
            'lokasi'        => $lokasi[($i - 1) % count($lokasi)],
            'created_at'    => date('Y-m-d', strtotime('2026-06-23 +' . ($i * 2) . ' day')),
            'status'        => $status[$k],
            'deskripsi'     => $barang[$k] . ' dalam kondisi baik, baru dipakai 1x.',
            'foto'          => '',
        ];
    }
    return $hasil;
}

/* ---------- Parameter halaman ---------- */
$filter = $_GET['status'] ?? 'semua';
if (!in_array($filter, ['semua', 'menunggu', 'terverifikasi', 'ditolak'], true)) {
    $filter = 'semua';
}
$halaman_ini   = max(1, (int) ($_GET['hal'] ?? 1));
$konfirmasi_id = (int) ($_GET['konfirmasi'] ?? 0);
$ok            = $_GET['ok'] ?? '';

/* ---------- Proses hapus permintaan ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    $id = (int) ($_POST['id'] ?? 0);

    if (!MODE_PREVIEW && !hash_equals($csrf, $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die("Permintaan tidak valid.");
    }

    if ($id > 0) {
        if (!MODE_PREVIEW) {
            $stmt = mysqli_prepare($conn, "DELETE FROM permintaan WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        header("Location: " . url_daftar($filter, $halaman_ini, ['ok' => 'hapus']));
        exit;
    }
}

/* ---------- Ambil data permintaan ---------- */
if (MODE_PREVIEW) {
    $semua = data_contoh_permintaan();
    if ($filter !== 'semua') {
        $semua = array_values(array_filter($semua, function ($p) use ($filter) {
            return $p['status'] === $filter;
        }));
    }
    $total = count($semua);

    $total_halaman = max(1, (int) ceil($total / PER_HALAMAN));
    $halaman_ini   = min($halaman_ini, $total_halaman);
    $offset        = ($halaman_ini - 1) * PER_HALAMAN;

    $permintaan = array_slice($semua, $offset, PER_HALAMAN);
} else {
    $where = "1=1";
    $types = '';
    $param = [];
    if ($filter !== 'semua') {
        $where .= " AND COALESCE(p.status, 'menunggu') = ?";
        $types .= 's';
        $param[] = $filter;
    }

    $total = (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM permintaan p WHERE $where", $types, ...$param)['n'];

    $total_halaman = max(1, (int) ceil($total / PER_HALAMAN));
    $halaman_ini   = min($halaman_ini, $total_halaman);
    $offset        = ($halaman_ini - 1) * PER_HALAMAN;
    $batas         = PER_HALAMAN;

    // Sesuaikan nama kolom/tabel dengan database Anda bila berbeda
    $permintaan = ambil_semua(
        $conn,
        "SELECT p.id, i.nama_barang AS nama, i.kategori,
                COALESCE(i.lokasi, ud.lokasi) AS lokasi,
                p.created_at,
                COALESCE(p.status, 'menunggu') AS status,
                ud.nama AS donatur,
                up.nama AS peminta, up.email AS peminta_email
         FROM permintaan p
         JOIN items i       ON i.id = p.id_item
         LEFT JOIN users ud ON ud.id = i.user_id
         LEFT JOIN users up ON up.id = p.id_penerima
         WHERE $where
         ORDER BY p.created_at DESC, p.id DESC
         LIMIT ? OFFSET ?",
        $types . 'ii', ...array_merge($param, [$batas, $offset])
    );
}

/* ---------- Tampilan ---------- */
$pil = [
    'semua'         => ['Semua',             'border-blue-500 bg-blue-50 text-blue-600'],
    'menunggu'      => ['Menunggu Verifikasi', 'border-amber-400 bg-amber-100 text-amber-700'],
    'terverifikasi' => ['Terverifikasi',     'border-green-500 bg-green-100 text-green-700'],
    'ditolak'       => ['Ditolak',           'border-red-500 bg-red-100 text-red-600'],
];
$pil_biasa = 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50';

$badge = [
    'menunggu'      => ['Menunggu Verifikasi', 'bg-amber-100 text-amber-700'],
    'terverifikasi' => ['Terverifikasi',       'bg-green-100 text-green-700'],
    'ditolak'       => ['Ditolak',             'bg-red-100 text-red-600'],
];

layout_awal('Kelola Permintaan', 'permintaan');
?>

            <!-- Judul -->
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">Kelola Permintaan</h1>
            <p class="text-sm text-slate-500 mt-2">Atur data donasi disini.</p>

            <?php if ($ok === 'hapus'): ?>
                <div class="mt-5 bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3">
                    Data permintaan berhasil dihapus.<?= MODE_PREVIEW ? ' (mode preview, perubahan tidak disimpan)' : ''; ?>
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

            <!-- Tabel permintaan -->
            <div class="bg-white rounded-2xl border border-slate-200 mt-6 overflow-x-auto">
                <table class="w-full text-left min-w-[860px]">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-500">
                            <th class="px-5 py-4">Nama Barang</th>
                            <th class="px-3 py-4">Kategori</th>
                            <th class="px-3 py-4">Donatur</th>
                            <th class="px-3 py-4">Lokasi</th>
                            <th class="px-3 py-4">Tanggal Ajuan</th>
                            <th class="px-3 py-4">Status</th>
                            <th class="px-3 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($permintaan) === 0): ?>
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-400 border-t border-slate-100">Tidak ada permintaan untuk filter ini.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($permintaan as $p):
                            $b                 = $badge[$p['status']] ?? $badge['menunggu'];
                            $sedang_konfirmasi = ($konfirmasi_id === (int) $p['id']);
                        ?>
                            <tr id="d<?= (int) $p['id']; ?>" class="border-t border-slate-100">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-full border border-slate-300 text-[10px] font-semibold text-slate-500 flex items-center justify-center shrink-0">
                                            <?= e(inisial($p['nama'])); ?>
                                        </span>
                                        <p class="text-sm font-bold text-slate-800"><?= e($p['nama']); ?></p>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-sm text-slate-700"><?= e($p['kategori']); ?></td>
                                <td class="px-3 py-3 text-sm text-slate-700"><?= e($p['donatur'] ?? '-'); ?></td>
                                <td class="px-3 py-3 text-sm text-slate-700"><?= e($p['lokasi'] ?? '-'); ?></td>
                                <td class="px-3 py-3 text-sm text-slate-700 whitespace-nowrap"><?= e(tgl_panjang($p['created_at'])); ?></td>
                                <td class="px-3 py-3">
                                    <span class="inline-block whitespace-nowrap px-3 py-1 rounded-full text-xs font-semibold <?= $b[1]; ?>"><?= e($b[0]); ?></span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="data-permintaan.php?id=<?= (int) $p['id']; ?>"
                                           class="px-3 py-1 rounded-md border border-slate-300 text-xs text-slate-500 hover:bg-green-500 hover:border-green-500 hover:text-white">Lihat</a>
                                        <a href="<?= e(url_daftar($filter, $halaman_ini, ['konfirmasi' => $p['id']]) . '#d' . (int) $p['id']); ?>"
                                           class="px-3 py-1 rounded-md border text-xs hover:bg-red-500 hover:border-red-500 hover:text-white <?= $sedang_konfirmasi ? 'bg-red-500 border-red-500 text-white' : 'border-slate-300 text-slate-500'; ?>">Hapus</a>
                                    </div>
                                </td>
                            </tr>

                            <?php if ($sedang_konfirmasi): ?>
                                <tr>
                                    <td colspan="7" class="p-0">
                                        <form method="POST" action="<?= e(url_daftar($filter, $halaman_ini)); ?>"
                                              class="flex items-center justify-between gap-4 px-5 py-3 border-y bg-red-200 border-red-400 text-red-950">
                                            <input type="hidden" name="id" value="<?= (int) $p['id']; ?>">
                                            <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                                            <p class="text-sm font-semibold">
                                                Anda yakin akan menghapus data permintaan <?= e($p['nama']); ?> oleh <?= e($p['peminta'] ?? '-'); ?> (<?= e($p['peminta_email'] ?? '-'); ?>)?
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
                <p class="text-xs font-semibold text-slate-500">Menampilkan <?= count($permintaan); ?> dari <?= number_format($total, 0, ',', '.'); ?></p>

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
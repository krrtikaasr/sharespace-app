<?php
date_default_timezone_set('Asia/Makassar'); // WITA

/* ============================================================
   MODE PREVIEW
   true  = tampilan saja (data contoh, tanpa login & database)
   false = terhubung database (butuh login admin + tabel siap)
   ============================================================ */
const MODE_PREVIEW = true;

/* Jumlah baris pada tabel "Donasi terbaru" & "Permintaan terbaru" */
const BATAS_TERBARU = 3;

/* ---------- Helper ---------- */
function e($teks) {
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function ambil_semua($conn, $sql, $types = '', ...$params) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $data = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $data[] = $r;
    }
    mysqli_stmt_close($stmt);
    return $data;
}

function ambil_satu($conn, $sql, $types = '', ...$params) {
    $semua = ambil_semua($conn, $sql, $types, ...$params);
    return $semua[0] ?? null;
}

// path foto jika filenya ada di folder uploads, selain itu null
function foto_ada($file, $folder = 'uploads/') {
    if (!empty($file) && file_exists(__DIR__ . '/' . $folder . $file)) {
        return $folder . $file;
    }
    return null;
}

function inisial($nama) {
    $kata = preg_split('/\s+/', trim($nama));
    if (count($kata) > 1) {
        return strtoupper(substr($kata[0], 0, 1) . substr(end($kata), 0, 1));
    }
    return strtoupper(substr($kata[0] ?? '', 0, 2)) ?: '?';
}

/* ---------- Autentikasi admin & ambil data ---------- */
$bulan_pendek = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

// kerangka data 6 bulan terakhir (urut dari yang terlama)
$deret = [];
for ($i = 5; $i >= 0; $i--) {
    $ts = strtotime(date('Y-m-01') . " -$i month");
    $deret[date('Y-m', $ts)] = [
        'label'      => $bulan_pendek[(int) date('n', $ts)],
        'donasi'     => 0,
        'permintaan' => 0,
    ];
}

if (MODE_PREVIEW) {
    $admin_nama = 'Admin';

    $stat = ['pengguna' => 128, 'donasi' => 342, 'permintaan' => 215, 'menunggu' => 18];

    $contoh_donasi     = [18, 24, 21, 34, 29, 40];
    $contoh_permintaan = [12, 17, 18, 25, 26, 31];
    $n = 0;
    foreach ($deret as $kunci => $isi) {
        $deret[$kunci]['donasi']     = $contoh_donasi[$n];
        $deret[$kunci]['permintaan'] = $contoh_permintaan[$n];
        $n++;
    }

    $kategori = [
        ['nama' => 'Makanan & sembako', 'total' => 82],
        ['nama' => 'Pakaian',           'total' => 66],
        ['nama' => 'Buku & alat tulis', 'total' => 51],
        ['nama' => 'Elektronik',        'total' => 40],
        ['nama' => 'Perabot rumah',     'total' => 31],
        ['nama' => 'Mainan anak',       'total' => 20],
    ];

    $donasi_terbaru = [
        ['barang' => 'Lampu Meja Kayu',     'foto' => '', 'donatur' => 'Rani Putri', 'status' => 'Permintaan masuk'],
        ['barang' => 'Kipas Angin Portable', 'foto' => '', 'donatur' => 'Ni Luh Putri', 'status' => 'Disetujui'],
        ['barang' => 'Kemeja Flanel',       'foto' => '', 'donatur' => 'Made Wijaya', 'status' => 'Tersedia'],
    ];

    $permintaan_terbaru = [
        ['barang' => 'Lampu Meja Kayu',     'pemohon' => 'Nadya Prameswari', 'status' => 'Menunggu'],
        ['barang' => 'Kipas Angin Portable', 'pemohon' => 'Made Wijaya',      'status' => 'Disetujui'],
        ['barang' => 'Rak Buku Kecil',      'pemohon' => 'Komang Ayu',       'status' => 'Ditolak'],
    ];
} else {
    session_start();
    require_once 'config/database.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
    $user_id = (int) $_SESSION['user_id'];

    $admin = ambil_satu($conn, "SELECT nama, role FROM users WHERE id = ?", "i", $user_id);
    if (!$admin || $admin['role'] !== 'admin') {
        http_response_code(403);
        die("Akses ditolak. Halaman ini hanya untuk admin.");
    }
    $admin_nama = $admin['nama'];

    /* --- Kartu statistik --- */
    $stat = [
        'pengguna'   => (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM users WHERE COALESCE(role, 'user') <> 'admin'")['n'],
        'donasi'     => (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM items")['n'],
        'permintaan' => (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM permintaan")['n'],
        'menunggu'   => (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM permintaan WHERE status_verifikasi = 'Pending'")['n'],
    ];

    /* --- Grafik per bulan (6 bulan terakhir) --- */
    $awal = array_key_first($deret) . '-01';

    $baris = ambil_semua(
        $conn,
        "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS total
         FROM items WHERE created_at >= ? GROUP BY ym",
        "s", $awal
    );
    foreach ($baris as $r) {
        if (isset($deret[$r['ym']])) {
            $deret[$r['ym']]['donasi'] = (int) $r['total'];
        }
    }

    $baris = ambil_semua(
        $conn,
        "SELECT DATE_FORMAT(tanggal_pengajuan, '%Y-%m') AS ym, COUNT(*) AS total
         FROM permintaan WHERE tanggal_pengajuan >= ? GROUP BY ym",
        "s", $awal
    );
    foreach ($baris as $r) {
        if (isset($deret[$r['ym']])) {
            $deret[$r['ym']]['permintaan'] = (int) $r['total'];
        }
    }

    /* --- Donasi per kategori --- */
    $kategori = ambil_semua(
        $conn,
        "SELECT k.nama, COUNT(i.id) AS total
         FROM kategori k
         LEFT JOIN items i ON i.kategori_id = k.id
         GROUP BY k.id, k.nama
         ORDER BY total DESC, k.nama ASC
         LIMIT 6"
    );

    /* --- Donasi terbaru --- */
    $batas = BATAS_TERBARU;
    $baris = ambil_semua(
        $conn,
        "SELECT i.nama_barang, i.foto, u.nama AS donatur,
                (SELECT COUNT(*) FROM permintaan p WHERE p.id_barang = i.id AND p.status_verifikasi = 'Pending')   AS pending,
                (SELECT COUNT(*) FROM permintaan p WHERE p.id_barang = i.id AND p.status_verifikasi = 'Disetujui') AS disetujui
         FROM items i
         JOIN users u ON i.user_id = u.id
         ORDER BY i.created_at DESC, i.id DESC
         LIMIT ?",
        "i", $batas
    );
    $donasi_terbaru = [];
    foreach ($baris as $r) {
        if ($r['pending'] > 0) {
            $status = 'Permintaan masuk';
        } elseif ($r['disetujui'] > 0) {
            $status = 'Disetujui';
        } else {
            $status = 'Tersedia';
        }
        $donasi_terbaru[] = [
            'barang'  => $r['nama_barang'],
            'foto'    => $r['foto'],
            'donatur' => $r['donatur'],
            'status'  => $status,
        ];
    }

    /* --- Permintaan terbaru --- */
    $baris = ambil_semua(
        $conn,
        "SELECT i.nama_barang, u.nama AS pemohon, p.status_verifikasi
         FROM permintaan p
         JOIN items i ON p.id_barang = i.id
         JOIN users u ON p.id_penerima = u.id
         ORDER BY p.tanggal_pengajuan DESC, p.id_permintaan DESC
         LIMIT ?",
        "i", $batas
    );
    $peta_status = ['Pending' => 'Menunggu', 'Disetujui' => 'Disetujui', 'Ditolak' => 'Ditolak'];
    $permintaan_terbaru = [];
    foreach ($baris as $r) {
        $permintaan_terbaru[] = [
            'barang'  => $r['nama_barang'],
            'pemohon' => $r['pemohon'],
            'status'  => $peta_status[$r['status_verifikasi']] ?? $r['status_verifikasi'],
        ];
    }
}

/* ---------- Turunan untuk tampilan ---------- */
$maks_grafik = 1;
foreach ($deret as $b) {
    $maks_grafik = max($maks_grafik, $b['donasi'], $b['permintaan']);
}

$maks_kategori = 1;
foreach ($kategori as $k) {
    $maks_kategori = max($maks_kategori, (int) $k['total']);
}

$badge = [
    'Permintaan masuk' => 'bg-amber-100 text-amber-800',
    'Menunggu'         => 'bg-amber-100 text-amber-800',
    'Disetujui'        => 'bg-green-100 text-green-800',
    'Tersedia'         => 'bg-blue-100 text-blue-700',
    'Ditolak'          => 'bg-red-100 text-red-700',
];

/* ---------- Ikon (path SVG) ---------- */
$ikon = [
    'beranda'    => '<path d="M3 11l9-8 9 8v10a1 1 0 01-1 1h-5v-7H9v7H4a1 1 0 01-1-1V11z"/>',
    'donasi'     => '<path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8M7.5 5.5l9 5"/>',
    'pengguna'   => '<circle cx="9" cy="8" r="3.5" fill="currentColor" stroke="none"/><path d="M2 20c0-3.6 3-6 7-6s7 2.4 7 6z" fill="currentColor" stroke="none"/><circle cx="17.5" cy="9" r="2.5" fill="currentColor" stroke="none"/><path d="M17.2 14c2.9 0 4.8 1.8 4.8 4.5V20h-4.2" fill="currentColor" stroke="none"/>',
    'permintaan' => '<rect x="3" y="8" width="18" height="12" rx="2.5"/><path d="M9 8V6a2 2 0 012-2h2a2 2 0 012 2v2M3 14h18"/>',
    'kategori'   => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 01-2.8 0L3 13V3h10l7.6 7.6a2 2 0 010 2.8z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
    'laporan'    => '<path d="M5 22V3"/><path d="M5 4h13l-3 4.5 3 4.5H5"/>',
    'pengaturan' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>',
    'logo'       => '<path d="M11 14h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 16"/><path d="m7 20 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9"/><path d="m2 15 6 6"/><path d="M19.5 8.5c.7-.7 1.5-1.6 1.5-2.7A2.73 2.73 0 0 0 16 4a2.78 2.78 0 0 0-5 1.8c0 1.2.8 2 1.5 2.8L16 12Z"/>',
    'menunggu'   => '<path d="M5 22V3"/><path d="M5 4h13l-3 4.5 3 4.5H5"/>',
];

/* ---------- Menu sidebar ---------- */
$menu = [
    ['kunci' => 'beranda',    'label' => 'Beranda',          'url' => 'beranda-admin.php'],
    ['kunci' => 'donasi',     'label' => 'Kelola Donasi',    'url' => 'kelola-donasi.php'],
    ['kunci' => 'pengguna',   'label' => 'Kelola Pengguna',  'url' => 'kelola-pengguna.php'],
    ['kunci' => 'permintaan', 'label' => 'Kelola Permintaan','url' => 'kelola-permintaan.php'],
    ['kunci' => 'kategori',   'label' => 'Kategori Barang',  'url' => 'kategori-barang.php'],
    ['kunci' => 'laporan',    'label' => 'Laporan',          'url' => 'laporan.php'],
    ['kunci' => 'pengaturan', 'label' => 'Pengaturan',       'url' => 'pengaturan.php'],
];
$menu_aktif = 'beranda';

/* ---------- Kartu statistik ---------- */
$kartu = [
    ['ikon' => 'pengguna',   'angka' => $stat['pengguna'],   'label' => 'Total pengguna'],
    ['ikon' => 'donasi',     'angka' => $stat['donasi'],     'label' => 'Total donasi'],
    ['ikon' => 'permintaan', 'angka' => $stat['permintaan'], 'label' => 'Total permintaan'],
    ['ikon' => 'menunggu',   'angka' => $stat['menunggu'],   'label' => 'Menunggu persetujuan'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Admin - ShareSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        try {
            if (localStorage.getItem('sidebar') === 'tutup' && window.innerWidth >= 1024) {
                document.documentElement.classList.add('sb-tutup');
            }
        } catch (err) {}
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }

        #sidebar { width: 18rem; transition: width .2s ease, transform .2s ease; }
        .sb-label { white-space: nowrap; }
        #sb-overlay { display: none; }

        @media (min-width: 1024px) {
            html.sb-tutup #sidebar { width: 5rem; }
            html.sb-tutup #sidebar .sb-label { display: none; }
            html.sb-tutup #sidebar .sb-brand { justify-content: center; padding-left: 0; padding-right: 0; }
            html.sb-tutup #sidebar nav { padding-left: .75rem; padding-right: .75rem; }
            html.sb-tutup #sidebar nav a { justify-content: center; padding-left: 0; padding-right: 0; }
        }
        @media (max-width: 1023px) {
            #sidebar { position: fixed; top: 0; bottom: 0; left: 0; z-index: 40; transform: translateX(-100%); }
            html.sb-buka #sidebar { transform: none; box-shadow: 0 0 40px rgba(15, 23, 42, .25); }
            html.sb-buka #sb-overlay { display: block; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
<div class="flex min-h-screen">

    <!-- ================= SIDEBAR ================= -->
    <aside id="sidebar" class="flex flex-col shrink-0 bg-white border-r border-slate-200 overflow-y-auto overflow-x-hidden lg:sticky lg:top-0 lg:h-screen">
        <div class="sb-brand flex items-start gap-3 px-8 pt-9 pb-8">
            <span class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><?= $ikon['logo']; ?></svg>
            </span>
            <div class="sb-label">
                <p class="text-2xl font-bold leading-7 text-slate-800">ShareSpace</p>
                <p class="text-xs font-semibold text-blue-600 mt-1 tracking-wide">ADMIN</p>
            </div>
        </div>

        <nav class="px-5 space-y-1.5" aria-label="Menu utama">
            <?php foreach ($menu as $m): $aktif = ($m['kunci'] === $menu_aktif); ?>
                <a href="<?= e($m['url']); ?>" title="<?= e($m['label']); ?>"
                   class="flex items-center gap-4 px-4 py-3.5 rounded-xl font-medium <?= $aktif ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'; ?>">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><?= $ikon[$m['kunci']]; ?></svg>
                    <span class="sb-label"><?= e($m['label']); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <!-- Latar gelap saat sidebar dibuka di layar kecil -->
    <div id="sb-overlay" class="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" onclick="sidebarToggle()"></div>

    <!-- ================= KONTEN UTAMA ================= -->
    <main class="flex-1 min-w-0">

        <!-- Bar atas: tombol buka/tutup sidebar -->
        <div class="px-5 pt-5 sm:px-8 lg:px-14 lg:pt-8">
            <button type="button" id="sb-tombol" onclick="sidebarToggle()"
                    aria-label="Buka atau tutup sidebar" aria-controls="sidebar" aria-expanded="true"
                    class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>

        <div class="px-5 py-6 sm:px-8 lg:px-14 lg:py-8 max-w-[1500px]">

            <!-- Header -->
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-slate-800">Beranda</h1>
                    <p class="text-slate-500 mt-2">Ringkasan aktivitas ShareSpace.</p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <span class="w-12 h-12 rounded-full bg-slate-200 text-slate-500 font-semibold flex items-center justify-center">
                        <?= e(inisial($admin_nama)); ?>
                    </span>
                    <div class="leading-tight">
                        <p class="font-semibold text-slate-800"><?= e($admin_nama); ?></p>
                        <a href="logout.php" class="text-sm text-red-600 hover:underline">Keluar</a>
                    </div>
                </div>
            </div>

            <!-- Kartu statistik -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-9">
                <?php foreach ($kartu as $k): ?>
                    <div class="bg-white rounded-3xl border border-slate-200 px-6 py-8 flex items-center gap-4">
                        <span class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><?= $ikon[$k['ikon']]; ?></svg>
                        </span>
                        <div>
                            <p class="text-3xl font-bold leading-8 text-slate-800"><?= number_format($k['angka'], 0, ',', '.'); ?></p>
                            <p class="text-slate-500 mt-1"><?= e($k['label']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Grafik & kategori -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mt-6">

                <!-- Donasi & permintaan per bulan -->
                <section class="xl:col-span-2 bg-white rounded-3xl border border-slate-200 p-7">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <h2 class="text-xl font-bold text-slate-800">Donasi &amp; permintaan per bulan</h2>
                        <div class="flex items-center gap-6 text-sm text-slate-500">
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-blue-600"></span>Donasi</span>
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-blue-300"></span>Permintaan</span>
                        </div>
                    </div>

                    <div class="mt-6 flex items-end justify-around gap-2 px-2">
                        <?php foreach ($deret as $b):
                            $tinggi_donasi     = round($b['donasi'] / $maks_grafik * 100);
                            $tinggi_permintaan = round($b['permintaan'] / $maks_grafik * 100);
                        ?>
                            <div class="flex flex-col items-center flex-1">
                                <div class="h-[280px] flex items-end gap-1.5">
                                    <div class="w-7 sm:w-8 rounded-t-md bg-blue-600 min-h-[3px]" style="height: <?= $tinggi_donasi; ?>%" title="Donasi: <?= (int) $b['donasi']; ?>"></div>
                                    <div class="w-7 sm:w-8 rounded-t-md bg-blue-300 min-h-[3px]" style="height: <?= $tinggi_permintaan; ?>%" title="Permintaan: <?= (int) $b['permintaan']; ?>"></div>
                                </div>
                                <span class="text-sm text-slate-500 mt-4"><?= e($b['label']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- Donasi per kategori -->
                <section class="bg-white rounded-3xl border border-slate-200 p-7">
                    <h2 class="text-xl font-bold text-slate-800">Donasi per kategori</h2>

                    <div class="mt-6 space-y-5">
                        <?php if (count($kategori) === 0): ?>
                            <p class="text-sm text-slate-400">Belum ada kategori.</p>
                        <?php endif; ?>

                        <?php foreach ($kategori as $k):
                            // skala diberi ruang 25% supaya batang terpanjang tidak memenuhi lintasan
                            $lebar = round((int) $k['total'] / ($maks_kategori * 1.25) * 100);
                        ?>
                            <div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-slate-800"><?= e($k['nama']); ?></span>
                                    <span class="font-semibold text-slate-800"><?= number_format((int) $k['total'], 0, ',', '.'); ?></span>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-slate-200 overflow-hidden">
                                    <div class="h-full rounded-full bg-blue-600" style="width: <?= $lebar; ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <!-- Tabel terbaru -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-x-5 gap-y-8 mt-10">

                <!-- Donasi terbaru -->
                <section>
                    <h2 class="text-xl font-bold text-slate-800 mb-4">Donasi terbaru</h2>
                    <div class="bg-white rounded-3xl border border-slate-200 overflow-x-auto">
                        <table class="w-full text-left min-w-[480px]">
                            <thead>
                                <tr class="text-sm text-slate-400">
                                    <th class="font-normal px-6 py-4">Barang</th>
                                    <th class="font-normal px-3 py-4">Donatur</th>
                                    <th class="font-normal px-3 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($donasi_terbaru) === 0): ?>
                                    <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-400 border-t border-slate-100">Belum ada donasi.</td></tr>
                                <?php endif; ?>

                                <?php foreach ($donasi_terbaru as $d): $foto = foto_ada($d['foto']); ?>
                                    <tr class="border-t border-slate-100">
                                        <td class="px-6 py-3.5">
                                            <div class="flex items-center gap-4">
                                                <?php if ($foto): ?>
                                                    <img src="<?= e($foto); ?>" alt="<?= e($d['barang']); ?>" class="w-12 h-12 rounded-xl object-cover shrink-0">
                                                <?php else: ?>
                                                    <span class="w-12 h-12 rounded-xl bg-slate-200 shrink-0"></span>
                                                <?php endif; ?>
                                                <span class="font-semibold text-slate-800"><?= e($d['barang']); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3.5 text-slate-700"><?= e($d['donatur']); ?></td>
                                        <td class="px-3 py-3.5">
                                            <span class="inline-block whitespace-nowrap px-3 py-1.5 rounded-full text-xs font-semibold <?= $badge[$d['status']] ?? 'bg-slate-100 text-slate-600'; ?>">
                                                <?= e($d['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Permintaan terbaru -->
                <section>
                    <h2 class="text-xl font-bold text-slate-800 mb-4">Permintaan terbaru</h2>
                    <div class="bg-white rounded-3xl border border-slate-200 overflow-x-auto">
                        <table class="w-full text-left min-w-[480px]">
                            <thead>
                                <tr class="text-sm text-slate-400">
                                    <th class="font-normal px-6 py-4">Barang</th>
                                    <th class="font-normal px-3 py-4">Pemohon</th>
                                    <th class="font-normal px-3 py-4">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($permintaan_terbaru) === 0): ?>
                                    <tr><td colspan="3" class="px-6 py-8 text-center text-sm text-slate-400 border-t border-slate-100">Belum ada permintaan.</td></tr>
                                <?php endif; ?>

                                <?php foreach ($permintaan_terbaru as $p): ?>
                                    <tr class="border-t border-slate-100">
                                        <td class="px-6 py-6 text-slate-700"><?= e($p['barang']); ?></td>
                                        <td class="px-3 py-6 text-slate-700"><?= e($p['pemohon']); ?></td>
                                        <td class="px-3 py-6">
                                            <span class="inline-block whitespace-nowrap px-3 py-1.5 rounded-full text-xs font-semibold <?= $badge[$p['status']] ?? 'bg-slate-100 text-slate-600'; ?>">
                                                <?= e($p['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>

<script>
    var root = document.documentElement;
    var tombol = document.getElementById('sb-tombol');

    function sidebarTerbuka() {
        return window.innerWidth >= 1024 ? !root.classList.contains('sb-tutup') : root.classList.contains('sb-buka');
    }
    function sidebarTampilkanStatus() {
        tombol.setAttribute('aria-expanded', sidebarTerbuka() ? 'true' : 'false');
    }
    function sidebarToggle() {
        if (window.innerWidth >= 1024) {
            var tutup = root.classList.toggle('sb-tutup');
            try { localStorage.setItem('sidebar', tutup ? 'tutup' : 'buka'); } catch (err) {}
        } else {
            root.classList.toggle('sb-buka');
        }
        sidebarTampilkanStatus();
    }
    document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape' && root.classList.contains('sb-buka')) {
            root.classList.remove('sb-buka');
            sidebarTampilkanStatus();
        }
    });
    window.addEventListener('resize', function () {
        root.classList.remove('sb-buka');
        sidebarTampilkanStatus();
    });
    sidebarTampilkanStatus();
</script>
</body>
</html>
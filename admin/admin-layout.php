<?php
/* ============================================================
   admin-layout.php
   File bersama untuk halaman admin: helper, autentikasi,
   menu, dan kerangka tampilan (sidebar bisa dibuka/tutup).
   Dipakai oleh: kelola-donasi.php dan data-donasi.php
   ============================================================ */
date_default_timezone_set('Asia/Makassar'); // WITA

/* ============================================================
   MODE PREVIEW
   true  = tampilan saja (data contoh, tanpa login & database)
   false = terhubung database (butuh login admin + tabel siap)
   ============================================================ */
const MODE_PREVIEW = true;

/* Jumlah donasi per halaman */
const PER_HALAMAN = 10;

/* Folder tempat foto barang disimpan (untuk mode database) */
const FOLDER_FOTO = 'uploads/';

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

function inisial($nama) {
    $kata = preg_split('/\s+/', trim($nama));
    if (count($kata) > 1) {
        return strtoupper(substr($kata[0], 0, 1) . substr(end($kata), 0, 1));
    }
    return strtoupper(substr($kata[0] ?? '', 0, 2)) ?: '?';
}

function tgl_panjang($tanggal) {
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $t = strtotime((string) $tanggal);
    if (!$t) return '-';
    return date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
}

// daftar nomor halaman, contoh: [1, 2, '...', 8]
function daftar_halaman($sekarang, $total) {
    $tampil = [];
    for ($i = 1; $i <= $total; $i++) {
        if ($i === 1 || $i === $total || abs($i - $sekarang) <= 1) {
            $tampil[] = $i;
        }
    }
    $hasil = [];
    $sebelumnya = 0;
    foreach ($tampil as $n) {
        if ($n - $sebelumnya > 1) {
            $hasil[] = '...';
        }
        $hasil[] = $n;
        $sebelumnya = $n;
    }
    return $hasil;
}

// data contoh untuk mode preview
function data_contoh_donasi() {
    $barang   = ['Kemeja Flanel', 'Buku Cerita Anak', 'Kipas Angin', 'Jaket Hoodie', 'Rak Buku', 'Sepatu Sekolah', 'Lampu Belajar', 'Selimut Tebal', 'Tas Ransel', 'Meja Lipat'];
    $kategori = ['Pakaian', 'Buku', 'Elektronik', 'Pakaian', 'Perabot', 'Pakaian', 'Elektronik', 'Perabot', 'Pakaian', 'Perabot'];
    $donatur  = ['Made Wijaya', 'Ni Luh Putri', 'Komang Ayu', 'Kadek Arya', 'Nadya Prameswari'];
    $lokasi   = ['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Denpasar'];
    $hasil    = [];
    for ($i = 1; $i <= 23; $i++) {
        $k = ($i - 1) % 10;
        $d = $donatur[($i - 1) % count($donatur)];
        $hasil[] = [
            'id'            => $i,
            'nama'          => $barang[$k],
            'kategori'      => $kategori[$k],
            'donatur'       => $d,
            'donatur_email' => strtolower(str_replace(' ', '.', $d)) . '@gmail.com',
            'lokasi'        => $lokasi[($i - 1) % count($lokasi)],
            'created_at'    => date('Y-m-d', strtotime('2026-06-23 +' . ($i * 2) . ' day')),
            'status'        => ($k < 5) ? 'tersedia' : 'habis',
            'deskripsi'     => $barang[$k] . ' dalam kondisi baik, baru dipakai 1x.',
            'foto'          => '',
        ];
    }
    return $hasil;
}

/* ---------- Autentikasi admin ---------- */
if (MODE_PREVIEW) {
    $csrf = '';
} else {
    session_start();
    require_once 'config/database.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
    $user_id = (int) $_SESSION['user_id'];

    $admin = ambil_satu($conn, "SELECT role FROM users WHERE id = ?", "i", $user_id);
    if (!$admin || $admin['role'] !== 'admin') {
        http_response_code(403);
        die("Akses ditolak. Halaman ini hanya untuk admin.");
    }
    $csrf = $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
}

/* ---------- Ikon & menu ---------- */
$ikon = [
    'beranda'    => '<path d="M3 11l9-8 9 8v10a1 1 0 01-1 1h-5v-7H9v7H4a1 1 0 01-1-1V11z"/>',
    'donasi'     => '<path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8M7.5 5.5l9 5"/>',
    'pengguna'   => '<circle cx="9" cy="8" r="3.5" fill="currentColor" stroke="none"/><path d="M2 20c0-3.6 3-6 7-6s7 2.4 7 6z" fill="currentColor" stroke="none"/><circle cx="17.5" cy="9" r="2.5" fill="currentColor" stroke="none"/><path d="M17.2 14c2.9 0 4.8 1.8 4.8 4.5V20h-4.2" fill="currentColor" stroke="none"/>',
    'permintaan' => '<rect x="3" y="8" width="18" height="12" rx="2.5"/><path d="M9 8V6a2 2 0 012-2h2a2 2 0 012 2v2M3 14h18"/>',
    'kategori'   => '<path d="M20.6 13.4l-7.2 7.2a2 2 0 01-2.8 0L3 13V3h10l7.6 7.6a2 2 0 010 2.8z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
    'laporan'    => '<path d="M5 22V3"/><path d="M5 4h13l-3 4.5 3 4.5H5"/>',
    'pengaturan' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/>',
    'logo'       => '<path d="M11 14h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 16"/><path d="m7 20 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9"/><path d="m2 15 6 6"/><path d="M19.5 8.5c.7-.7 1.5-1.6 1.5-2.7A2.73 2.73 0 0 0 16 4a2.78 2.78 0 0 0-5 1.8c0 1.2.8 2 1.5 2.8L16 12Z"/>',
];

$menu = [
    ['kunci' => 'beranda',    'label' => 'Beranda',           'url' => 'beranda-admin.php'],
    ['kunci' => 'donasi',     'label' => 'Kelola Donasi',     'url' => 'kelola-donasi.php'],
    ['kunci' => 'pengguna',   'label' => 'Kelola Pengguna',   'url' => 'kelola-pengguna.php'],
    ['kunci' => 'permintaan', 'label' => 'Kelola Permintaan', 'url' => 'kelola-permintaan.php'],
    ['kunci' => 'kategori',   'label' => 'Kategori Barang',   'url' => 'kategori-barang.php'],
    ['kunci' => 'laporan',    'label' => 'Laporan',           'url' => 'laporan.php'],
    ['kunci' => 'pengaturan', 'label' => 'Pengaturan',        'url' => 'pengaturan.php'],
];

/* ---------- Kerangka tampilan: bagian atas ---------- */
function layout_awal($judul, $menu_aktif) {
    global $ikon, $menu;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul); ?> - ShareSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        // Pulihkan status sidebar sebelum halaman tampil (mencegah kedip)
        try {
            if (localStorage.getItem('sidebar') === 'tutup' && window.innerWidth >= 1024) {
                document.documentElement.classList.add('sb-tutup');
            }
        } catch (err) {}
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        tr[id^="d"] { scroll-margin-top: 90px; }

        /* ----- Sidebar ----- */
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

        <div class="px-5 py-6 sm:px-8 lg:px-14 lg:py-8 max-w-[1300px]">
<?php
}

/* ---------- Kerangka tampilan: bagian bawah ---------- */
function layout_akhir() {
?>
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
<?php
}
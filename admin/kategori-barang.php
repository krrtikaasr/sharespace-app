<?php
date_default_timezone_set('Asia/Makassar'); // WITA

/* ============================================================
   MODE PREVIEW
   true  = tampilan saja (data contoh disimpan sementara di sesi
           browser, jadi tambah / edit / hapus terasa nyata)
   false = terhubung database (butuh login admin + tabel siap)
   ============================================================ */
const MODE_PREVIEW = true;

const PER_HALAMAN    = 10;   // jumlah kategori per halaman
const MAKS_NAMA      = 60;   // panjang maksimal nama kategori
const MAKS_DESKRIPSI = 255;  // panjang maksimal deskripsi

session_start();

/* ---------- Helper umum ---------- */
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

function jalankan($conn, $sql, $types = '', ...$params) {
    $stmt = mysqli_prepare($conn, $sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function url_kat($halaman, $tambah = []) {
    return 'kategori-barang.php?' . http_build_query(array_merge(['hal' => $halaman], $tambah));
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

/* ---------- Data contoh (mode preview) ---------- */
function data_contoh() {
    $isi = [
        ['Makanan & sembako',    82, 'Makanan yang layak konsumsi disertai dengan note alergen dan waktu expired.'],
        ['Pakaian',              66, 'Pakaian layak pakai dalam kondisi bersih dan tidak sobek.'],
        ['Buku & alat tulis',    51, 'Buku bacaan, buku pelajaran, dan perlengkapan alat tulis.'],
        ['Elektronik',           40, 'Perangkat elektronik yang masih berfungsi dengan baik.'],
        ['Perabot rumah',        31, 'Perabot dan perlengkapan rumah tangga seperti meja, kursi, dan lampu.'],
        ['Mainan anak',          20, 'Mainan anak yang aman dan tidak berbahaya.'],
        ['Peralatan dapur',      17, 'Panci, piring, gelas, dan perlengkapan memasak lainnya.'],
        ['Perlengkapan bayi',    14, 'Perlengkapan bayi yang bersih dan aman dipakai kembali.'],
        ['Olahraga',             12, 'Perlengkapan olahraga dalam kondisi baik.'],
        ['Tas & sepatu',         11, 'Tas dan sepatu layak pakai.'],
        ['Alat musik',            6, 'Alat musik yang masih bisa dimainkan.'],
        ['Tanaman & berkebun',    5, 'Tanaman, pot, dan peralatan berkebun.'],
        ['Kesehatan',             3, 'Alat bantu kesehatan non-medis, seperti tongkat atau kursi roda.'],
        ['Otomotif',              0, 'Aksesori dan suku cadang kendaraan.'],
    ];
    $hasil = [];
    foreach ($isi as $i => $baris) {
        $hasil[] = [
            'id'        => $i + 1,
            'nama'      => $baris[0],
            'deskripsi' => $baris[2],
            'jumlah'    => $baris[1],
        ];
    }
    return $hasil;
}

/* ---------- Akses data kategori (preview = sesi, selain itu = database) ---------- */
function kat_total($conn) {
    if (MODE_PREVIEW) {
        return count($_SESSION['kat_contoh']);
    }
    return (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM kategori")['n'];
}

function kat_halaman($conn, $offset, $batas) {
    if (MODE_PREVIEW) {
        return array_slice($_SESSION['kat_contoh'], $offset, $batas);
    }
    return ambil_semua(
        $conn,
        "SELECT k.id, k.nama, COALESCE(k.deskripsi, '') AS deskripsi,
                (SELECT COUNT(*) FROM items i WHERE i.kategori_id = k.id) AS jumlah
         FROM kategori k
         ORDER BY k.id ASC
         LIMIT ? OFFSET ?",
        "ii", $batas, $offset
    );
}

function kat_cari($conn, $id) {
    if (MODE_PREVIEW) {
        foreach ($_SESSION['kat_contoh'] as $k) {
            if ((int) $k['id'] === (int) $id) {
                return $k;
            }
        }
        return null;
    }
    return ambil_satu(
        $conn,
        "SELECT id, nama, COALESCE(deskripsi, '') AS deskripsi,
                (SELECT COUNT(*) FROM items i WHERE i.kategori_id = kategori.id) AS jumlah
         FROM kategori WHERE id = ?",
        "i", $id
    );
}

// apakah nama sudah dipakai kategori lain (tanpa membedakan huruf besar/kecil)
function kat_nama_ada($conn, $nama, $kecuali_id = 0) {
    if (MODE_PREVIEW) {
        foreach ($_SESSION['kat_contoh'] as $k) {
            if ((int) $k['id'] !== (int) $kecuali_id && mb_strtolower($k['nama']) === mb_strtolower($nama)) {
                return true;
            }
        }
        return false;
    }
    return (bool) ambil_satu(
        $conn,
        "SELECT id FROM kategori WHERE LOWER(nama) = LOWER(?) AND id <> ?",
        "si", $nama, $kecuali_id
    );
}

function kat_tambah($conn, $nama, $deskripsi) {
    if (MODE_PREVIEW) {
        $maks = 0;
        foreach ($_SESSION['kat_contoh'] as $k) {
            $maks = max($maks, (int) $k['id']);
        }
        $_SESSION['kat_contoh'][] = [
            'id' => $maks + 1, 'nama' => $nama, 'deskripsi' => $deskripsi, 'jumlah' => 0,
        ];
        return;
    }
    jalankan($conn, "INSERT INTO kategori (nama, deskripsi) VALUES (?, ?)", "ss", $nama, $deskripsi);
}

function kat_ubah($conn, $id, $nama, $deskripsi) {
    if (MODE_PREVIEW) {
        foreach ($_SESSION['kat_contoh'] as $i => $k) {
            if ((int) $k['id'] === (int) $id) {
                $_SESSION['kat_contoh'][$i]['nama']      = $nama;
                $_SESSION['kat_contoh'][$i]['deskripsi'] = $deskripsi;
            }
        }
        return;
    }
    jalankan($conn, "UPDATE kategori SET nama = ?, deskripsi = ? WHERE id = ?", "ssi", $nama, $deskripsi, $id);
}

// jumlah barang yang masih memakai kategori ini
function kat_jumlah_barang($conn, $id) {
    if (MODE_PREVIEW) {
        $k = kat_cari($conn, $id);
        return $k ? (int) $k['jumlah'] : 0;
    }
    return (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM items WHERE kategori_id = ?", "i", $id)['n'];
}

function kat_hapus($conn, $id) {
    if (MODE_PREVIEW) {
        $_SESSION['kat_contoh'] = array_values(array_filter($_SESSION['kat_contoh'], function ($k) use ($id) {
            return (int) $k['id'] !== (int) $id;
        }));
        return;
    }
    jalankan($conn, "DELETE FROM kategori WHERE id = ?", "i", $id);
}

/* ---------- Autentikasi admin ---------- */
$conn = null;
if (MODE_PREVIEW) {
    $csrf = '';
    if (isset($_GET['reset'])) {
        unset($_SESSION['kat_contoh']);
        header("Location: kategori-barang.php");
        exit;
    }
    if (!isset($_SESSION['kat_contoh'])) {
        $_SESSION['kat_contoh'] = data_contoh();
    }
} else {
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

/* ---------- Parameter halaman ---------- */
$halaman_ini = max(1, (int) ($_GET['hal'] ?? 1));
$id_edit     = (int) ($_GET['edit'] ?? 0);
$ok          = $_GET['ok'] ?? '';
$gagal       = $_GET['gagal'] ?? '';
$gagal_n     = (int) ($_GET['n'] ?? 0);
$konfirmasi_id = (int) ($_GET['hapus'] ?? 0);
$error       = [];
$input       = ['nama' => '', 'deskripsi' => ''];

/* ---------- Proses tambah / ubah / hapus ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];
    $id   = (int) ($_POST['id'] ?? 0);

    if (!MODE_PREVIEW && !hash_equals($csrf, $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die("Permintaan tidak valid.");
    }

    // hapus (hanya jika tidak ada barang yang memakai kategori ini)
    if ($aksi === 'hapus' && $id > 0) {
        if (kat_cari($conn, $id)) {
            $dipakai = kat_jumlah_barang($conn, $id);
            if ($dipakai > 0) {
                header("Location: " . url_kat($halaman_ini, ['gagal' => 'dipakai', 'n' => $dipakai]));
                exit;
            }
            kat_hapus($conn, $id);
        }
        header("Location: " . url_kat($halaman_ini, ['ok' => 'hapus']));
        exit;
    }

    // tambah / ubah
    if (in_array($aksi, ['tambah', 'ubah'], true)) {
        $input['nama']      = trim($_POST['nama'] ?? '');
        $input['deskripsi'] = trim($_POST['deskripsi'] ?? '');

        if ($input['nama'] === '') {
            $error[] = "Nama kategori wajib diisi.";
        } elseif (mb_strlen($input['nama']) > MAKS_NAMA) {
            $error[] = "Nama kategori maksimal " . MAKS_NAMA . " karakter.";
        } elseif (kat_nama_ada($conn, $input['nama'], $aksi === 'ubah' ? $id : 0)) {
            $error[] = "Nama kategori sudah dipakai.";
        }
        if (mb_strlen($input['deskripsi']) > MAKS_DESKRIPSI) {
            $error[] = "Deskripsi maksimal " . MAKS_DESKRIPSI . " karakter.";
        }
        if ($aksi === 'ubah' && !kat_cari($conn, $id)) {
            $error[] = "Kategori tidak ditemukan.";
        }

        if (empty($error)) {
            if ($aksi === 'tambah') {
                kat_tambah($conn, $input['nama'], $input['deskripsi']);
                // kategori baru muncul paling bawah, jadi buka halaman terakhir
                $halaman_akhir = max(1, (int) ceil(kat_total($conn) / PER_HALAMAN));
                header("Location: " . url_kat($halaman_akhir, ['ok' => 'tambah']));
            } else {
                kat_ubah($conn, $id, $input['nama'], $input['deskripsi']);
                header("Location: " . url_kat($halaman_ini, ['ok' => 'ubah']));
            }
            exit;
        }

        if ($aksi === 'ubah') {
            $id_edit = $id;
        }
    }
}

/* ---------- Mode form: tambah atau edit ---------- */
$kat_edit  = $id_edit > 0 ? kat_cari($conn, $id_edit) : null;
$mode_edit = ($kat_edit !== null);
if ($mode_edit && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $input = ['nama' => $kat_edit['nama'], 'deskripsi' => $kat_edit['deskripsi']];
}

/* ---------- Ambil data tabel ---------- */
$total         = kat_total($conn);
$total_halaman = max(1, (int) ceil($total / PER_HALAMAN));
$halaman_ini   = min($halaman_ini, $total_halaman);
$offset        = ($halaman_ini - 1) * PER_HALAMAN;
$kategori      = kat_halaman($conn, $offset, PER_HALAMAN);

/* ---------- Tampilan ---------- */
$pesan_ok = [
    'tambah' => 'Kategori berhasil ditambahkan.',
    'ubah'   => 'Kategori berhasil diperbarui.',
    'hapus'  => 'Kategori berhasil dihapus.',
];

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
$menu_aktif = 'kategori';

$url_form = url_kat($halaman_ini, $mode_edit ? ['edit' => $kat_edit['id']] : []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Barang - ShareSpace</title>
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
        #form, tr[id^="k"] { scroll-margin-top: 90px; }

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

            <!-- Judul -->
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">Kategori Barang</h1>
            <p class="text-sm text-slate-500 mt-2">Atur kategori barang yang akan digunakan oleh user</p>

            <?php if (isset($pesan_ok[$ok])): ?>
                <div class="mt-5 bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3">
                    <?= e($pesan_ok[$ok]); ?>
                </div>
            <?php endif; ?>

            <?php if ($gagal === 'dipakai'): ?>
                <div class="mt-5 bg-red-50 text-red-700 text-sm rounded-xl px-4 py-3">
                    Kategori tidak bisa dihapus karena masih dipakai <?= $gagal_n; ?> barang.
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)] gap-6 items-start mt-6">

                <!-- ============ TABEL KATEGORI ============ -->
                <div class="bg-white rounded-2xl border border-slate-200 overflow-x-auto">
                    <table class="w-full text-left min-w-[440px]">
                        <thead>
                            <tr class="text-xs font-semibold text-slate-500">
                                <th class="px-5 py-4">Nama Kategori</th>
                                <th class="px-3 py-4">Jumlah Barang</th>
                                <th class="px-3 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($kategori) === 0): ?>
                                <tr>
                                    <td colspan="3" class="px-5 py-10 text-center text-sm text-slate-400 border-t border-slate-100">Belum ada kategori.</td>
                                </tr>
                            <?php endif; ?>

                            <?php foreach ($kategori as $k):
                                $sedang_edit       = $mode_edit && (int) $kat_edit['id'] === (int) $k['id'];
                                $sedang_konfirmasi = ($konfirmasi_id === (int) $k['id']);
                            ?>
                                <tr id="k<?= (int) $k['id']; ?>" class="border-t border-slate-100 <?= $sedang_edit ? 'bg-blue-50/60' : ''; ?>">
                                    <td class="px-5 py-4 text-sm font-bold text-slate-800"><?= e($k['nama']); ?></td>
                                    <td class="px-3 py-4 text-sm text-slate-700"><?= number_format((int) $k['jumlah'], 0, ',', '.'); ?></td>
                                    <td class="px-3 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="<?= e(url_kat($halaman_ini, ['edit' => $k['id']]) . '#form'); ?>"
                                               class="px-3 py-1 rounded-md border border-slate-300 text-xs text-slate-500 hover:bg-blue-600 hover:border-blue-600 hover:text-white">Edit</a>
                                            <a href="<?= e(url_kat($halaman_ini, ['hapus' => $k['id']]) . '#k' . (int) $k['id']); ?>"
                                               class="px-3 py-1 rounded-md border border-slate-300 text-xs text-slate-500 hover:bg-red-500 hover:border-red-500 hover:text-white">Hapus</a>
                                        </div>
                                    </td>
                                </tr>

                                <?php if ($sedang_konfirmasi): ?>
                                    <tr>
                                        <td colspan="3" class="p-0">
                                            <form method="POST" action="<?= e(url_kat($halaman_ini)); ?>"
                                                  class="flex items-center justify-between gap-4 px-5 py-3 border-y bg-red-200 border-red-400 text-red-950">
                                                <input type="hidden" name="id" value="<?= (int) $k['id']; ?>">
                                                <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                                                <p class="text-sm font-semibold">Anda yakin menghapus kategori <?= e($k['nama']); ?>?</p>
                                                <div class="flex items-center gap-4 shrink-0">
                                                    <button type="submit" name="aksi" value="hapus" title="Ya, hapus" class="text-slate-800 hover:text-green-700">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                                                    </button>
                                                    <a href="<?= e(url_kat($halaman_ini)); ?>" title="Batal" class="text-slate-800 hover:text-red-700">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
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

                <!-- ============ FORM TAMBAH / EDIT ============ -->
                <section id="form" class="bg-white rounded-2xl border border-slate-200 p-6">
                    <h2 class="text-base font-bold text-slate-900"><?= $mode_edit ? 'Edit Kategori' : 'Tambah Kategori'; ?></h2>

                    <?php if (!empty($error)): ?>
                        <div class="mt-3 bg-red-50 text-red-700 text-xs rounded-lg p-3 space-y-1">
                            <?php foreach ($error as $pesan): ?>
                                <p><?= e($pesan); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= e($url_form); ?>" class="mt-4">
                        <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                        <?php if ($mode_edit): ?>
                            <input type="hidden" name="id" value="<?= (int) $kat_edit['id']; ?>">
                        <?php endif; ?>

                        <label for="nama" class="block text-xs font-semibold text-slate-800 mb-1.5">Nama Kategori</label>
                        <input type="text" id="nama" name="nama" maxlength="<?= MAKS_NAMA; ?>" required
                               value="<?= e($input['nama']); ?>" placeholder="Masukkan nama kategori"
                               class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:border-blue-500">

                        <label for="deskripsi" class="block text-xs font-semibold text-slate-800 mt-4 mb-1.5">Deskripsi Kategori</label>
                        <textarea id="deskripsi" name="deskripsi" rows="5" maxlength="<?= MAKS_DESKRIPSI; ?>"
                                  placeholder="Masukkan penjelasan singkat tentang kategori"
                                  class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:border-blue-500"><?= e($input['deskripsi']); ?></textarea>

                        <?php if ($mode_edit): ?>
                            <button type="submit" name="aksi" value="ubah"
                                    class="w-full mt-5 py-3 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Edit Kategori</button>
                            <a href="<?= e(url_kat($halaman_ini)); ?>"
                               class="block w-full mt-3 py-3 rounded-lg border border-blue-500 bg-white text-center text-sm font-medium text-slate-700 hover:bg-blue-50">Batal</a>
                        <?php else: ?>
                            <button type="submit" name="aksi" value="tambah"
                                    class="w-full mt-5 py-3 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Tambah Kategori</button>
                        <?php endif; ?>
                    </form>
                </section>
            </div>

            <!-- Pagination -->
            <div class="flex flex-wrap items-center justify-between gap-3 mt-6 px-1">
                <p class="text-xs font-semibold text-slate-500">Menampilkan <?= count($kategori); ?> dari <?= number_format($total, 0, ',', '.'); ?></p>

                <div class="flex items-center gap-2 text-xs">
                    <?php if ($halaman_ini > 1): ?>
                        <a href="<?= e(url_kat($halaman_ini - 1)); ?>" class="px-3 h-8 flex items-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">Sebelumnya</a>
                    <?php else: ?>
                        <span class="px-3 h-8 flex items-center rounded-md border border-slate-200 bg-white text-slate-300 cursor-not-allowed">Sebelumnya</span>
                    <?php endif; ?>

                    <?php foreach (daftar_halaman($halaman_ini, $total_halaman) as $n): ?>
                        <?php if ($n === '...'): ?>
                            <span class="min-w-[2rem] h-8 px-2 flex items-center justify-center rounded-md border border-slate-300 bg-white text-slate-500">...</span>
                        <?php else: ?>
                            <a href="<?= e(url_kat($n)); ?>"
                               class="min-w-[2rem] h-8 px-2 flex items-center justify-center rounded-md border <?= $n === $halaman_ini ? 'border-blue-500 bg-blue-50 text-blue-600 font-semibold' : 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50'; ?>">
                                <?= $n; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if ($halaman_ini < $total_halaman): ?>
                        <a href="<?= e(url_kat($halaman_ini + 1)); ?>" class="px-3 h-8 flex items-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">Selanjutnya</a>
                    <?php else: ?>
                        <span class="px-3 h-8 flex items-center rounded-md border border-slate-200 bg-white text-slate-300 cursor-not-allowed">Selanjutnya</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (MODE_PREVIEW): ?>
                <p class="mt-8 text-[11px] text-slate-400">
                    Mode preview: perubahan disimpan sementara di sesi browser, bukan di database.
                    <a href="kategori-barang.php?reset=1" class="text-blue-600 hover:underline">Kembalikan data contoh</a>
                </p>
            <?php endif; ?>
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
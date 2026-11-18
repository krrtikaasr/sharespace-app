<?php
/* ============================================================
   unggah-barang.php  -  Tambah Donasi (sisi user)
   Butuh: config/database.php ($conn = mysqli), tabel users, items,
   item_foto (lihat unggah-barang.sql). Folder uploads/items/ otomatis dibuat.
   ============================================================ */
date_default_timezone_set('Asia/Makassar'); // WITA
session_start();
require_once 'config/database.php';

const MAKS_FOTO   = 5;
const MAKS_UKURAN = 5 * 1024 * 1024; // 5 MB per foto
const FOLDER_FOTO = 'uploads/items/';

/* Pilihan form (ubah sesuai kebutuhan) */
$daftar_lokasi = ['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Bangli', 'Klungkung', 'Karangasem', 'Buleleng', 'Jembrana'];
$daftar_kondisi = ['Baru', 'Seperti baru', 'Layak pakai'];
$daftar_waktu = [
    'Atur bersama melalui chat',
    'Pagi (08.00 - 12.00)',
    'Siang - sore (12.00 - 17.00)',
    'Malam (17.00 - 21.00)',
    'Akhir pekan',
];
$daftar_metode = [
    'langsung' => ['Diambil langsung oleh penerima', 'Bertemu di lokasi yang kamu tentukan.'],
];

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
    $res  = mysqli_stmt_get_result($stmt);
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

function panjang($teks) {
    return function_exists('mb_strlen') ? mb_strlen($teks, 'UTF-8') : strlen($teks);
}

function ikon($nama, $kelas = 'w-5 h-5') {
    static $path = [
        'beranda'   => '<path d="M3 11l9-8 9 8v10a1 1 0 01-1 1h-5v-7H9v7H4a1 1 0 01-1-1V11z"/>',
        'jelajahi'  => '<circle cx="12" cy="12" r="9"/><path d="M15.5 8.5l-2 5-5 2 2-5 5-2z"/>',
        'bagikan'   => '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>',
        'aktivitas' => '<path d="M3 13l3-8h12l3 8v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z"/><path d="M3 13h5l1 3h6l1-3h5"/>',
        'profil'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>',
        'kamera'    => '<path d="M4 8h3l2-3h6l2 3h3a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V9a1 1 0 011-1z"/><circle cx="12" cy="13" r="3.5"/>',
        'pin'       => '<path d="M12 21s7-6.2 7-11.5A7 7 0 005 9.5C5 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
        'kanan'     => '<path d="M9 6l6 6-6 6"/>',
        'bawah'     => '<path d="M6 9l6 6 6-6"/>',
        'minus'     => '<path d="M5 12h14"/>',
        'plus'      => '<path d="M12 5v14M5 12h14"/>',
        'panah'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'tutup'     => '<path d="M6 6l12 12M18 6L6 18"/>',
        'centang'   => '<path d="M5 13l4 4L19 7"/>',
        'logo'      => '<path d="M11 14h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 16"/><path d="m7 20 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9"/><path d="m2 15 6 6"/><path d="M19.5 8.5c.7-.7 1.5-1.6 1.5-2.7A2.73 2.73 0 0 0 16 4a2.78 2.78 0 0 0-5 1.8c0 1.2.8 2 1.5 2.8L16 12Z"/>',
    ];
    return '<svg class="' . $kelas . '" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">' . ($path[$nama] ?? '') . '</svg>';
}

function galat($g, $kunci) {
    return isset($g[$kunci]) ? '<p class="text-xs text-red-600 mt-1.5">' . e($g[$kunci]) . '</p>' : '';
}

function kelas_input($g, $kunci) {
    $dasar = 'w-full px-3.5 rounded-xl border bg-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2';
    return $dasar . (isset($g[$kunci]) ? ' border-red-400 focus:border-red-500 focus:ring-red-100' : ' border-slate-200 focus:border-blue-500 focus:ring-blue-100');
}

/* ---------- Autentikasi user ---------- */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
$user_id = (int) $_SESSION['user_id'];

$user = ambil_satu($conn, "SELECT nama, COALESCE(status, 'aktif') AS status FROM users WHERE id = ?", "i", $user_id);
if (!$user) {
    header("Location: index.php");
    exit;
}
if ($user['status'] === 'diblokir') {
    http_response_code(403);
    die("Akun Anda diblokir, sehingga tidak bisa menambah donasi.");
}
$csrf = $_SESSION['csrf'] ??= bin2hex(random_bytes(16));

/* Daftar kategori: dari tabel kategori (sesuaikan nama tabel/kolom), cadangan bila kosong */
$daftar_kategori = [];
try {
    foreach (ambil_semua($conn, "SELECT nama FROM kategori ORDER BY nama") as $r) {
        $daftar_kategori[] = $r['nama'];
    }
} catch (Throwable $ex) {
    $daftar_kategori = [];
}
if (!$daftar_kategori) {
    $daftar_kategori = ['Pakaian', 'Buku', 'Elektronik', 'Perabot', 'Mainan', 'Peralatan Dapur', 'Lainnya'];
}

/* ---------- Nilai awal form ---------- */
$lama = [
    'nama' => '', 'kategori' => '', 'kondisi' => '', 'jumlah' => 1, 'deskripsi' => '',
    'lokasi' => '', 'alamat' => '', 'metode' => 'langsung', 'waktu' => $daftar_waktu[0],
    'catatan' => '', 'konfirmasi' => '',
];
$galat = [];

/* ---------- Layar sukses (sekali tampil) ---------- */
$sukses = !empty($_SESSION['sukses']);
unset($_SESSION['sukses']);

/* ---------- Proses simpan donasi ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Total unggahan melebihi post_max_size -> PHP mengosongkan $_POST
    if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        $galat['umum'] = 'Ukuran total foto terlalu besar. Kurangi jumlah atau ukuran foto.';
    } elseif (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die("Permintaan tidak valid.");
    } else {
        foreach (array_keys($lama) as $k) {
            $lama[$k] = trim((string) ($_POST[$k] ?? $lama[$k]));
        }
        $lama['jumlah'] = (int) $lama['jumlah'];

        // Validasi teks
        $n = panjang($lama['nama']);
        if ($n < 3 || $n > 100) $galat['nama'] = 'Nama barang 3 - 100 karakter.';
        if (!in_array($lama['kategori'], $daftar_kategori, true)) $galat['kategori'] = 'Pilih kategori barang.';
        if (!in_array($lama['kondisi'], $daftar_kondisi, true)) $galat['kondisi'] = 'Pilih kondisi barang.';
        if ($lama['jumlah'] < 1 || $lama['jumlah'] > 99) $galat['jumlah'] = 'Jumlah 1 - 99 unit.';
        $n = panjang($lama['deskripsi']);
        if ($n < 10 || $n > 500) $galat['deskripsi'] = 'Deskripsi 10 - 500 karakter.';
        if (!in_array($lama['lokasi'], $daftar_lokasi, true)) $galat['lokasi'] = 'Pilih kecamatan atau kota.';
        $n = panjang($lama['alamat']);
        if ($n < 5 || $n > 255) $galat['alamat'] = 'Alamat pengambilan 5 - 255 karakter.';
        if (!isset($daftar_metode[$lama['metode']])) $galat['metode'] = 'Pilih metode pengambilan.';
        if (!in_array($lama['waktu'], $daftar_waktu, true)) $galat['waktu'] = 'Pilih waktu pengambilan.';
        if (panjang($lama['catatan']) > 200) $galat['catatan'] = 'Catatan maksimal 200 karakter.';
        if ($lama['konfirmasi'] === '') $galat['konfirmasi'] = 'Centang pernyataan ini untuk melanjutkan.';

        // Validasi foto
        $foto = [];
        $f = $_FILES['foto'] ?? null;
        if ($f && is_array($f['name'])) {
            foreach ($f['name'] as $i => $nama_asli) {
                if ($f['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                $foto[] = ['tmp' => $f['tmp_name'][$i], 'err' => $f['error'][$i], 'ukuran' => $f['size'][$i]];
            }
        }
        if (count($foto) === 0) {
            $galat['foto'] = 'Tambahkan minimal 1 foto barang.';
        } elseif (count($foto) > MAKS_FOTO) {
            $galat['foto'] = 'Maksimal ' . MAKS_FOTO . ' foto.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            foreach ($foto as $i => $ft) {
                if ($ft['err'] !== UPLOAD_ERR_OK || !is_uploaded_file($ft['tmp'])) {
                    $galat['foto'] = 'Salah satu foto gagal diunggah. Coba lagi.';
                    break;
                }
                if ($ft['ukuran'] > MAKS_UKURAN) {
                    $galat['foto'] = 'Ukuran tiap foto maksimal 5 MB.';
                    break;
                }
                $mime = $finfo->file($ft['tmp']);
                if (!in_array($mime, ['image/jpeg', 'image/png'], true) || !@getimagesize($ft['tmp'])) {
                    $galat['foto'] = 'Foto harus berformat JPG atau PNG.';
                    break;
                }
                $foto[$i]['ext'] = ($mime === 'image/png') ? 'png' : 'jpg';
            }
        }

        // Simpan
        if (!$galat) {
            $tersimpan = [];
            try {
                $folder = __DIR__ . '/' . FOLDER_FOTO;
                if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
                    throw new RuntimeException('Folder foto gagal dibuat');
                }
                foreach ($foto as $ft) {
                    $path = FOLDER_FOTO . bin2hex(random_bytes(12)) . '.' . $ft['ext'];
                    if (!move_uploaded_file($ft['tmp'], __DIR__ . '/' . $path)) {
                        throw new RuntimeException('Foto gagal disimpan');
                    }
                    $tersimpan[] = $path;
                }

                mysqli_begin_transaction($conn);

                $catatan = ($lama['catatan'] === '') ? null : $lama['catatan'];
                $cover   = $tersimpan[0];
                $stmt = mysqli_prepare($conn,
                    "INSERT INTO items (user_id, nama_barang, kategori, kondisi, jumlah, deskripsi, lokasi, alamat, metode, waktu_pengambilan, catatan, foto, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'tersedia')"
                );
                mysqli_stmt_bind_param($stmt, "isssisssssss",
                    $user_id, $lama['nama'], $lama['kategori'], $lama['kondisi'], $lama['jumlah'],
                    $lama['deskripsi'], $lama['lokasi'], $lama['alamat'], $lama['metode'], $lama['waktu'],
                    $catatan, $cover
                );
                mysqli_stmt_execute($stmt);
                $item_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);

                $stmt = mysqli_prepare($conn, "INSERT INTO item_foto (item_id, path, urutan) VALUES (?, ?, ?)");
                foreach ($tersimpan as $urutan => $path) {
                    mysqli_stmt_bind_param($stmt, "isi", $item_id, $path, $urutan);
                    mysqli_stmt_execute($stmt);
                }
                mysqli_stmt_close($stmt);

                mysqli_commit($conn);

                $_SESSION['sukses'] = true;
                header("Location: unggah-barang.php");
                exit;
            } catch (Throwable $ex) {
                @mysqli_rollback($conn);
                foreach ($tersimpan as $path) {
                    @unlink(__DIR__ . '/' . $path);
                }
                $galat['umum'] = 'Donasi gagal disimpan. Silakan coba lagi.';
            }
        }
    }
}

/* ---------- Tampilan ---------- */
$menu = [
    ['kunci' => 'beranda',   'label' => 'Beranda',   'url' => 'beranda.php'],
    ['kunci' => 'jelajahi',  'label' => 'Jelajahi',  'url' => 'jelajahi.php'],
    ['kunci' => 'bagikan',   'label' => 'Bagikan',   'url' => 'unggah-barang.php'],
    ['kunci' => 'aktivitas', 'label' => 'Aktivitas', 'url' => 'aktivitas.php'],
    ['kunci' => 'profil',    'label' => 'Profil',    'url' => 'profil.php'],
];
$menu_aktif = 'bagikan';
$label = 'block text-xs font-semibold text-slate-800 mb-1.5';
$bintang = '<span class="text-blue-600">*</span>';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Tambah Donasi - ShareSpace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] } } } };</script>
    <style>input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button{-webkit-appearance:none;margin:0}input[type=number]{-moz-appearance:textfield}</style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased">

    <!-- Sidebar (desktop) -->
    <aside class="hidden lg:flex fixed inset-y-0 left-0 w-64 flex-col bg-white border-r border-slate-200 px-6 py-7">
        <a href="beranda.php" class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center"><?= ikon('logo', 'w-6 h-6'); ?></span>
            <span class="text-xl font-bold text-slate-900">ShareSpace</span>
        </a>
        <p class="text-xs font-medium text-slate-500 mt-10 mb-3 px-1">MENU</p>
        <nav class="space-y-1">
            <?php foreach ($menu as $m): $aktif = ($m['kunci'] === $menu_aktif); ?>
                <a href="<?= e($m['url']); ?>" class="flex items-center gap-3 px-1.5 py-2 text-sm font-medium <?= $aktif ? 'text-blue-600' : 'text-slate-600 hover:text-slate-900'; ?>">
                    <?= ikon($m['kunci']); ?><?= e($m['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="mt-auto flex items-center gap-3 px-1">
            <span class="w-9 h-9 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold flex items-center justify-center"><?= e(inisial($user['nama'])); ?></span>
            <span class="text-sm font-semibold text-slate-800 truncate"><?= e($user['nama']); ?></span>
        </div>
    </aside>

    <!-- Header (mobile) -->
    <header class="lg:hidden sticky top-0 z-20 bg-white border-b border-slate-200 h-14 px-4 flex items-center justify-between" style="padding-top:env(safe-area-inset-top)">
        <a href="beranda.php" class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center"><?= ikon('logo', 'w-5 h-5'); ?></span>
            <span class="text-lg font-bold text-slate-900">ShareSpace</span>
        </a>
        <span class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 text-[11px] font-semibold flex items-center justify-center"><?= e(inisial($user['nama'])); ?></span>
    </header>

    <main class="lg:ml-64 px-4 sm:px-8 lg:px-14 py-6 lg:py-10 pb-28 lg:pb-12">

    <?php if ($sukses): ?>
        <!-- ===== Layar sukses ===== -->
        <div class="min-h-[65vh] flex flex-col items-center justify-center text-center">
            <span class="w-[72px] h-[72px] rounded-full bg-blue-600 text-white flex items-center justify-center">
                <svg class="w-9 h-9" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>
            </span>
            <p class="mt-5 text-lg text-slate-500">Donasi telah ditambahkan!</p>
            <div class="flex flex-wrap justify-center gap-3 mt-6">
                <a href="aktivitas.php" class="px-5 h-10 inline-flex items-center rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Lihat aktivitas</a>
                <a href="unggah-barang.php" class="px-5 h-10 inline-flex items-center rounded-xl border border-slate-300 bg-white text-sm font-semibold text-slate-600 hover:bg-slate-50">Tambah lagi</a>
            </div>
        </div>

    <?php else: ?>
        <!-- ===== Form ===== -->
        <div class="max-w-5xl">
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">Tambah Donasi</h1>
            <p class="text-sm text-slate-500 mt-2">Beri barang tak terpakai kehidupan baru. Lengkapi detailnya agar mudah ditemukan.</p>
            <p class="text-sm text-slate-500">* Wajib diisi</p>

            <?php if (isset($galat['umum'])): ?>
                <div class="mt-5 bg-red-50 text-red-700 text-sm rounded-xl px-4 py-3"><?= e($galat['umum']); ?></div>
            <?php elseif ($galat): ?>
                <div class="mt-5 bg-red-50 text-red-700 text-sm rounded-xl px-4 py-3">Beberapa isian belum sesuai. Periksa kembali form di bawah.</div>
            <?php endif; ?>

            <form id="form-donasi" method="POST" action="unggah-barang.php" enctype="multipart/form-data" class="mt-6 grid lg:grid-cols-2 gap-6 items-start">
                <input type="hidden" name="csrf" value="<?= e($csrf); ?>">

                <!-- Kartu kiri: foto & detail -->
                <section class="bg-white rounded-3xl border border-slate-300 p-5 sm:p-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-slate-800">Foto barang <?= $bintang; ?></h2>
                        <span id="hitung-foto" class="text-sm text-slate-500">0/<?= MAKS_FOTO; ?> foto</span>
                    </div>

                    <input id="foto" name="foto[]" type="file" accept="image/jpeg,image/png" multiple class="sr-only">

                    <label id="zona-kosong" for="foto" class="mt-3 block cursor-pointer rounded-2xl border border-dashed <?= isset($galat['foto']) ? 'border-red-400' : 'border-slate-300'; ?> py-8 text-center hover:bg-blue-50/40">
                        <span class="w-10 h-10 mx-auto rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><?= ikon('kamera'); ?></span>
                        <span class="block mt-2 text-sm font-semibold text-blue-600">Tambah foto</span>
                        <span class="block mt-1 text-xs text-slate-500">JPG atau PNG &middot; Maks. 5 MB per foto</span>
                    </label>

                    <div id="grid-foto" class="hidden mt-3 grid grid-cols-3 gap-3"></div>
                    <p id="galat-foto-js" class="text-xs text-red-600 mt-1.5 hidden"></p>
                    <?= galat($galat, 'foto'); ?>
                    <p class="text-xs text-slate-500 mt-3">Gunakan foto asli yang memperlihatkan kondisi barang.</p>

                    <h2 class="text-lg font-bold text-slate-900 mt-7">Detail barang</h2>

                    <div class="mt-3">
                        <label for="nama" class="<?= $label; ?>">Nama barang <?= $bintang; ?></label>
                        <input id="nama" name="nama" type="text" maxlength="100" value="<?= e($lama['nama']); ?>" placeholder="Contoh: Lampu meja kayu" class="h-11 <?= kelas_input($galat, 'nama'); ?>">
                        <?= galat($galat, 'nama'); ?>
                    </div>

                    <div class="mt-4">
                        <label for="kategori" class="<?= $label; ?>">Kategori <?= $bintang; ?></label>
                        <div class="relative">
                            <select id="kategori" name="kategori" class="h-11 appearance-none pr-10 <?= kelas_input($galat, 'kategori'); ?> <?= $lama['kategori'] === '' ? 'text-slate-400' : ''; ?>">
                                <option value="">Pilih kategori barang</option>
                                <?php foreach ($daftar_kategori as $k): ?>
                                    <option value="<?= e($k); ?>" <?= $lama['kategori'] === $k ? 'selected' : ''; ?>><?= e($k); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500"><?= ikon('bawah', 'w-4 h-4'); ?></span>
                        </div>
                        <?= galat($galat, 'kategori'); ?>
                    </div>

                    <div class="mt-4">
                        <span class="<?= $label; ?>">Kondisi <?= $bintang; ?></span>
                        <div class="grid grid-cols-3 gap-2">
                            <?php foreach ($daftar_kondisi as $kd): ?>
                                <label class="cursor-pointer">
                                    <input type="radio" name="kondisi" value="<?= e($kd); ?>" class="peer sr-only" <?= $lama['kondisi'] === $kd ? 'checked' : ''; ?>>
                                    <span class="h-10 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-xs text-slate-600 hover:bg-slate-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-600 peer-checked:font-semibold peer-focus-visible:ring-2 peer-focus-visible:ring-blue-200"><?= e($kd); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <?= galat($galat, 'kondisi'); ?>
                    </div>

                    <div class="mt-4">
                        <label for="jumlah" class="<?= $label; ?>">Jumlah <?= $bintang; ?></label>
                        <div class="h-11 flex items-center rounded-xl border <?= isset($galat['jumlah']) ? 'border-red-400' : 'border-slate-200'; ?> bg-white px-3.5">
                            <span class="flex-1 text-sm text-slate-400">Unit barang</span>
                            <button type="button" data-langkah="-1" aria-label="Kurangi" class="w-7 h-7 flex items-center justify-center text-slate-500 hover:text-slate-900"><?= ikon('minus', 'w-4 h-4'); ?></button>
                            <input id="jumlah" name="jumlah" type="number" min="1" max="99" value="<?= (int) $lama['jumlah']; ?>" class="w-10 text-center text-sm font-medium text-slate-800 focus:outline-none bg-transparent">
                            <button type="button" data-langkah="1" aria-label="Tambah" class="w-7 h-7 flex items-center justify-center text-blue-600 hover:text-blue-800"><?= ikon('plus', 'w-4 h-4'); ?></button>
                        </div>
                        <?= galat($galat, 'jumlah'); ?>
                    </div>

                    <div class="mt-4">
                        <label for="deskripsi" class="<?= $label; ?>">Deskripsi <?= $bintang; ?></label>
                        <div class="relative">
                            <textarea id="deskripsi" name="deskripsi" rows="4" maxlength="500" placeholder="Ceritakan kondisi, ukuran, kelengkapan, dan kekurangan barang jika ada." class="py-3 pb-7 resize-none <?= kelas_input($galat, 'deskripsi'); ?>"><?= e($lama['deskripsi']); ?></textarea>
                            <span id="hitung-deskripsi" class="absolute right-3.5 bottom-2.5 text-[10px] text-slate-500"><?= panjang($lama['deskripsi']); ?>/500</span>
                        </div>
                        <?= galat($galat, 'deskripsi'); ?>
                    </div>
                </section>

                <!-- Kartu kanan: lokasi & pengambilan -->
                <section class="bg-white rounded-3xl border border-slate-300 p-5 sm:p-8">
                    <h2 class="text-lg font-bold text-slate-900">Lokasi &amp; pengambilan</h2>

                    <div class="mt-3">
                        <label for="lokasi" class="<?= $label; ?>">Lokasi barang <?= $bintang; ?></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-blue-600"><?= ikon('pin', 'w-4 h-4'); ?></span>
                            <select id="lokasi" name="lokasi" class="h-11 appearance-none pl-10 pr-10 <?= kelas_input($galat, 'lokasi'); ?> <?= $lama['lokasi'] === '' ? 'text-slate-400' : ''; ?>">
                                <option value="">Pilih kecamatan atau kota</option>
                                <?php foreach ($daftar_lokasi as $l): ?>
                                    <option value="<?= e($l); ?>" <?= $lama['lokasi'] === $l ? 'selected' : ''; ?>><?= e($l); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500"><?= ikon('kanan', 'w-4 h-4'); ?></span>
                        </div>
                        <?= galat($galat, 'lokasi'); ?>
                    </div>

                    <div class="mt-4">
                        <label for="alamat" class="<?= $label; ?>">Alamat pengambilan <?= $bintang; ?></label>
                        <input id="alamat" name="alamat" type="text" maxlength="255" value="<?= e($lama['alamat']); ?>" placeholder="Nama jalan, nomor rumah, atau titik temu" class="h-11 <?= kelas_input($galat, 'alamat'); ?>">
                        <?= galat($galat, 'alamat'); ?>
                        <p class="text-[11px] text-slate-500 mt-1.5">Alamat lengkap hanya dibagikan ke penerima yang kamu setujui.</p>
                    </div>

                    <div class="mt-4">
                        <span class="<?= $label; ?>">Metode pengambilan <?= $bintang; ?></span>
                        <?php foreach ($daftar_metode as $kunci => $info): ?>
                            <label class="block cursor-pointer">
                                <input type="radio" name="metode" value="<?= e($kunci); ?>" class="peer sr-only" <?= $lama['metode'] === $kunci ? 'checked' : ''; ?>>
                                <span class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3.5 py-3 peer-checked:border-blue-500 peer-checked:bg-blue-50 [&_i]:peer-checked:border-blue-600 [&_i]:peer-checked:border-[5px]">
                                    <i class="w-4 h-4 shrink-0 rounded-full border-2 border-slate-300 bg-white"></i>
                                    <span>
                                        <span class="block text-xs font-semibold text-blue-700"><?= e($info[0]); ?></span>
                                        <span class="block text-[11px] text-slate-500 mt-0.5"><?= e($info[1]); ?></span>
                                    </span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                        <?= galat($galat, 'metode'); ?>
                    </div>

                    <div class="mt-4">
                        <label for="waktu" class="<?= $label; ?>">Waktu pengambilan <?= $bintang; ?></label>
                        <div class="relative">
                            <select id="waktu" name="waktu" class="h-11 appearance-none pr-10 <?= kelas_input($galat, 'waktu'); ?>">
                                <?php foreach ($daftar_waktu as $w): ?>
                                    <option value="<?= e($w); ?>" <?= $lama['waktu'] === $w ? 'selected' : ''; ?>><?= e($w); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500"><?= ikon('bawah', 'w-4 h-4'); ?></span>
                        </div>
                        <?= galat($galat, 'waktu'); ?>
                    </div>

                    <div class="mt-4">
                        <label for="catatan" class="<?= $label; ?>">Catatan pengambilan (opsional)</label>
                        <input id="catatan" name="catatan" type="text" maxlength="200" value="<?= e($lama['catatan']); ?>" placeholder="Contoh: Bisa diambil setelah pukul 17.00" class="h-11 <?= kelas_input($galat, 'catatan'); ?>">
                        <?= galat($galat, 'catatan'); ?>
                    </div>

                    <label class="flex items-start gap-3 mt-5 cursor-pointer">
                        <input type="checkbox" name="konfirmasi" value="1" class="mt-0.5 w-5 h-5 shrink-0 rounded-md border-slate-300 accent-blue-600" <?= $lama['konfirmasi'] !== '' ? 'checked' : ''; ?>>
                        <span class="text-xs text-slate-500">Saya memastikan barang layak digunakan dan dibagikan secara gratis kepada penerima.</span>
                    </label>
                    <?= galat($galat, 'konfirmasi'); ?>

                    <button id="tombol-kirim" type="submit" class="mt-5 w-full h-12 rounded-xl bg-blue-600 text-white text-sm font-semibold flex items-center justify-center gap-2 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed">
                        <span>Terbitkan donasi</span><?= ikon('panah', 'w-5 h-5'); ?>
                    </button>
                </section>
            </form>
        </div>
    <?php endif; ?>

    </main>

    <!-- Navigasi bawah (mobile) -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-slate-200 grid grid-cols-5" style="padding-bottom:env(safe-area-inset-bottom)">
        <?php foreach ($menu as $m): $aktif = ($m['kunci'] === $menu_aktif); ?>
            <a href="<?= e($m['url']); ?>" class="flex flex-col items-center gap-1 py-2.5 text-[11px] font-medium <?= $aktif ? 'text-blue-600' : 'text-slate-500'; ?>">
                <?= ikon($m['kunci']); ?><?= e($m['label']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

<?php if (!$sukses): ?>
<script>
(function () {
    var MAKS = <?= MAKS_FOTO; ?>, UKURAN = <?= MAKS_UKURAN; ?>;
    var input = document.getElementById('foto');
    var kosong = document.getElementById('zona-kosong');
    var grid = document.getElementById('grid-foto');
    var hitung = document.getElementById('hitung-foto');
    var pesan = document.getElementById('galat-foto-js');
    var berkas = [];

    function tampilkanPesan(teks) {
        pesan.textContent = teks || '';
        pesan.classList.toggle('hidden', !teks);
    }

    function sinkron() {
        var dt = new DataTransfer();
        berkas.forEach(function (f) { dt.items.add(f); });
        input.files = dt.files;
        hitung.textContent = berkas.length + '/' + MAKS + ' foto';
        kosong.classList.toggle('hidden', berkas.length > 0);
        grid.classList.toggle('hidden', berkas.length === 0);
        grid.innerHTML = '';

        berkas.forEach(function (f, i) {
            var kotak = document.createElement('div');
            kotak.className = 'relative aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-100';
            var img = document.createElement('img');
            img.className = 'w-full h-full object-cover';
            img.alt = 'Foto ' + (i + 1);
            img.src = URL.createObjectURL(f);
            img.onload = function () { URL.revokeObjectURL(img.src); };
            var hapus = document.createElement('button');
            hapus.type = 'button';
            hapus.setAttribute('aria-label', 'Hapus foto ' + (i + 1));
            hapus.className = 'absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-slate-900/70 text-white flex items-center justify-center text-sm leading-none';
            hapus.textContent = '\u00d7';
            hapus.onclick = function () { berkas.splice(i, 1); tampilkanPesan(''); sinkron(); };
            kotak.appendChild(img);
            kotak.appendChild(hapus);
            grid.appendChild(kotak);
        });

        if (berkas.length > 0 && berkas.length < MAKS) {
            var tambah = document.createElement('label');
            tambah.htmlFor = 'foto';
            tambah.className = 'aspect-square rounded-xl border border-dashed border-slate-300 flex items-center justify-center text-2xl text-blue-600 cursor-pointer hover:bg-blue-50/40';
            tambah.setAttribute('aria-label', 'Tambah foto');
            tambah.textContent = '+';
            grid.appendChild(tambah);
        }
    }

    input.addEventListener('change', function () {
        var baru = Array.prototype.slice.call(input.files);
        var galat = '';
        baru.forEach(function (f) {
            if (berkas.length >= MAKS) { galat = 'Maksimal ' + MAKS + ' foto.'; return; }
            if (f.type !== 'image/jpeg' && f.type !== 'image/png') { galat = 'Foto harus berformat JPG atau PNG.'; return; }
            if (f.size > UKURAN) { galat = 'Ukuran tiap foto maksimal 5 MB.'; return; }
            berkas.push(f);
        });
        tampilkanPesan(galat);
        sinkron();
    });

    // Stepper jumlah
    var jumlah = document.getElementById('jumlah');
    document.querySelectorAll('[data-langkah]').forEach(function (b) {
        b.addEventListener('click', function () {
            var v = (parseInt(jumlah.value, 10) || 1) + parseInt(b.dataset.langkah, 10);
            jumlah.value = Math.min(99, Math.max(1, v));
        });
    });

    // Penghitung deskripsi
    var desk = document.getElementById('deskripsi');
    var hDesk = document.getElementById('hitung-deskripsi');
    desk.addEventListener('input', function () { hDesk.textContent = desk.value.length + '/500'; });

    // Warna placeholder pada select
    ['kategori', 'lokasi'].forEach(function (id) {
        var s = document.getElementById(id);
        s.addEventListener('change', function () { s.classList.toggle('text-slate-400', s.value === ''); });
    });

    // Cegah kirim ganda
    document.getElementById('form-donasi').addEventListener('submit', function (ev) {
        if (berkas.length === 0) {
            ev.preventDefault();
            tampilkanPesan('Tambahkan minimal 1 foto barang.');
            kosong.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        var t = document.getElementById('tombol-kirim');
        t.disabled = true;
        t.firstElementChild.textContent = 'Menyimpan...';
    });
})();
</script>
<?php endif; ?>
</body>
</html>
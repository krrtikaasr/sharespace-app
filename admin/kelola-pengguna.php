<?php
date_default_timezone_set('Asia/Makassar'); // WITA

/* ============================================================
   MODE PREVIEW
   true  = tampilan saja (data contoh, tanpa login & database)
   false = terhubung database (butuh login admin + tabel siap)
   ============================================================ */
const MODE_PREVIEW = true;

/* Jumlah pengguna per halaman */
const PER_HALAMAN = 10;

/* ---------- Helper ---------- */
function e($teks) {
    return htmlspecialchars((string) $teks, ENT_QUOTES, 'UTF-8');
}

function ambil_semua($conn, $sql,$types = '', ...$params) {$stmt = mysqli_prepare($conn,$sql);
    if ($types !== '') {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);$res = mysqli_stmt_get_result($stmt);$data = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $data[] =$r;
    }
    mysqli_stmt_close($stmt);
    return $data;
}

function ambil_satu($conn, $sql,$types = '', ...$params) {$semua = ambil_semua($conn,$sql, $types, ...$params);
    return $semua[0] ?? null;
}

function inisial($nama) {
    $kata = preg_split('/\s+/', trim($nama));
    if (count($kata) > 1) {
        return strtoupper(substr($kata[0], 0, 1) . substr(end($kata), 0, 1));
    }
    return strtoupper(substr($kata[0] ?? '', 0, 2)) ?: '?';
}

function tgl_panjang($tanggal) {$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $t = strtotime($tanggal);
    if (!$t) return '-';
    return date('j', $t) . ' ' .$bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
}

function url_daftar($filter, $halaman,$tambah = []) {
    return 'kelola-pengguna.php?' . http_build_query(array_merge(['status' => $filter, 'hal' => $halaman],$tambah));
}

// daftar nomor halaman, contoh: [1, 2, '...', 8]
function daftar_halaman($sekarang, $total) {$tampil = [];
    for ($i = 1; $i <= $total; $i++) {
        if ($i === 1 || $i ===$total || abs($i -$sekarang) <= 1) {
            $tampil[] =$i;
        }
    }
    $hasil = [];$sebelumnya = 0;
    foreach ($tampil as$n) {
        if ($n - $sebelumnya > 1) {$hasil[] = '...';
        }
        $hasil[] =$n;
        $sebelumnya =$n;
    }
    return $hasil;
}

// data contoh untuk mode preview
function data_contoh() {
    $nama   = ['Made Wijaya', 'Ni Luh Putri', 'Komang Ayu', 'Kadek Arya', 'Nadya Prameswari', 'Rani Putri', 'Putu Dharma', 'Ayu Lestari', 'Wayan Sudira', 'Gede Pratama'];
    $lokasi = ['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Denpasar'];$hasil  = [];
    for ($i = 1; $i <= 23; $i++) {$n = $nama[($i - 1) % count($nama)];$hasil[] = [
            'id'         => $i,
            'nama'       => $n,
            'email'      => strtolower(str_replace(' ', '.', $n)) .$i . '@gmail.com',
            'lokasi'     => $lokasi[($i - 1) \% count($lokasi)],
            'created_at' => date('Y-m-d', strtotime('2026-06-23 +' . ($i * 3) . ' day')),
            'status'     => (($i - 1) % 10 < 5) ? 'aktif' : 'diblokir',
            'donasi'     => (($i * 7) \% 23) + 2, 'permintaan' => (($i * 5) % 13) + 1,
        ];
    }
    return $hasil;
}

/* ---------- Parameter halaman ---------- */
$filter =$_GET['status'] ?? 'semua';
if (!in_array($filter, ['semua', 'aktif', 'diblokir'], true)) {$filter = 'semua';
}
$halaman_ini   = max(1, (int) ($_GET['hal'] ?? 1));
$konfirmasi_id = (int) ($_GET['konfirmasi'] ?? 0);
$ok            =$_GET['ok'] ?? '';

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
    $user_id = (int)$_SESSION['user_id'];

    $admin = ambil_satu($conn, "SELECT role FROM users WHERE id = ?", "i", $user_id);
    if (!$admin \vert{}\vert{}$admin['role'] !== 'admin') {
        http_response_code(403);
        die("Akses ditolak. Halaman ini hanya untuk admin.");
    }
    $csrf =$_SESSION['csrf'] ??= bin2hex(random_bytes(16));
}

/* ---------- Proses blokir / buka blokir ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi =$_POST['aksi'];
    $id   = (int) ($_POST['id'] ?? 0);

    if (!MODE_PREVIEW && !hash_equals($csrf,$_POST['csrf'] ?? '')) {
        http_response_code(400);
        die("Permintaan tidak valid.");
    }

    if (in_array($aksi, ['blokir', 'buka'], true) &&$id > 0) {
        if (!MODE_PREVIEW) {
            $status_baru = ($aksi === 'blokir') ? 'diblokir' : 'aktif';
            $stmt = mysqli_prepare($conn, "UPDATE users SET status = ? WHERE id = ? AND COALESCE(role, 'user') <> 'admin'");
            mysqli_stmt_bind_param($stmt, "si", $status_baru,$id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        header("Location: " . url_daftar($filter, $halaman_ini, ['ok' =>$aksi]));
        exit;
    }
}

/* ---------- Ambil data pengguna ---------- */
if (MODE_PREVIEW) {
    $semua = data_contoh();
    if ($filter !== 'semua') {
        $semua = array_values(array_filter($semua, function ($u) use ($filter) {
            return $u['status'] ===$filter;
        }));
    }
    $total = count($semua);

    $total_halaman = max(1, (int) ceil($total / PER_HALAMAN));$halaman_ini   = min($halaman_ini,$total_halaman);
    $offset        = ($halaman_ini - 1) * PER_HALAMAN;

    $pengguna = array_slice($semua,$offset, PER_HALAMAN);
} else {
    $where = "COALESCE(u.role, 'user') <> 'admin'";
    $types = '';$param = [];
    if ($filter !== 'semua') {$where .= " AND COALESCE(u.status, 'aktif') = ?";
        $types .= 's';
        $param[] =$filter;
    }

    $total = (int) ambil_satu($conn, "SELECT COUNT(*) AS n FROM users u WHERE $where", $types, ...$param)['n'];

    $total_halaman = max(1, (int) ceil($total / PER_HALAMAN));$halaman_ini   = min($halaman_ini,$total_halaman);
    $offset        = ($halaman_ini - 1) * PER_HALAMAN;
    $batas         = PER_HALAMAN;

    $pengguna = ambil_semua($conn,
        "SELECT u.id, u.nama, u.email, u.lokasi, u.created_at,
                COALESCE(u.status, 'aktif') AS status,
                (SELECT COUNT(*) FROM items i WHERE i.user_id = u.id)        AS donasi,
                (SELECT COUNT(*) FROM permintaan p WHERE p.id_penerima = u.id) AS permintaan
         FROM users u
         WHERE $where
         ORDER BY u.created_at DESC, u.id DESC
         LIMIT ? OFFSET ?",
        $types . 'ii', ...array_merge($param, [$batas,$offset])
    );
}

/* ---------- Tampilan ---------- */
$pil = [ 'semua'    => ['Semua',    'border-blue-500 bg-blue-50 text-blue-600'], 'aktif'    => ['Aktif',    'border-green-500 bg-green-100 text-green-700'], 'diblokir' => ['Diblokir', 'border-red-500 bg-red-100 text-red-600'], ];$pil_biasa = 'border-slate-300 bg-white text-slate-600 hover:bg-slate-50';

$pesan_ok = [
    'blokir' => 'Pengguna berhasil diblokir.',
    'buka'   => 'Blokir pengguna berhasil dibuka.',
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

$menu = [ ['kunci' => 'beranda',    'label' => 'Beranda',           'url' => 'beranda-admin.php'], ['kunci' => 'donasi',     'label' => 'Kelola Donasi',     'url' => 'kelola-donasi.php'], ['kunci' => 'pengguna',   'label' => 'Kelola Pengguna',   'url' => 'kelola-pengguna.php'], ['kunci' => 'permintaan', 'label' => 'Kelola Permintaan', 'url' => 'kelola-permintaan.php'], ['kunci' => 'kategori',   'label' => 'Kategori Barang',   'url' => 'kategori-barang.php'], ['kunci' => 'laporan',    'label' => 'Laporan',           'url' => 'laporan.php'], ['kunci' => 'pengaturan', 'label' => 'Pengaturan',        'url' => 'pengaturan.php'], ];$menu_aktif = 'pengguna';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport
<?php
date_default_timezone_set('Asia/Makassar'); // WITA

/* ============================================================
   MODE PREVIEW
   true  = tampilan saja (data contoh, tanpa login & database)
   false = terhubung database (butuh login admin + tabel siap)
   ============================================================ */
const MODE_PREVIEW = true;

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
    $t = strtotime($tanggal);
    if (!$t) return '-';
    return date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
}

// data contoh untuk mode preview (sama dengan kelola-pengguna.php)
function data_contoh() {
    $nama   = ['Made Wijaya', 'Ni Luh Putri', 'Komang Ayu', 'Kadek Arya', 'Nadya Prameswari', 'Rani Putri', 'Putu Dharma', 'Ayu Lestari', 'Wayan Sudira', 'Gede Pratama'];
    $lokasi = ['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Denpasar'];
    $hasil  = [];
    for ($i = 1; $i <= 23; $i++) {
        $n = $nama[($i - 1) % count($nama)];
        $hasil[$i] = [
            'id'         => $i,
            'nama'       => $n,
            'email'      => strtolower(str_replace(' ', '.', $n)) . $i . '@gmail.com',
            'lokasi'     => $lokasi[($i - 1) % count($lokasi)],
            'created_at' => date('Y-m-d', strtotime('2026-06-23 +' . ($i * 3) . ' day')),
            'status'     => (($i - 1) % 10 < 5) ? 'aktif' : 'diblokir',
            'donasi'     => (($i * 7) % 23) + 2,
            'permintaan' => (($i * 5) % 13) + 1,
        ];
    }
    return $hasil;
}

/* ---------- Parameter halaman ---------- */
$id_pengguna = (int) ($_GET['id'] ?? 0);
$konfirmasi  = isset($_GET['konfirmasi']) && $_GET['konfirmasi'] == '1';
$ok          = $_GET['ok'] ?? '';

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

/* ---------- Proses blokir / buka blokir ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi'])) {
    $aksi = $_POST['aksi'];

    if (!MODE_PREVIEW && !hash_equals($csrf, $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die("Permintaan tidak valid.");
    }

    if (in_array($aksi, ['blokir', 'buka'], true) && $id_pengguna > 0) {
        if (!MODE_PREVIEW) {
            $status_baru = ($aksi === 'blokir') ? 'diblokir' : 'aktif';
            $stmt = mysqli_prepare($conn, "UPDATE users SET status = ? WHERE id = ? AND COALESCE(role, 'user') <> 'admin'");
            mysqli_stmt_bind_param($stmt, "si", $status_baru, $id_pengguna);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        header("Location: data-pengguna.php?" . http_build_query(['id' => $id_pengguna, 'ok' => $aksi]));
        exit;
    }
}

/* ---------- Ambil data pengguna ---------- */
if (MODE_PREVIEW) {
    $contoh = data_contoh();
    $u = $contoh[$id_pengguna] ?? $contoh[1];
    $id_pengguna = (int) $u['id'];
} else {
    $u = ambil_satu(
        $conn,
        "SELECT u.id, u.nama, u.email, u.lokasi, u.created_at,
                COALESCE(u.status, 'aktif') AS status,
                (SELECT COUNT(*) FROM items i WHERE i.user_id = u.id)          AS donasi,
                (SELECT COUNT(*) FROM permintaan p WHERE p.id_penerima = u.id) AS permintaan
         FROM users u
         WHERE u.id = ? AND COALESCE(u.role, 'user') <> 'admin'",
        "i", $id_pengguna
    );

    if (!$u) {
        http_response_code(404);
        die("Pengguna tidak ditemukan.");
    }
}

$diblokir = ($u['status'] === 'diblokir');

/* ---------- Tampilan ---------- */
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

$menu = [
    ['kunci' => 'beranda',    'label' => 'Beranda',           'url' => 'beranda-admin.php'],
    ['kunci' => 'donasi',     'label' => 'Kelola Donasi',     'url' => 'kelola-donasi.php'],
    ['kunci' => 'pengguna',   'label' => 'Kelola Pengguna',   'url' => 'kelola-pengguna.php'],
    ['kunci' => 'permintaan', 'label' => 'Kelola Permintaan', 'url' => 'kelola-permintaan.php'],
    ['kunci' => 'kategori',   'label' => 'Kategori Barang',   'url' => 'kategori-barang.php'],
    ['kunci' => 'laporan',    'label' => 'Laporan',           'url' => 'laporan.php'],
    ['kunci' => 'pengaturan', 'label' => 'Pengaturan',        'url' => 'pengaturan.php'],
];
$menu_aktif = 'pengguna';

$url_detail = 'data-pengguna.php?id=' . $id_pengguna;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna - ShareSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800">
<div class="flex min-h-screen">

    <!-- ================= SIDEBAR (desktop) ================= -->
    <aside class="hidden lg:flex flex-col w-72 shrink-0 bg-white border-r border-slate-200">
        <div class="flex items-start gap-3 px-8 pt-9 pb-8">
            <span class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><?= $ikon['logo']; ?></svg>
            </span>
            <div>
                <p class="text-2xl font-bold leading-7 text-slate-800">ShareSpace</p>
                <p class="text-xs font-semibold text-blue-600 mt-1 tracking-wide">ADMIN</p>
            </div>
        </div>

        <nav class="px-5 space-y-1.5">
            <?php foreach ($menu as $m): $aktif = ($m['kunci'] === $menu_aktif); ?>
                <a href="<?= e($m['url']); ?>"
                   class="flex items-center gap-4 px-4 py-3.5 rounded-xl font-medium <?= $aktif ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700'; ?>">
                    <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><?= $ikon[$m['kunci']]; ?></svg>
                    <?= e($m['label']); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <!-- ================= KONTEN UTAMA ================= -->
    <main class="flex-1 min-w-0">

        <!-- Navigasi ringkas (layar kecil) -->
        <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3">
            <div class="flex items-center gap-2.5 mb-3">
                <span class="w-9 h-9 rounded-lg bg-blue-600 text-white flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><?= $ikon['logo']; ?></svg>
                </span>
                <p class="font-bold text-slate-800">ShareSpace <span class="text-xs font-semibold text-blue-600 ml-1">ADMIN</span></p>
            </div>
            <div class="flex gap-2 overflow-x-auto pb-1">
                <?php foreach ($menu as $m): $aktif = ($m['kunci'] === $menu_aktif); ?>
                    <a href="<?= e($m['url']); ?>"
                       class="whitespace-nowrap px-3.5 py-2 rounded-lg text-xs font-medium <?= $aktif ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-500'; ?>">
                        <?= e($m['label']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="px-5 py-7 sm:px-8 lg:px-14 lg:py-12 max-w-[1100px]">

            <!-- Judul -->
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">Data Pengguna</h1>

            <?php if (isset($pesan_ok[$ok])): ?>
                <div class="mt-5 bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3">
                    <?= e($pesan_ok[$ok]); ?><?= MODE_PREVIEW ? ' (mode preview, perubahan tidak disimpan)' : ''; ?>
                </div>
            <?php endif; ?>

            <!-- Identitas -->
            <div class="flex items-center gap-6 mt-8">
                <span class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border border-slate-300 bg-white text-sm text-slate-500 flex items-center justify-center shrink-0">
                    <?= e(inisial($u['nama'])); ?>
                </span>
                <div class="min-w-0">
                    <p class="text-2xl sm:text-3xl font-bold text-slate-900 truncate"><?= e($u['nama']); ?></p>
                    <p class="text-lg sm:text-2xl text-slate-500 mt-1 truncate"><?= e($u['email']); ?></p>
                </div>
            </div>

            <!-- Detail -->
            <div class="max-w-3xl bg-white rounded-2xl border border-slate-200 mt-10 text-sm">
                <div class="flex px-6 py-4">
                    <span class="w-44 font-bold text-slate-800">Bergabung pada</span>
                    <span class="text-slate-700">: <?= e(tgl_panjang($u['created_at'])); ?></span>
                </div>
                <div class="flex px-6 py-4 border-t border-slate-100">
                    <span class="w-44 font-bold text-slate-800">Lokasi</span>
                    <span class="text-slate-700">: <?= e($u['lokasi']); ?></span>
                </div>
                <div class="flex px-6 py-4 border-t border-slate-100">
                    <span class="w-44 font-bold text-slate-800">Status</span>
                    <span class="text-slate-700">: <?= $diblokir ? 'Tidak Aktif' : 'Aktif'; ?></span>
                </div>
            </div>

            <!-- Statistik -->
            <div class="flex flex-wrap gap-6 mt-8">
                <div class="bg-white rounded-2xl border border-slate-200 px-6 py-5 flex items-center gap-4 min-w-[190px]">
                    <span class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><?= $ikon['donasi']; ?></svg>
                    </span>
                    <div>
                        <p class="text-2xl font-bold leading-7 text-slate-900"><?= number_format((int) $u['donasi'], 0, ',', '.'); ?></p>
                        <p class="text-xs text-slate-500 mt-0.5">Total donasi</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 px-6 py-5 flex items-center gap-4 min-w-[190px]">
                    <span class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><?= $ikon['permintaan']; ?></svg>
                    </span>
                    <div>
                        <p class="text-2xl font-bold leading-7 text-slate-900"><?= number_format((int) $u['permintaan'], 0, ',', '.'); ?></p>
                        <p class="text-xs text-slate-500 mt-0.5">Total permintaan</p>
                    </div>
                </div>
            </div>

            <!-- Tombol blokir / konfirmasi -->
            <div class="max-w-3xl mt-12">
                <?php if ($konfirmasi): ?>
                    <form method="POST" action="<?= e($url_detail); ?>"
                          class="flex items-center justify-between gap-4 px-5 py-3 border <?= $diblokir ? 'bg-green-100 border-green-400 text-green-900' : 'bg-red-200 border-red-400 text-red-950'; ?>">
                        <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                        <p class="text-sm font-semibold">
                            Anda yakin <?= $diblokir ? 'membuka blokir' : 'melakukan blokir'; ?> untuk <?= e($u['nama']); ?> (<?= e($u['email']); ?>)?
                        </p>
                        <div class="flex items-center gap-4 shrink-0">
                            <button type="submit" name="aksi" value="<?= $diblokir ? 'buka' : 'blokir'; ?>" title="Ya, lanjutkan" class="text-slate-800 hover:text-green-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <a href="<?= e($url_detail); ?>" title="Batal" class="text-slate-800 hover:text-red-700">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18"/></svg>
                            </a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="flex justify-end">
                        <a href="<?= e($url_detail . '&konfirmasi=1'); ?>"
                           class="px-9 py-2.5 rounded-lg border border-slate-300 bg-white text-sm text-slate-500 hover:text-white <?= $diblokir ? 'hover:bg-blue-600 hover:border-blue-600' : 'hover:bg-red-500 hover:border-red-500'; ?>">
                            <?= $diblokir ? 'Buka blokir' : 'Blokir'; ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
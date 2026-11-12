<?php
date_default_timezone_set('Asia/Makassar'); // WITA

/* ============================================================
   MODE PREVIEW (True = Tampilan saja, tanpa database)
   ============================================================ */
const MODE_PREVIEW = true;

if (!MODE_PREVIEW) {
    session_start();
    require_once 'config/database.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
    $user_id = (int) $_SESSION['user_id'];
}

/* ---------- Helper ---------- */
function ambil_satu($conn, $sql, $types = '', ...$params) {
    return null;
}

function foto_url($file, $folder = 'uploads/') {
    if (!empty($file) && file_exists(__DIR__ . '/' . $folder . $file)) {
        return $folder . $file;
    }
    return 'https://placehold.co/200x200/e2e8f0/94a3b8?text=Foto';
}

function inisial($nama) {
    $kata = preg_split('/\s+/', trim($nama));
    $hasil = strtoupper(substr($kata[0] ?? '', 0, 1));
    if (count($kata) > 1) {
        $hasil .= strtoupper(substr(end($kata), 0, 1));
    }
    return $hasil ?: '?';
}

function jarak_km($lat1, $lon1, $lat2, $lon2) {
    return 0;
}

/* ---------- Data barang (Kosong/Bersih) ---------- */
$id_barang = isset($_GET['id']) ? (int) $_GET['id'] : 1;
$milik_sendiri = false;
$sudah_diminta = false;

$barang = [
    'id'            => $id_barang,
    'nama_barang'   => 'Lampu Belajar Kayu',
    'foto'          => 'meja-belajar.png',
    'jumlah'        => 1,
    'kondisi'       => 'Baik',
    'lokasi'        => 'Denpasar',
    'id_pemberi'    => 0,
    'nama_pemberi'  => 'Githa',
    'jarak'         => '20 Km',
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Barang - ShareSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100">
<div class="max-w-md mx-auto min-h-screen bg-slate-50 relative pb-36">

    <!-- Header -->
    <div class="flex items-center gap-3 px-5 pt-6 pb-4">
        <a href="dashboard.php" onclick="if (history.length > 1) { history.back(); return false; }"
           class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-lg font-bold text-slate-800">Detail barang</h1>
    </div>

    <div class="px-5">

        <!-- Foto barang -->
        <img src="<?= htmlspecialchars(foto_url($barang['foto'])); ?>" alt="<?= htmlspecialchars($barang['nama_barang']); ?>"
             class="w-full aspect-[4/3] object-cover rounded-2xl">

        <!-- Nama & status -->
        <div class="flex items-start justify-between gap-3 mt-4">
            <h2 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($barang['nama_barang']); ?></h2>
            <span class="text-sm font-bold text-blue-600 mt-1">Gratis</span>
        </div>
        <p class="text-xs text-slate-400 mt-1"><?= (int) $barang['jumlah']; ?> unit • <?= htmlspecialchars($barang['kondisi']); ?></p>

        <!-- Dibagikan oleh -->
        <h3 class="font-bold text-slate-800 mt-6 mb-3">Dibagikan oleh</h3>
        <div class="bg-white rounded-2xl border border-slate-200 p-3 flex items-center gap-3">
            <span class="w-11 h-11 rounded-full bg-amber-100 text-amber-700 text-sm font-bold flex items-center justify-center shrink-0">
                <?= htmlspecialchars(inisial($barang['nama_pemberi'])); ?>
            </span>
            <div>
                <div class="flex items-center gap-1.5">
                    <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($barang['nama_pemberi']); ?></p>
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
                </div>
                <?php if (!empty($barang['jarak'])): ?>
                    <p class="text-[11px] text-slate-400 mt-0.5"><?= htmlspecialchars($barang['jarak']); ?> km darimu</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lokasi & pengambilan -->
        <h3 class="font-bold text-slate-800 mt-6 mb-3">Lokasi & pengambilan</h3>
        <div class="bg-white rounded-2xl border border-slate-200 p-3">
            <div class="flex gap-3 pb-3 border-b border-slate-100">
                <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s7-7 7-12a7 7 0 00-14 0c0 5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <div>
                    <p class="text-xs font-semibold text-slate-800"><?= htmlspecialchars($barang['lokasi']); ?></p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Ambil langsung, tanpa ongkir.</p>
                </div>
            </div>

            <div class="bg-blue-50 rounded-xl px-3 py-2.5 flex items-start gap-2.5 mt-3">
                <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13M19 12v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7M7.5 8a2.5 2.5 0 010-5C11 3 12 8 12 8s1-5 4.5-5a2.5 2.5 0 010 5"/></svg>
                <div>
                    <p class="text-xs font-semibold text-blue-700">Berbagi, bukan berbelanja.</p>
                    <p class="text-[11px] text-blue-600">Barang ini gratis. Tidak ada pembayaran apa pun.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol bawah -->
    <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-t border-slate-200 px-5 pt-3 pb-5">
        <?php if ($milik_sendiri): ?>
            <a href="edit_donasi.php?id=<?= (int) $barang['id']; ?>" class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-blue-50 text-blue-600 text-sm font-semibold hover:bg-blue-100">
                Ini barang donasimu · Edit donasi
            </a>
            <p class="text-center text-[10px] text-slate-400 mt-2">Kamu tidak bisa meminta barangmu sendiri.</p>

        <?php elseif ($sudah_diminta): ?>
            <span class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-slate-100 text-slate-400 text-sm font-semibold cursor-not-allowed">
                Permintaan sudah diajukan
            </span>
            <p class="text-center text-[10px] text-slate-400 mt-2">Pantau statusnya di halaman profil.</p>

        <?php else: ?>
            <a href="ajukan-permintaan.php?id=<?= (int) $barang['id']; ?>" class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                Ajukan permintaan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
            <p class="text-center text-[10px] text-slate-400 mt-2">Isi rencana pengambilan di halaman berikutnya.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
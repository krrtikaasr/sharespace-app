<?php
session_start();
// require_once 'config/database.php';

$user_id = 1;
$sukses = null;

function ambil_semua($conn, $sql, $types = '', ...$params) {
    return [];
}

function tgl_indo($tanggal) {
    $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $t = strtotime($tanggal);
    if (!$t) return '-';
    return date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
}

function foto_url($file, $folder = 'uploads/') {
    if (!empty($file) && file_exists(__DIR__ . '/' . $folder . $file)) {
        return $folder . $file;
    }
    return 'https://placehold.co/200x200/e2e8f0/94a3b8?text=Foto';
}

$profil = [
    'nama'   => 'Kiara Anastasia',
    'email'  => 'kiaraanastasia@gmail.com',
    'foto'   => 'AI.jpg',
    'lokasi' => 'Denpasar, Bali',
];

$donasi = [
    [
        'id'              => 1,
        'nama_barang'     => 'Lampu Meja Kayu',
        'foto'            => 'meja-belajar.png',
        'created_at'      => '2026-11-02',
        'total_peminat'   => 2,
        'total_pending'   => 1,
        'total_disetujui' => 1,
    ],
];

$permintaan = [
    [
        'id_permintaan'     => 1,
        'status_verifikasi' => 'Disetujui',
        'tanggal_pengajuan' => '2026-11-03',
        'nama_barang'       => 'Lampu Belajar Kayu',
        'foto'              => '',
        'nama_pemberi'      => 'Githa Maharani',
        'status_pickup'     => 'Menunggu Jemput',
    ],
];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - ShareSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100">
<div class="max-w-md mx-auto min-h-screen bg-slate-50 relative pb-24">

    <!-- Header -->
    <div class="flex items-center justify-between px-5 pt-6 pb-4">
        <h1 class="text-2xl font-bold text-slate-800">Profil saya</h1>
        <a href="pengaturan.php" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/></svg>
        </a>
    </div>

    <?php if ($sukses): ?>
        <div class="mx-5 mb-3 bg-green-50 text-green-700 p-3 rounded-xl text-sm"><?= htmlspecialchars($sukses); ?></div>
    <?php endif; ?>

    <!-- Kartu profil -->
    <div class="mx-5 bg-white rounded-2xl border border-slate-200 p-4">
        <div class="flex items-center gap-4">
            <div class="relative">
                <img src="<?= htmlspecialchars(foto_url($profil['foto'], 'uploads/profil/')); ?>" alt="Foto profil" class="w-20 h-20 rounded-full object-cover">
                <span class="absolute -bottom-0.5 -right-0.5 w-6 h-6 bg-blue-600 text-white rounded-full border-2 border-white flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="min-w-0">
                <h2 class="text-lg font-bold text-slate-800 truncate"><?= htmlspecialchars($profil['nama']); ?></h2>
                <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($profil['email']); ?></p>
                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a7 7 0 00-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 00-7-7zm0 9.5A2.5 2.5 0 1112 6.5a2.5 2.5 0 010 5z"/></svg>
                    <?= htmlspecialchars($profil['lokasi']); ?>
                </p>
            </div>
        </div>
        <a href="edit_profil.php" class="mt-4 flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-blue-600 hover:bg-slate-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
            Edit Profil
        </a>
    </div>

    <!-- Riwayat donasi saya -->
    <div class="flex items-center justify-between px-5 mt-6 mb-3">
        <h3 class="font-bold text-slate-800">Riwayat Donasi Saya</h3>
        <span class="text-xs text-slate-400"><?= count($donasi); ?> donasi</span>
    </div>

    <div class="px-5 space-y-4">
        <?php if (count($donasi) === 0): ?>
            <p class="bg-white rounded-2xl border border-slate-200 p-6 text-center text-sm text-slate-400">Kamu belum pernah mendonasikan barang.</p>
        <?php endif; ?>

        <?php foreach ($donasi as $d): ?>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <div class="flex gap-3">
                    <img src="<?= htmlspecialchars(foto_url($d['foto'])); ?>" alt="<?= htmlspecialchars($d['nama_barang']); ?>" class="w-20 h-20 rounded-xl object-cover">
                    <div class="min-w-0">
                        <h4 class="font-bold text-slate-800"><?= htmlspecialchars($d['nama_barang']); ?></h4>

                        <?php if ($d['total_pending'] > 0): ?>
                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">Permintaan Masuk</span>
                        <?php elseif ($d['total_disetujui'] > 0): ?>
                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-green-100 text-green-700">Disetujui</span>
                        <?php else: ?>
                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-500">Tersedia</span>
                        <?php endif; ?>

                        <p class="text-xs text-slate-400 mt-1.5">Diposting <?= tgl_indo($d['created_at']); ?></p>
                    </div>
                </div>

                <?php if ($d['total_peminat'] > 0): ?>
                    <div class="mt-3 bg-amber-50 text-amber-800 text-xs rounded-lg px-3 py-2.5 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8"/></svg>
                        <?= (int) $d['total_peminat']; ?> tetangga tertarik dengan donasimu.
                    </div>
                <?php endif; ?>

                <div class="mt-3 space-y-2">
                    <a href="status_donasi.php?id_barang=<?= (int) $d['id']; ?>" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
                        Verifikasi Permintaan
                    </a>

                    <?php if ($d['total_disetujui'] > 0): ?>
                        <a href="status_donasi.php?id_barang=<?= (int) $d['id']; ?>#jadwal" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl border border-slate-200 text-blue-600 text-sm font-semibold hover:bg-slate-50">
                    <?php else: ?>
                        <span class="flex items-center justify-center gap-2 w-full py-3 rounded-xl border border-slate-200 text-slate-300 text-sm font-semibold cursor-not-allowed">
                    <?php endif; ?>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                        Jadwalkan Penjemputan
                    <?php if ($d['total_disetujui'] > 0): ?></a><?php else: ?></span><?php endif; ?>

                    <a href="edit_donasi.php?id=<?= (int) $d['id']; ?>" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-blue-50 text-blue-600 text-sm font-semibold hover:bg-blue-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        Edit Donasi
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Riwayat permintaan saya -->
    <div class="flex items-center justify-between px-5 mt-6 mb-3">
        <h3 class="font-bold text-slate-800">Riwayat Permintaan Saya</h3>
        <span class="text-xs text-slate-400"><?= count($permintaan); ?> barang</span>
    </div>

    <div class="px-5 space-y-4">
        <?php if (count($permintaan) === 0): ?>
            <p class="bg-white rounded-2xl border border-slate-200 p-6 text-center text-sm text-slate-400">Kamu belum mengajukan permintaan barang.</p>
        <?php endif; ?>

        <?php foreach ($permintaan as $p):
            $status = $p['status_verifikasi'];
            if ($status === 'Disetujui') {
                $badge = 'bg-green-100 text-green-700';
            } elseif ($status === 'Ditolak') {
                $badge = 'bg-red-100 text-red-700';
            } else {
                $badge = 'bg-amber-100 text-amber-700';
            }
        ?>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <div class="flex gap-3">
                    <img src="<?= htmlspecialchars(foto_url($p['foto'])); ?>" alt="<?= htmlspecialchars($p['nama_barang']); ?>" class="w-20 h-20 rounded-xl object-cover">
                    <div class="min-w-0">
                        <h4 class="font-bold text-slate-800"><?= htmlspecialchars($p['nama_barang']); ?></h4>
                        <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?= $badge; ?>"><?= htmlspecialchars($status); ?></span>
                        <p class="text-xs text-slate-400 mt-1.5">Dari <?= htmlspecialchars($p['nama_pemberi']); ?> · <?= tgl_indo($p['tanggal_pengajuan']); ?></p>
                    </div>
                </div>

                <p class="text-xs text-slate-500 mt-3">
                    <?php if ($status === 'Disetujui'): ?>
                        Permintaanmu disetujui. Kabari pemberi setelah barang selesai diambil, ya.
                    <?php elseif ($status === 'Ditolak'): ?>
                        Maaf, permintaanmu belum bisa disetujui oleh pemberi.
                    <?php else: ?>
                        Permintaanmu sedang menunggu verifikasi dari pemberi.
                    <?php endif; ?>
                </p>

                <?php if ($status === 'Disetujui'): ?>
                    <?php if ($p['status_pickup'] === 'Selesai'): ?>
                        <div class="mt-3 w-full py-3 rounded-xl bg-green-50 text-green-700 text-sm font-semibold text-center">Barang sudah diambil</div>
                    <?php else: ?>
                        <form method="POST" class="mt-3">
                            <input type="hidden" name="id_permintaan" value="<?= (int) $p['id_permintaan']; ?>">
                            <button type="submit" name="selesai_ambil" class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg>
                                Update Status Pengambilan
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <p class="text-center text-xs text-slate-400 mt-6 px-5">Barang berpindah, kebaikan bertambah.</p>

    <!-- Bottom navigation -->
    <nav class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-t border-slate-200 flex justify-around py-2.5 text-[11px]">
        <a href="dashboard.php" class="flex flex-col items-center gap-1 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 11l9-8 9 8v10a1 1 0 01-1 1h-5v-7H9v7H4a1 1 0 01-1-1V11z"/></svg>Beranda
        </a>
        <a href="jelajahi.php" class="flex flex-col items-center gap-1 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M16 8l-2 6-6 2 2-6 6-2z"/></svg>Jelajahi
        </a>
        <a href="bagikan.php" class="flex flex-col items-center gap-1 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>Bagikan
        </a>
        <a href="aktivitas.php" class="flex flex-col items-center gap-1 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16v12H4zM4 16l4 4h8l4-4"/></svg>Aktivitas
        </a>
        <a href="profil.php" class="flex flex-col items-center gap-1 text-blue-600 font-semibold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></svg>Profil
        </a>
    </nav>
</div>
</body>
</html>
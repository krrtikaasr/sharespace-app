<?php
session_start();
require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/includes/helpers.php';

// ID pengguna yang sedang login (diisi saat proses login)
$userId = $_SESSION['user_id'] ?? null;

$donasi    = []; // daftar donasi milik pengguna
$permintaan = []; // daftar permintaan yang diajukan pengguna

if ($pdo && $userId) {
    // Tab "Donasi Saya"
    $stmt = $pdo->prepare(
        'SELECT id, nama_barang, jumlah, foto, status, created_at
           FROM donations WHERE user_id = ? ORDER BY created_at DESC'
    );
    $stmt->execute([$userId]);
    $donasi = $stmt->fetchAll();

    // Tab "Permintaan Saya"
    $stmt = $pdo->prepare(
        'SELECT r.id, r.status AS status_permintaan, r.created_at,
                d.nama_barang, d.jumlah, d.foto
           FROM requests r JOIN donations d ON d.id = r.donation_id
          WHERE r.requester_id = ? ORDER BY r.created_at DESC'
    );
    $stmt->execute([$userId]);
    $permintaan = $stmt->fetchAll();
}

// ===== DATA DUMMY (hapus blok ini kalau database sudah terisi) =====
$PAKAI_DUMMY = true;

if ($PAKAI_DUMMY && !$donasi) {
    $donasi = [
        [
            'id'          => 1,
            'nama_barang' => 'Lampu Belajar',
            'jumlah'      => 4,
            'foto'        => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=300&q=80',
            'status'      => 'pending',
            'created_at'  => date('Y-m-d H:i:s'),
        ],
        [
            'id'          => 2,
            'nama_barang' => 'Mainan Kayu',
            'jumlah'      => 3,
            'foto'        => 'assets/images/wood.jpg',
            'status'      => 'verified',
            'created_at'  => date('Y-m-d H:i:s', strtotime('-4 hours')),
        ],
    ];
}
// ===================================================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareSpace - Status & Riwayat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white">

<div class="w-full max-w-[430px] mx-auto bg-white min-h-screen flex flex-col relative">

    <div class="px-5 pt-5 pb-28 flex-1">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-4">
            <a href="javascript:history.back()" aria-label="Kembali"
               class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-slate-50 transition">
                <?= icon('arrow-left', 'w-4 h-4') ?>
            </a>
            <h1 class="font-bold text-base text-slate-900">Status & Riwayat</h1>
        </div>

        <!-- Tab elips: indikator biru bergeser ke pilihan yang diklik -->
        <div class="relative flex p-1 rounded-full border border-slate-400 mb-5" role="tablist">
            <span id="tabIndicator"
                  class="absolute top-1 bottom-1 left-1 w-[calc(50%-4px)] bg-[#2563EB] rounded-full transition-transform duration-300 ease-out"></span>
            <button type="button" role="tab" data-tab="donasi"
                    class="tab-btn relative z-10 flex-1 py-2.5 rounded-full text-xs font-medium text-white transition-colors duration-300">
                Donasi Saya
            </button>
            <button type="button" role="tab" data-tab="permintaan"
                    class="tab-btn relative z-10 flex-1 py-2.5 rounded-full text-xs font-medium text-slate-900 transition-colors duration-300">
                Permintaan Saya
            </button>
        </div>

        <h2 class="font-bold text-xl text-slate-900 mb-3">Status Card</h2>

        <!-- Panel: Donasi Saya -->
        <section id="panel-donasi" class="space-y-3">
            <?php if (!$donasi): ?>
                <p class="text-xs text-slate-400 text-center py-8">Belum ada donasi. Mulai berbagi lewat menu Bagikan.</p>
            <?php endif; ?>

            <?php foreach ($donasi as $d):
                // Pending belum bisa dibuka; verified -> verifikasi pemohon; atur_jadwal -> atur jadwal
                $href = $d['status'] === 'atur_jadwal'
                    ? 'atur-jadwal.php?id=' . (int)$d['id']
                    : 'status-verifikasi.php?id=' . (int)$d['id'];
                $tag   = $href ? 'a' : 'div';
                $attr  = $href ? 'href="' . e($href) . '"' : '';
                $hover = $href ? 'hover:border-blue-300 hover:shadow-sm cursor-pointer' : '';
            ?>
                <<?= $tag ?> <?= $attr ?> class="block border border-slate-300 rounded-[20px] p-3 bg-white transition <?= $hover ?>">
                    <div class="flex items-center gap-3.5">
                        <?= foto_atau_placeholder($d['foto'], $d['nama_barang'], 'w-[62px] h-[62px] rounded-xl shrink-0') ?>
                        <div class="min-w-0">
                            <h3 class="font-bold text-sm text-slate-900 truncate"><?= e($d['nama_barang']) ?></h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                <?= (int)$d['jumlah'] ?> unit • <?= e($d['status'] === 'pending' ? waktu_dibuat($d['created_at']) : waktu_relatif($d['created_at'])) ?>
                            </p>
                            <div class="mt-1.5"><?= badge_status($d['status']) ?></div>
                        </div>
                    </div>
                </<?= $tag ?>>
            <?php endforeach; ?>
        </section>

        <!-- Panel: Permintaan Saya -->
        <section id="panel-permintaan" class="space-y-3 hidden">
            <?php if (!$permintaan): ?>
                <p class="text-xs text-slate-400 text-center py-8">Kamu belum meminta barang apa pun.</p>
            <?php endif; ?>

            <?php foreach ($permintaan as $p):
                $label = ['menunggu' => ['Menunggu', 'bg-[#FEF3C7] text-[#D97706]', 'clock'],
                          'diterima' => ['Diterima', 'bg-[#D1FAE5] text-[#059669]', 'check-circle'],
                          'ditolak'  => ['Ditolak',  'bg-[#FECACA] text-[#B91C1C]', 'x']][$p['status_permintaan']];
            ?>
                <div class="border border-slate-300 rounded-[20px] p-3 bg-white">
                    <div class="flex items-center gap-3.5">
                        <?= foto_atau_placeholder($p['foto'], $p['nama_barang'], 'w-[62px] h-[62px] rounded-xl shrink-0') ?>
                        <div class="min-w-0">
                            <h3 class="font-bold text-sm text-slate-900 truncate"><?= e($p['nama_barang']) ?></h3>
                            <p class="text-[11px] text-slate-400 mt-0.5"><?= (int)$p['jumlah'] ?> unit • <?= e(waktu_relatif($p['created_at'])) ?></p>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium mt-1.5 <?= $label[1] ?>">
                                <?= icon($label[2], 'w-3 h-3') ?><?= $label[0] ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <!-- Banner -->
        <div class="bg-[#FEF3C7] rounded-2xl p-3.5 flex items-center gap-3 mt-4">
            <span class="text-[#D97706] shrink-0"><?= icon('heart', 'w-8 h-8') ?></span>
            <div>
                <p class="text-[11px] font-bold text-slate-900 leading-snug">Tak terpakai di rumahmu, berarti bagi yang lain.</p>
                <p class="text-[10px] text-slate-500 mt-0.5">Dampak besar pada setiap bentuk berbagi</p>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/includes/navbar.php'; ?>
</div>

<script>
    const indikator = document.getElementById('tabIndicator');
    const tombol    = document.querySelectorAll('.tab-btn');
    const panel     = { donasi: document.getElementById('panel-donasi'),
                        permintaan: document.getElementById('panel-permintaan') };

    function pilihTab(nama) {
        indikator.style.transform = nama === 'donasi' ? 'translateX(0)' : 'translateX(100%)';
        tombol.forEach(b => {
            const aktif = b.dataset.tab === nama;
            b.classList.toggle('text-white', aktif);
            b.classList.toggle('text-slate-900', !aktif);
        });
        Object.entries(panel).forEach(([k, el]) => el.classList.toggle('hidden', k !== nama));
    }
    tombol.forEach(b => b.addEventListener('click', () => pilihTab(b.dataset.tab)));
</script>
</body>
</html>

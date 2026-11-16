<?php
/**
 * Layout bersama (responsive): sidebar + topbar di desktop, header + navbar bawah di HP.
 * Variabel yang bisa diisi halaman SEBELUM include file ini:
 *   $judulHalaman, $subjudul, $kembaliHref, $navAktif, $wajibLogin (default true)
 * Halaman wajib sudah punya: $pdo (dari config/pdo.php)
 */
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/auth.php';

$userId       = current_user_id();
$judulHalaman = $judulHalaman ?? 'Status & Riwayat';
$subjudul     = $subjudul ?? 'Pantau donasi dan permintaan barangmu';
$kembaliHref  = $kembaliHref ?? null;
$navAktif     = $navAktif ?? 'aktivitas';
$BASE         = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';

if (($wajibLogin ?? true) && !$userId) {
    header('Location: login.php');
    exit;
}

/* Menu navigasi: sesuaikan alamat file dengan punya kelompok */
$menu = [
    'jelajahi'  => ['Jelajahi',  'compass',     'dashboard.php'],
    'bagikan'   => ['Bagikan',   'plus-circle', 'tambah-donasi.php'],
    'aktivitas' => ['Aktivitas', 'inbox',       'status-donasi.php'],
    'profil'    => ['Profil',    'user',        'profil.php'],
];

$namaUser = 'Kamu'; $fotoUser = null; $notifList = []; $notifBelum = 0;
if (!empty($pdo) && $userId) {
    try {
        $st = $pdo->prepare('SELECT nama, foto FROM users WHERE id = ?');
        $st->execute([$userId]);
        if ($u = $st->fetch()) { $namaUser = $u['nama']; $fotoUser = $u['foto'] ?? null; }
    } catch (Throwable $ex) { /* kolom foto mungkin belum ada */
        try {
            $st = $pdo->prepare('SELECT nama FROM users WHERE id = ?');
            $st->execute([$userId]);
            if ($u = $st->fetch()) { $namaUser = $u['nama']; }
        } catch (Throwable $ex2) {}
    }
    try {
        $st = $pdo->prepare('SELECT id, judul, deskripsi, link, dibaca, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 8');
        $st->execute([$userId]);
        $notifList = $st->fetchAll();
        $st = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND dibaca = 0');
        $st->execute([$userId]);
        $notifBelum = (int)$st->fetchColumn();
    } catch (Throwable $ex) { /* tabel notifications belum dibuat */ }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#2563EB">
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="manifest" href="<?= e($BASE) ?>manifest.json">
    <link rel="apple-touch-icon" href="<?= e($BASE) ?>icons/icon-192.png">
    <title>ShareSpace - <?= e($judulHalaman) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; } html { -webkit-tap-highlight-color: transparent; }</style>
</head>
<body class="bg-slate-50 text-slate-900">

<!-- ===== Sidebar (desktop) ===== -->
<aside class="hidden lg:flex fixed inset-y-0 left-0 w-[248px] flex-col bg-white border-r border-slate-200 z-30">
    <div class="flex items-center gap-3 px-7 pt-7 pb-9">
        <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center"><?= icon('heart', 'w-5 h-5') ?></span>
        <span class="text-xl font-extrabold tracking-tight">ShareSpace</span>
    </div>
    <p class="px-8 text-[11px] font-semibold tracking-wider text-slate-400 mb-2">MENU</p>
    <nav class="px-4 space-y-1.5">
        <?php foreach ($menu as $key => [$label, $ikon, $href]): $aktif = ($key === $navAktif); ?>
            <a href="<?= e($href) ?>" class="relative flex items-center gap-3 h-11 px-5 rounded-xl text-[14.5px] transition-colors <?= $aktif ? 'bg-blue-50 text-[#2563EB] font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-[#2563EB]' ?>">
                <?php if ($aktif): ?><span class="absolute left-0 top-2.5 bottom-2.5 w-1 rounded-full bg-[#2563EB]"></span><?php endif; ?>
                <?= icon($ikon, 'w-5 h-5') ?><?= e($label) ?>
            </a>
        <?php endforeach; ?>
    </nav>
    <a href="profil.php" class="mt-auto m-4 flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:border-blue-300 transition">
        <?php if ($fotoUser): ?>
            <img src="<?= e($fotoUser) ?>" alt="" class="w-9 h-9 rounded-full object-cover" onerror="this.remove()">
        <?php else: ?>
            <span class="w-9 h-9 rounded-full bg-slate-300 text-white flex items-center justify-center"><?= icon('user', 'w-4 h-4') ?></span>
        <?php endif; ?>
        <span class="min-w-0 flex-1">
            <span class="block text-sm font-bold truncate"><?= e($namaUser) ?></span>
            <span class="block text-xs text-slate-500">Lihat profil</span>
        </span>
        <?= icon('chev-r', 'w-4 h-4 text-slate-400') ?>
    </a>
</aside>

<div class="lg:pl-[248px] min-h-screen flex flex-col">

    <!-- ===== Topbar ===== -->
    <header class="sticky top-0 z-20 bg-white lg:bg-slate-50 border-b border-slate-200 lg:border-0" style="padding-top: env(safe-area-inset-top)">
        <div class="max-w-[1240px] mx-auto px-4 lg:px-10 h-14 lg:h-20 flex items-center gap-3">
            <?php if ($kembaliHref): ?>
                <a href="<?= e($kembaliHref) ?>" aria-label="Kembali" class="lg:hidden w-9 h-9 shrink-0 rounded-full border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-slate-50">
                    <?= icon('arrow-left', 'w-4 h-4') ?>
                </a>
            <?php endif; ?>
            <div class="min-w-0 flex-1">
                <h1 class="font-bold text-base lg:text-[26px] lg:font-extrabold leading-tight truncate"><?= e($judulHalaman) ?></h1>
                <p class="hidden lg:block text-[13px] text-slate-500 mt-0.5 truncate"><?= e($subjudul) ?></p>
            </div>

            <label class="hidden lg:flex items-center gap-2 w-[244px] h-11 px-4 bg-white border border-slate-200 rounded-full text-slate-400 focus-within:border-[#2563EB] focus-within:text-[#2563EB] transition">
                <?= icon('search', 'w-[18px] h-[18px]') ?>
                <input id="cariBarang" type="search" placeholder="Cari barang..." autocomplete="off" class="w-full bg-transparent outline-none text-[13.5px] text-slate-900 placeholder:text-slate-400">
            </label>

            <!-- Notifikasi -->
            <div class="relative">
                <button id="btnNotif" type="button" aria-label="Notifikasi" class="relative w-10 h-10 lg:w-11 lg:h-11 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-blue-50 hover:text-[#2563EB] transition">
                    <?= icon('bell', 'w-5 h-5') ?>
                    <?php if ($notifBelum > 0): ?><span id="dotNotif" class="absolute top-2 right-2.5 w-2.5 h-2.5 rounded-full bg-red-500 ring-2 ring-white"></span><?php endif; ?>
                </button>
                <div id="panelNotif" class="hidden absolute right-0 top-full mt-2 w-[380px] max-w-[calc(100vw-2rem)] bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden z-40">
                    <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-slate-100">
                        <h2 class="font-extrabold text-base">Notifikasi</h2>
                        <button id="btnBacaSemua" type="button" class="text-xs font-semibold text-[#2563EB] hover:underline">Tandai semua dibaca</button>
                    </div>
                    <?php if (!$notifList): ?>
                        <p class="px-5 py-8 text-center text-xs text-slate-400">Belum ada notifikasi.</p>
                    <?php endif; ?>
                    <?php foreach ($notifList as $n): $tag = $n['link'] ? 'a' : 'div'; ?>
                        <<?= $tag ?> <?= $n['link'] ? 'href="' . e($n['link']) . '"' : '' ?> class="flex gap-3 px-5 py-3.5 border-b border-slate-50 hover:bg-slate-50 <?= $n['dibaca'] ? '' : 'bg-[#F5F9FF]' ?>">
                            <span class="w-10 h-10 shrink-0 rounded-full bg-blue-100 text-[#2563EB] flex items-center justify-center"><?= icon('msg', 'w-5 h-5') ?></span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-[13.5px] font-bold text-slate-900"><?= e($n['judul']) ?></span>
                                <?php if ($n['deskripsi']): ?><span class="block text-xs text-slate-500 mt-0.5"><?= e($n['deskripsi']) ?></span><?php endif; ?>
                                <span class="block text-[11px] text-slate-400 mt-0.5"><?= e(waktu_relatif($n['created_at'])) ?></span>
                            </span>
                            <?php if (!$n['dibaca']): ?><span data-notif-dot class="w-2 h-2 mt-2 rounded-full bg-[#2563EB] shrink-0"></span><?php endif; ?>
                        </<?= $tag ?>>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="profil.php" class="hidden lg:flex w-11 h-11 rounded-full bg-slate-300 text-white items-center justify-center overflow-hidden" aria-label="Profil">
                <?php if ($fotoUser): ?><img src="<?= e($fotoUser) ?>" alt="" class="w-full h-full object-cover" onerror="this.remove()"><?php else: ?><?= icon('user', 'w-5 h-5') ?><?php endif; ?>
            </a>
        </div>
    </header>

    <main class="flex-1 w-full max-w-[1240px] mx-auto px-4 lg:px-10 pt-4 lg:pt-2 pb-28 lg:pb-12">

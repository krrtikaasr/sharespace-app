
<?php
$username = "Username";
$initials = "AN";

// Daftar Kategori untuk Grid 3x2 (HP) / 6x1 (laptop)
$quickCategories = [
    [
        'label' => 'Makanan',
        'target_category' => 'Makanan',
        'icon' => '<svg class="w-6 h-6 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v7a3 3 0 003 3h1v8m0-8h1a3 3 0 003-3V3M7 3v7m10-7v18m0-18a3 3 0 013 3v4a3 3 0 01-3 3"/></svg>'
    ],
    [
        'label' => 'Pakaian',
        'target_category' => 'Pakaian',
        'icon' => '<svg class="w-6 h-6 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/></svg>'
    ],
    [
        'label' => 'Elektronik',
        'target_category' => 'Elektronik',
        'icon' => '<svg class="w-6 h-6 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a9 9 0 0118 0v6"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3z"/></svg>'
    ],
    [
        'label' => 'Alat Tulis',
        'target_category' => 'Alat Tulis',
        'icon' => '<svg class="w-6 h-6 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>'
    ],
    [
        'label' => 'Furnitur',
        'target_category' => 'Furnitur',
        'icon' => '<svg class="w-6 h-6 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 9V6a2 2 0 00-2-2H6a2 2 0 00-2 2v3"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 11v5a2 2 0 002 2h16a2 2 0 002-2v-5a2 2 0 00-4 0v2H6v-2a2 2 0 00-4 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M4 18v2m16-2v2"/></svg>'
    ],
    [
        'label' => 'Lainnya',
        'target_category' => 'Lainnya',
        'icon' => '<svg class="w-6 h-6 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4l3.5 6h-7L12 4zM5 14h5v5H5v-5zm11.5 0a2.5 2.5 0 100 5 2.5 2.5 0 000-5z"/></svg>'
    ],
];

// Rekomendasi untukmu
$recommendations = [
    [
        'id' => 1,
        'title' => 'Roti Gandum',
        'category' => 'Makanan',
        'location' => 'Denpasar',
        'condition' => 'Baru hari ini',
        'status' => 'Tersedia',
        'image' => '../assets/roti-gandum.png'
    ],
    [
        'id' => 2,
        'title' => 'Buku',
        'category' => 'Alat Tulis',
        'location' => 'Tabanan',
        'condition' => 'Kondisi baik',
        'status' => 'Tersedia',
        'image' => '../assets/buku.png'
    ],
    [
        'id' => 3,
        'title' => 'Boneka',
        'category' => 'Lainnya',
        'location' => 'Bangli',
        'condition' => 'Baik',
        'status' => 'Tersedia',
        'image' => '../assets/boneka.png'
    ],
];

// Menu navigasi
$navItems = [
    [
        'label' => 'Beranda',
        'href' => 'beranda.php',
        'active' => true,
        'paths' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'
    ],
    [
        'label' => 'Jelajahi',
        'href' => 'jelajahi.php',
        'active' => false,
        'paths' => '<circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M14.5 9.5l-2 5-5 2 2-5 5-2z"/>'
    ],
    [
        'label' => 'Bagikan',
        'href' => '#',
        'active' => false,
        'paths' => '<circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/>'
    ],
    [
        'label' => 'Aktivitas',
        'href' => '#',
        'active' => false,
        'paths' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'
    ],
    [
        'label' => 'Profil',
        'href' => '#',
        'active' => false,
        'paths' => '<path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'
    ],
];

// Logo ShareSpace
$renderBrand = function ($large = false) { ?>
    <a href="beranda.php" class="flex items-center <?= $large ? 'gap-3' : 'gap-2.5' ?>">
        <div class="<?= $large ? 'w-[52px] h-[52px] rounded-[14px]' : 'w-9 h-9 rounded-xl' ?> bg-[#2554E8] flex items-center justify-center shadow-xs">
            <svg class="<?= $large ? 'w-7 h-7' : 'w-5 h-5' ?> text-white" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" transform="translate(3.5, -1) scale(0.65)" />
                <path d="M2 14.5l3.5 3.5" />
                <path d="M4.5 17l3.5-3.5a2 2 0 0 1 2.8 0l1.2 1.2a2 2 0 0 0 2.8 0l3.7-3.7a1.8 1.8 0 0 1 2.5 2.5l-5.3 5.3a4 4 0 0 1-2.8 1.2H7.5" />
                <path d="M9.5 15.5h3" />
            </svg>
        </div>
        <span class="<?= $large ? 'text-[24px]' : 'text-[19px]' ?> font-bold text-slate-900 tracking-tight">ShareSpace</span>
    </a>
<?php };
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareSpace - Beranda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="min-h-screen bg-white lg:flex">

    <!-- Sidebar (hanya laptop) -->
    <aside class="hidden lg:flex lg:flex-col w-[267px] shrink-0 sticky top-0 h-screen bg-white border-r border-slate-200/70 px-6 pt-10 pb-8">
        <div>
            <?php $renderBrand(true); ?>
        </div>

        <p class="mt-[78px] mb-2 px-2 text-sm tracking-wide text-slate-500">MENU</p>

        <nav class="flex flex-col gap-0.5">
            <?php foreach ($navItems as $nav): ?>
                <a href="<?= $nav['href'] ?>"
                   class="flex items-center gap-3 px-2 h-11 rounded-xl text-sm font-semibold transition-colors <?= $nav['active']
                        ? 'text-[#2554E8]'
                        : 'text-slate-500 hover:bg-slate-50 hover:text-slate-700' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="<?= $nav['active'] ? '2' : '1.8' ?>" viewBox="0 0 24 24">
                        <?= $nav['paths'] ?>
                    </svg>
                    <?= htmlspecialchars($nav['label']) ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Akun pengguna -->
        <a href="#" class="mt-auto flex items-center gap-3 px-2">
            <span class="w-9 h-9 rounded-full bg-[#EEF2FF] text-[#2554E8] text-xs font-bold flex items-center justify-center">
                <?= htmlspecialchars($initials) ?>
            </span>
            <span class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($username) ?></span>
        </a>
    </aside>

    <!-- Area konten -->
    <div class="w-full flex-1 min-w-0 min-h-screen flex flex-col relative lg:bg-[#F8FAFD]">

        <!-- Top App Bar -->
        <header class="px-5 pt-6 pb-2 lg:px-7 lg:pt-6 flex items-center justify-between bg-white lg:bg-transparent">

            <!-- Logo: tampil di HP saja -->
            <div class="lg:hidden">
                <?php $renderBrand(); ?>
            </div>

            <div class="flex items-center gap-2.5 lg:ml-auto">

                <!-- Tombol notifikasi desktop: membuka drawer -->
                <button
                    type="button"
                    onclick="openNotificationDrawer()"
                    class="hidden lg:flex relative w-10 h-10 rounded-full border border-slate-200 bg-white items-center justify-center text-slate-700 hover:bg-slate-50 transition-colors"
                    aria-label="Buka notifikasi"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>

                    <span class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-[#2554E8] ring-2 ring-white"></span>
                </button>

                <!-- Tombol notifikasi mobile: menuju halaman notifikasi -->
                <a
                    href="notifikasi.php"
                    class="lg:hidden relative w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-700 hover:bg-slate-50 transition-colors"
                    aria-label="Buka notifikasi"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>

                    <span class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-[#2554E8] ring-2 ring-white"></span>
                </a>

                <!-- Profil mobile -->
                <a href="#" class="lg:hidden w-10 h-10 rounded-full bg-[#EEF2FF] text-[#2554E8] text-xs font-bold flex items-center justify-center hover:bg-blue-100 transition-colors">
                    <?= htmlspecialchars($initials) ?>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="px-5 pt-4 pb-24 lg:px-12 lg:pt-4 lg:pb-12 flex-1">
            <div class="w-full max-w-5xl mx-auto">

                <!-- Greeting Section -->
                <section class="mb-5 lg:mb-6 lg:text-center">
                    <h1 class="text-[28px] lg:text-[36px] font-bold text-slate-900 tracking-tight leading-tight">
                        Halo, <?= htmlspecialchars($username) ?>!
                    </h1>

                    <p class="text-[13px] lg:text-base text-slate-500 mt-1">
                        Beri barang tak terpakai kehidupan baru.
                    </p>
                </section>

                <!-- Search Bar -->
                <section class="mb-6 lg:mb-10 lg:max-w-[955px] lg:mx-auto flex gap-3">
                    <a href="jelajahi.php" class="flex items-center gap-3 flex-1 min-w-0 lg:h-14 bg-white border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-400 shadow-2xs hover:border-[#2554E8] transition-colors">
                        <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z" />
                        </svg>

                        <span class="text-[13px] lg:text-sm truncate">Cari barang yang kamu butuhkan...</span>
                    </a>

                    <!-- Tombol cari (hanya laptop) -->
                    <a href="jelajahi.php" class="hidden lg:flex items-center justify-center lg:h-14 px-8 rounded-2xl bg-[#2554E8] hover:bg-[#1f47c9] text-white text-[15px] font-semibold transition-colors">
                        Cari barang
                    </a>
                </section>

                <!-- Category Grid -->
                <section class="grid grid-cols-3 md:grid-cols-6 gap-3 lg:gap-4 xl:gap-6 mb-6 lg:mb-10">
                    <?php foreach ($quickCategories as $cat): ?>
                        <a href="jelajahi.php?category=<?= urlencode($cat['target_category']) ?>"
                           class="bg-[#F3F6FD] hover:bg-[#E8EEFC] active:scale-95 rounded-2xl py-4 px-2 flex flex-col xl:flex-row items-center justify-center gap-2 xl:gap-3 xl:h-20 transition-all">

                            <div class="w-8 h-8 flex items-center justify-center">
                                <?= $cat['icon'] ?>
                            </div>

                            <span class="text-xs xl:text-sm font-medium xl:font-semibold text-slate-800">
                                <?= htmlspecialchars($cat['label']) ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </section>

                <!-- Rekomendasi untukmu -->
                <section>
                    <div class="flex items-center justify-between mb-3.5 lg:mb-5">
                        <h2 class="text-[17px] lg:text-[22px] font-bold text-slate-900">
                            Rekomendasi untukmu
                        </h2>

                        <a href="jelajahi.php?category=Semua" class="text-xs lg:text-sm font-semibold text-[#2554E8] hover:underline">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-0.5 md:gap-4 lg:gap-5">
                        <?php foreach ($recommendations as $item): ?>
                            <article class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden flex flex-col hover:shadow-md transition-shadow">

                                <div class="relative aspect-[4/3] lg:aspect-[19/10] bg-slate-100 overflow-hidden">
                                    <img
                                        src="<?= htmlspecialchars($item['image']) ?>"
                                        alt="<?= htmlspecialchars($item['title']) ?>"
                                        class="w-full h-full object-cover"
                                    >
                                </div>

                                <div class="p-3 lg:p-3.5 flex-1 flex flex-col justify-between gap-0.5">
                                    <div class="flex flex-col gap-0.5">
                                        <h3 class="text-[14px] lg:text-xl font-bold text-slate-900 line-clamp-1">
                                            <?= htmlspecialchars($item['title']) ?>
                                        </h3>

                                        <p class="text-[11px] lg:text-base text-slate-500">
                                            <?= htmlspecialchars($item['category']) ?>
                                        </p>

                                        <div class="flex items-center gap-1 lg:gap-1.5 text-[11px] lg:text-base text-slate-500">
                                            <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                                                <circle cx="12" cy="11" r="2"/>
                                            </svg>

                                            <span class="truncate">
                                                <?= htmlspecialchars($item['location']) ?>
                                            </span>
                                        </div>

                                        <p class="text-[11px] lg:text-base text-slate-500 truncate">
                                            <?= htmlspecialchars($item['condition']) ?>
                                        </p>
                                    </div>

                                    <div>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 lg:py-1.5 rounded-full text-[10px] lg:text-[13px] font-semibold bg-emerald-50 text-emerald-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <?= htmlspecialchars($item['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>

            </div>
        </main>

        <!-- Bottom Navigation Bar -->
        <nav class="fixed inset-x-0 bottom-0 bg-white border-t border-slate-100 px-3 py-2 flex items-center justify-around z-30 lg:hidden">
            <?php foreach ($navItems as $nav): ?>
                <a href="<?= $nav['href'] ?>"
                   class="flex flex-col items-center gap-1 py-1 px-2 <?= $nav['active'] ? 'text-[#2554E8]' : 'text-slate-400 hover:text-slate-600' ?>">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="<?= $nav['active'] ? '2' : '1.8' ?>" viewBox="0 0 24 24">
                        <?= $nav['paths'] ?>
                    </svg>

                    <span class="text-[10px] <?= $nav['active'] ? 'font-semibold' : 'font-medium' ?>">
                        <?= htmlspecialchars($nav['label']) ?>
                    </span>
                </a>
            <?php endforeach; ?>
        </nav>

    </div>

    <!-- ========================================= -->
    <!-- DRAWER NOTIFIKASI KHUSUS DESKTOP / LAPTOP -->
    <!-- ========================================= -->

    <div id="notificationOverlay" class="fixed inset-0 z-50 hidden" aria-hidden="true">

        <!-- Background gelap -->
        <button
            type="button"
            onclick="closeNotificationDrawer()"
            class="absolute inset-0 w-full h-full bg-slate-950/60 opacity-0 transition-opacity duration-300"
            aria-label="Tutup notifikasi"
        ></button>

        <!-- Panel drawer dari kanan -->
        <aside
            id="notificationDrawer"
            role="dialog"
            aria-modal="true"
            aria-label="Notifikasi"
            class="absolute top-0 right-0 h-full w-full max-w-[480px] bg-white shadow-2xl translate-x-full transition-transform duration-300 ease-out"
        >
            <iframe
                src="notifikasi.php?drawer=1"
                title="Notifikasi"
                class="w-full h-full border-0"
            ></iframe>
        </aside>
    </div>

    <script>
        const notificationOverlay = document.getElementById('notificationOverlay');
        const notificationBackdrop = notificationOverlay?.querySelector('button');
        const notificationDrawer = document.getElementById('notificationDrawer');

        // Membuka drawer notifikasi
        function openNotificationDrawer() {
            if (!notificationOverlay) return;

            notificationOverlay.classList.remove('hidden');
            notificationOverlay.setAttribute('aria-hidden', 'false');

            // Mencegah halaman utama ikut discroll
            document.body.classList.add('overflow-hidden');

            requestAnimationFrame(() => {
                notificationBackdrop.classList.remove('opacity-0');
                notificationBackdrop.classList.add('opacity-100');

                notificationDrawer.classList.remove('translate-x-full');
            });
        }

        // Menutup drawer notifikasi
        function closeNotificationDrawer() {
            if (!notificationOverlay) return;

            notificationBackdrop.classList.remove('opacity-100');
            notificationBackdrop.classList.add('opacity-0');

            notificationDrawer.classList.add('translate-x-full');

            notificationOverlay.setAttribute('aria-hidden', 'true');

            // Sembunyikan overlay setelah animasi selesai
            setTimeout(() => {
                notificationOverlay.classList.add('hidden');

                // Mengaktifkan kembali scroll halaman utama
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }

        // Menutup drawer menggunakan tombol Escape
        document.addEventListener('keydown', (event) => {
            if (
                event.key === 'Escape' &&
                notificationOverlay &&
                !notificationOverlay.classList.contains('hidden')
            ) {
                closeNotificationDrawer();
            }
        });

        // Menerima perintah penutupan dari notifikasi.php
        window.addEventListener('message', (event) => {
            if (event.data === 'closeNotifications') {
                closeNotificationDrawer();
            }
        });

        // Fungsi toggle like
        function toggleLike(btn) {
            const svg = btn.querySelector('svg');
            const isLiked = svg.classList.contains('text-rose-500');

            if (isLiked) {
                btn.innerHTML = `<svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>`;
            } else {
                btn.innerHTML = `<svg class="w-4 h-4 text-rose-500 fill-rose-500" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>`;
            }
        }
    </script>

</body>
</html>

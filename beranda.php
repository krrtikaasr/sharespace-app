<?php
$username = "Username";
$initials = "AN";

// Daftar Kategori untuk Grid 3x2
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

// Rekomendasi untukmu (Path gambar naik 1 folder ke ../assets/)
$recommendations = [
    [
        'id' => 1,
       'title' => 'Roti Gandum',
        'category' => 'Makanan',
        'location' => 'Jimbaran',
        'distance' => '6 km',
        'condition' => 'Baru - Layak Konsumsi',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/roti-gandum.png'
    ],
    [
        'id' => 2,
        'title' => 'Buku',
        'category' => 'Alat Tulis',
        'location' => 'Jimbaran',
        'distance' => '1,5 km',
        'condition' => 'Kondisi baik',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/buku.png'
    ],
];
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
            background-color: #1f2937;
        }
    </style>
</head>
<body class="flex justify-center min-h-screen">

    <!-- Container Mobile View -->
    <div class="w-full max-w-[430px] bg-white min-h-screen flex flex-col relative shadow-2xl">
        
        <!-- Top App Bar -->
        <header class="px-5 pt-6 pb-2 flex items-center justify-between bg-white">
            <a href="beranda.php" class="flex items-center gap-2.5">
                <!-- App Icon (Tangan Menadah Hati) -->
                <div class="w-9 h-9 rounded-xl bg-[#2554E8] flex items-center justify-center shadow-xs">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <!-- Ikon Hati di Atas Tangan -->
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" transform="translate(3.5, -1) scale(0.65)" />
                        <!-- Ikon Telapak Tangan -->
                        <path d="M2 14.5l3.5 3.5" />
                        <path d="M4.5 17l3.5-3.5a2 2 0 0 1 2.8 0l1.2 1.2a2 2 0 0 0 2.8 0l3.7-3.7a1.8 1.8 0 0 1 2.5 2.5l-5.3 5.3a4 4 0 0 1-2.8 1.2H7.5" />
                        <path d="M9.5 15.5h3" />
                    </svg>
                </div>
                <span class="text-[19px] font-bold text-slate-900 tracking-tight">ShareSpace</span>
            </a>

            <div class="flex items-center gap-2.5">
                <button type="button" class="relative w-10 h-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-slate-700 hover:bg-slate-50 transition-colors" aria-label="Notifikasi">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-2 right-2.5 w-2 h-2 rounded-full bg-[#2554E8] ring-2 ring-white"></span>
                </button>

                <a href="#" class="w-10 h-10 rounded-full bg-[#EEF2FF] text-[#2554E8] text-xs font-bold flex items-center justify-center hover:bg-blue-100 transition-colors">
                    <?= htmlspecialchars($initials) ?>
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="px-5 pt-4 pb-24 flex-1">
            <!-- Greeting Section -->
            <section class="mb-5">
                <h1 class="text-[28px] font-bold text-slate-900 tracking-tight leading-tight">
                    Halo, <?= htmlspecialchars($username) ?>!
                </h1>
                <p class="text-[13px] text-slate-500 mt-1">
                    Beri barang tak terpakai kehidupan baru.
                </p>
            </section>

            <!-- Search Bar -> Diarahkan ke ../jelajahi/jelajahi.php -->
            <section class="mb-6">
                <a href="jelajahi.php" class="flex items-center gap-3 w-full bg-white border border-slate-200 rounded-2xl px-4 py-3.5 text-slate-400 shadow-2xs hover:border-[#2554E8] transition-colors">
                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z" />
                    </svg>
                    <span class="text-[13px]">Cari barang yang kamu butuhkan...</span>
                </a>
            </section>

            <!-- Category Grid -> Diarahkan ke ../jelajahi/jelajahi.php?category=... -->
            <section class="grid grid-cols-3 gap-3 mb-6">
                <?php foreach ($quickCategories as $cat): ?>
                    <a href="jelajahi.php?category=<?= urlencode($cat['target_category']) ?>" 
                       class="bg-[#F3F6FD] hover:bg-[#E8EEFC] active:scale-95 rounded-2xl py-4 px-2 flex flex-col items-center justify-center gap-2 transition-all">
                        <div class="w-8 h-8 flex items-center justify-center">
                            <?= $cat['icon'] ?>
                        </div>
                        <span class="text-xs font-medium text-slate-800">
                            <?= htmlspecialchars($cat['label']) ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </section>

            <!-- Akses Cepat Card -->
            <section class="bg-[#F3F6FD] rounded-2xl p-4 mb-6">
                <h2 class="text-[15px] font-bold text-slate-900 mb-3">Akses Cepat</h2>
                <div class="grid grid-cols-2 gap-3">
                    <a href="#" class="bg-white hover:bg-slate-50 rounded-xl py-3 px-3.5 flex items-center justify-center gap-2 border border-slate-100/80 shadow-2xs transition-colors">
                        <svg class="w-4 h-4 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="text-xs font-semibold text-slate-800">Donasi Saya</span>
                    </a>

                    <a href="#" class="bg-white hover:bg-slate-50 rounded-xl py-3 px-3.5 flex items-center justify-center gap-2 border border-slate-100/80 shadow-2xs transition-colors">
                        <svg class="w-4 h-4 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="text-xs font-semibold text-slate-800">Favorit</span>
                    </a>
                </div>
            </section>

            <!-- Rekomendasi untukmu (2 Barang) -->
            <section>
                <div class="flex items-center justify-between mb-3.5">
                    <h2 class="text-[17px] font-bold text-slate-900">Rekomendasi untukmu</h2>
                    <a href="jelajahi.php?category=Semua" class="text-xs font-semibold text-[#2554E8] hover:underline">
                        Lihat Semua
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <?php foreach ($recommendations as $item): ?>
                        <article class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                            <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden">
                                <img 
                                    src="<?= htmlspecialchars($item['image']) ?>" 
                                    alt="<?= htmlspecialchars($item['title']) ?>" 
                                    class="w-full h-full object-cover"
                                >
                                <button 
                                    type="button"
                                    onclick="toggleLike(this)"
                                    class="absolute top-2.5 right-2.5 w-7 h-7 rounded-full bg-white/95 backdrop-blur-xs flex items-center justify-center shadow-xs transition-transform active:scale-90"
                                    aria-label="Sukai barang"
                                >
                                    <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="p-3 flex-1 flex flex-col justify-between">
                                <div>
                                    <h3 class="text-[14px] font-bold text-slate-900 line-clamp-1">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </h3>
                                    <p class="text-[11px] text-slate-500 mt-0.5">
                                        <?= htmlspecialchars($item['category']) ?>
                                    </p>

                                    <div class="flex items-center gap-1 text-[11px] text-slate-500 mt-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                                            <circle cx="12" cy="11" r="2"/>
                                        </svg>
                                        <span class="truncate"><?= htmlspecialchars($item['location']) ?> · <?= htmlspecialchars($item['distance']) ?></span>
                                    </div>

                                    <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                        <?= htmlspecialchars($item['condition']) ?>
                                    </p>
                                </div>

                                <div class="mt-2.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        </main>

        <!-- Bottom Navigation Bar -->
        <nav class="fixed bottom-0 w-full max-w-[430px] bg-white border-t border-slate-100 px-3 py-2 flex items-center justify-around z-30">
            <!-- Active Tab: Beranda -->
            <a href="beranda.php" class="flex flex-col items-center gap-1 text-[#2554E8] py-1 px-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-semibold">Beranda</span>
            </a>

            <!-- Tab: Jelajahi -> Mengarah ke /jelajahi.php -->
            <a href="jelajahi.php" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-600 py-1 px-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 9.5l-2 5-5 2 2-5 5-2z"/>
                </svg>
                <span class="text-[10px] font-medium">Jelajahi</span>
            </a>

            <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-600 py-1 px-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/>
                </svg>
                <span class="text-[10px] font-medium">Bagikan</span>
            </a>

            <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-600 py-1 px-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span class="text-[10px] font-medium">Aktivitas</span>
            </a>

            <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-600 py-1 px-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[10px] font-medium">Profil</span>
            </a>
        </nav>

    </div>

    <script>
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
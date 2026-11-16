<?php
// Data Barang dengan path ../assets/ sesuai struktur folder sharespace-app
$items = [
    [
        'id' => 1,
        'title' => 'Meja Belajar',
        'category' => 'Furnitur',
        'location' => 'Badung',
        'distance' => 3.3,
        'condition' => 'Baik',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/meja-belajar.png'
    ],
    [
        'id' => 2,
        'title' => 'Tas Sekolah',
        'category' => 'Pakaian',
        'location' => 'Denpasar',
        'distance' => 5.8,
        'condition' => 'Seperti Baru',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/tas-sekolah.png'
    ],
    [
        'id' => 3,
        'title' => 'Buku',
        'category' => 'Alat Tulis',
        'location' => 'Tabanan',
        'distance' => 6.5,
        'condition' => 'Baik',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/buku.png'
    ],
    [
        'id' => 4,
        'title' => 'Boneka',
        'category' => 'Lainnya',
        'location' => 'Munggu',
        'distance' => 4.3,
        'condition' => 'Baik',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/boneka.png'
    ],
    [
        'id' => 5,
        'title' => 'Aksesori Elektronik',
        'category' => 'Elektronik',
        'location' => 'Tabanan',
        'distance' => 4.2,
        'condition' => 'Seperti Baru',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/aksesoris-elektronik.png'
    ],
    [
        'id' => 6,
        'title' => 'Makanan Kemasan',
        'category' => 'Makanan',
        'location' => 'Kuta',
        'distance' => 2.4,
        'condition' => 'Baru - Tersegel',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/makanan-kemasan.png'
    ],
    [
        'id' => 7,
        'title' => 'Roti Gandum',
        'category' => 'Makanan',
        'location' => 'Jimbaran',
        'distance' => 6.0,
        'condition' => 'Baru - Layak Konsumsi',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/roti-gandum.png'
    ],
    [
        'id' => 8,
        'title' => 'Peralatan Olahraga',
        'category' => 'Lainnya',
        'location' => 'Denpasar',
        'distance' => 7.6,
        'condition' => 'Seperti Baru',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/peralatan-olahraga.png'
    ],
    [
        'id' => 9,
        'title' => 'Cardigan',
        'category' => 'Pakaian',
        'location' => 'Denpasar',
        'distance' => 7.0,
        'condition' => 'Baru - Belum Dipakai',
        'status' => 'Tersedia',
        'liked' => true,
        'image' => '../assets/cardigan.png'
    ],
    [
        'id' => 10,
        'title' => 'Gitar',
        'category' => 'Lainnya',
        'location' => 'Denpasar',
        'distance' => 10.0,
        'condition' => 'Baru - Belum Dipakai',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/gitar.png'
    ],
    [
        'id' => 11,
        'title' => 'Beras Putih 1 kg',
        'category' => 'Makanan',
        'location' => 'Denpasar',
        'distance' => 8.5,
        'condition' => 'Baru - Tersegel',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/beras-putih.png'
    ],
    [
        'id' => 12,
        'title' => 'Mi Instan',
        'category' => 'Makanan',
        'location' => 'Karangasem',
        'distance' => 12.0,
        'condition' => 'Baru - Tersegel',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/mi-instan.png'
    ],
    [
        'id' => 13,
        'title' => 'Pensil Warna',
        'category' => 'Alat Tulis',
        'location' => 'Denpasar',
        'distance' => 7.2,
        'condition' => 'Seperti Baru',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/pensil-warna.png'
    ],
    [
        'id' => 14,
        'title' => 'Penggaris',
        'category' => 'Alat Tulis',
        'location' => 'Denpasar',
        'distance' => 6.8,
        'condition' => 'Seperti Baru',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/penggaris.png'
    ],
    [
        'id' => 15,
        'title' => 'Earphone Bluetooth',
        'category' => 'Elektronik',
        'location' => 'Denpasar',
        'distance' => 10.0,
        'condition' => 'Baru - Belum Dipakai',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/tws.png'
    ],
    [
        'id' => 16,
        'title' => 'Pulpen',
        'category' => 'Alat Tulis',
        'location' => 'Denpasar',
        'distance' => 10.0,
        'condition' => 'Baru - Belum Dipakai',
        'status' => 'Tersedia',
        'liked' => false,
        'image' => '../assets/image 5.png'
    ],
];

$categories = ['Semua', 'Makanan', 'Pakaian', 'Elektronik', 'Alat Tulis', 'Furnitur', 'Lainnya'];
$locationOptions = [
    'Jimbaran, Bali · radius 3 km',
    'Jimbaran, Bali · radius 5 km',
    'Jimbaran, Bali · radius 10 km',
    'Semua wilayah Bali'
];
$conditionOptions = ['Baru', 'Seperti Baru', 'Baik', 'Cukup'];

$selectedCategory = $_GET['category'] ?? 'Semua';
$searchQuery      = trim($_GET['q'] ?? '');
$selectedLocation = $_GET['lokasi'] ?? 'Jimbaran, Bali · radius 3 km';
$selectedKondisi  = $_GET['kondisi'] ?? '';
$selectedStatus   = $_GET['ketersediaan'] ?? 'Hanya tersedia';
$selectedSort     = $_GET['urutkan'] ?? 'Terbaru';

$filteredItems = array_filter($items, function ($item) use ($selectedCategory, $searchQuery, $selectedKondisi, $selectedStatus) {
    $matchCategory = ($selectedCategory === 'Semua') || (strcasecmp($item['category'], $selectedCategory) === 0);
    $matchSearch   = ($searchQuery === '') || (stripos($item['title'], $searchQuery) !== false);
    $matchKondisi  = ($selectedKondisi === '') || (stripos($item['condition'], $selectedKondisi) === 0);
    $matchStatus   = ($selectedStatus === 'Semua barang') || ($item['status'] === 'Tersedia');

    return $matchCategory && $matchSearch && $matchKondisi && $matchStatus;
});

if ($selectedSort === 'Terdekat') {
    usort($filteredItems, function ($a, $b) {
        return $a['distance'] <=> $b['distance'];
    });
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi - Katalog Barang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #1f2937;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="flex justify-center min-h-screen">

    <!-- Container Mobile View -->
    <div class="w-full max-w-[430px] bg-[#F8FAFC] min-h-screen flex flex-col relative shadow-2xl overflow-x-clip">
        
        <!-- Header & Search Section -->
        <header class="bg-white px-5 pt-6 pb-3 sticky top-0 z-20 border-b border-slate-100">
            <h1 class="text-[26px] font-bold text-slate-900 tracking-tight leading-tight">Jelajahi</h1>
            <p class="text-xs text-slate-500 mt-0.5 mb-4">Temukan barang berguna dari sesama.</p>

            <!-- Search Bar -->
            <form method="GET" action="jelajahi.php" class="relative mb-3.5">
                <?php if ($selectedCategory !== 'Semua'): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                <?php endif; ?>
                <?php if ($selectedKondisi !== ''): ?>
                    <input type="hidden" name="kondisi" value="<?= htmlspecialchars($selectedKondisi) ?>">
                <?php endif; ?>
                <input type="hidden" name="ketersediaan" value="<?= htmlspecialchars($selectedStatus) ?>">
                <input type="hidden" name="urutkan" value="<?= htmlspecialchars($selectedSort) ?>">

                <span class="absolute inset-y-0 left-3.5 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z" />
                    </svg>
                </span>
                <input 
                    type="text" 
                    name="q" 
                    value="<?= htmlspecialchars($searchQuery) ?>"
                    placeholder="Cari barang yang kamu butuhkan..." 
                    class="w-full bg-white border border-slate-200 text-slate-800 text-xs rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 placeholder-slate-400 shadow-xs"
                >
            </form>

            <!-- Horizontal Category Pills -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                <?php foreach ($categories as $cat): 
                    $isActive = ($selectedCategory === $cat);
                    $queryParams = array_merge($_GET, ['category' => $cat]);
                    $url = 'jelajahi.php?' . http_build_query($queryParams);
                ?>
                    <a href="<?= htmlspecialchars($url) ?>" 
                       class="px-4 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all <?= $isActive ? 'bg-[#2554E8] text-white shadow-xs' : 'bg-slate-100/90 text-slate-600 hover:bg-slate-200/70' ?>">
                        <?= htmlspecialchars($cat) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </header>

        <!-- Sub-header: Lokasi, Tombol Filter, Jumlah Item & Sort -->
        <div class="px-5 pt-3 pb-2">
            <div class="flex items-center justify-between mb-3">
                <button type="button" onclick="openFilterModal()" class="flex items-center gap-1.5 text-xs font-medium text-slate-700 hover:text-slate-900">
                    <svg class="w-4 h-4 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                        <circle cx="12" cy="11" r="2.5"/>
                    </svg>
                    <span>Jimbaran, Bali</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <button type="button" onclick="openFilterModal()" class="relative flex items-center gap-1.5 bg-white border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 shadow-2xs hover:bg-slate-50 active:scale-95 transition-transform">
                    <svg class="w-3.5 h-3.5 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/>
                    </svg>
                    <span>Filter</span>
                    <?php if ($selectedKondisi !== '' || $selectedSort !== 'Terbaru'): ?>
                        <span class="w-2 h-2 rounded-full bg-[#2554E8]"></span>
                    <?php endif; ?>
                </button>
            </div>

            <div class="flex items-center justify-between text-xs">
                <p class="font-semibold text-slate-800">
                    <?= count($filteredItems) ?> <?= $selectedCategory === 'Semua' ? 'barang' : strtolower(htmlspecialchars($selectedCategory)) ?> di sekitar
                </p>
                <button type="button" onclick="openFilterModal()" class="flex items-center gap-1 text-slate-500 hover:text-slate-800">
                    <span><?= htmlspecialchars($selectedSort) ?></span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Product Grid -->
        <main class="px-5 pt-2 pb-24 flex-1">
            <?php if (empty($filteredItems)): ?>
                <div class="text-center py-12 bg-white rounded-2xl border border-slate-100 mt-2">
                    <p class="text-sm font-medium text-slate-600">Barang tidak ditemukan.</p>
                    <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci atau reset filter kamu.</p>
                    <a href="jelajahi.php" class="inline-block mt-3 px-4 py-1.5 bg-blue-50 text-[#2554E8] rounded-full text-xs font-medium hover:bg-blue-100">Reset semua filter</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 gap-3.5">
                    <?php foreach ($filteredItems as $item): ?>
                        <article class="bg-white rounded-2xl border border-slate-100 shadow-2xs overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                            <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden">
                                <img 
                                    src="<?= htmlspecialchars($item['image']) ?>" 
                                    alt="<?= htmlspecialchars($item['title']) ?>" 
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                                <button 
                                    type="button"
                                    onclick="toggleLike(this)"
                                    class="absolute top-2.5 right-2.5 w-7 h-7 rounded-full bg-white/95 backdrop-blur-xs flex items-center justify-center shadow-xs transition-transform active:scale-90"
                                    aria-label="Sukai barang"
                                >
                                    <?php if ($item['liked']): ?>
                                        <svg class="w-4 h-4 text-rose-500 fill-rose-500" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                    <?php endif; ?>
                                </button>
                            </div>

                            <div class="p-3 flex-1 flex flex-col justify-between">
                                <div>
                                    <h2 class="text-[13px] font-semibold text-slate-900 line-clamp-1">
                                        <?= htmlspecialchars($item['title']) ?>
                                    </h2>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        <?= htmlspecialchars($item['category']) ?>
                                    </p>

                                    <div class="flex items-center gap-1 text-[10px] text-slate-500 mt-2">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                                            <circle cx="12" cy="11" r="2"/>
                                        </svg>
                                        <span class="truncate"><?= htmlspecialchars($item['location']) ?> • <?= number_format($item['distance'], $item['distance'] == floor($item['distance']) ? 0 : 1) ?> km</span>
                                    </div>

                                    <p class="text-[10px] text-slate-500 mt-0.5 truncate">
                                        <?= htmlspecialchars($item['condition']) ?>
                                    </p>
                                </div>

                                <div class="mt-2.5">
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-emerald-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <!-- Bottom Navigation Bar -->
        <nav class="fixed bottom-0 w-full max-w-[430px] bg-white border-t border-slate-100 px-3 py-2 flex items-center justify-around z-30">
            <!-- Tab: Beranda -> Mengarah ke ../beranda/beranda.php -->
            <a href="./beranda.php" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-600 py-1 px-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-medium">Beranda</span>
            </a>

            <!-- Active Tab: Jelajahi -->
            <a href="jelajahi.php" class="flex flex-col items-center gap-1 text-[#2554E8] py-1 px-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 9.5l-2 5-5 2 2-5 5-2z"/>
                </svg>
                <span class="text-[10px] font-semibold">Jelajahi</span>
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

        <!-- POP-UP BOTTOM SHEET FILTER BARANG -->
        <div id="filterModal" class="fixed inset-0 z-50 hidden flex justify-center items-end">
            <div 
                id="filterBackdrop" 
                onclick="closeFilterModal()" 
                class="fixed inset-0 bg-black/60 backdrop-blur-[1px] opacity-0 transition-opacity duration-300"
            ></div>

            <div 
                id="filterSheet" 
                class="relative w-full max-w-[430px] bg-white rounded-t-[28px] px-6 pt-3 pb-7 z-10 shadow-2xl transform translate-y-full transition-transform duration-300 ease-out"
            >
                <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto mb-4"></div>

                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-[20px] font-bold text-slate-900 tracking-tight">Filter barang</h2>
                    <button type="button" onclick="closeFilterModal()" class="p-1.5 text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form method="GET" action="jelajahi.php" class="space-y-5">
                    <?php if ($selectedCategory !== 'Semua'): ?>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                    <?php endif; ?>
                    <?php if ($searchQuery !== ''): ?>
                        <input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>">
                    <?php endif; ?>

                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-2">Lokasi</label>
                        <div class="relative">
                            <select name="lokasi" class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium text-slate-700 focus:outline-none focus:border-[#2554E8]">
                                <?php foreach ($locationOptions as $loc): ?>
                                    <option value="<?= htmlspecialchars($loc) ?>" <?= $selectedLocation === $loc ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($loc) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-3.5 flex items-center text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-2">Kondisi</label>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($conditionOptions as $cond): ?>
                                <label class="cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="kondisi" 
                                        value="<?= htmlspecialchars($cond) ?>" 
                                        class="peer sr-only"
                                        <?= $selectedKondisi === $cond ? 'checked' : '' ?>
                                        onclick="toggleRadio(this)"
                                    >
                                    <span class="inline-block px-4 py-2 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-600 peer-checked:bg-[#2554E8] peer-checked:text-white peer-checked:border-[#2554E8] transition-all">
                                        <?= htmlspecialchars($cond) ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-2">Ketersediaan</label>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach (['Semua barang', 'Hanya tersedia'] as $statusOpt): ?>
                                <label class="cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="ketersediaan" 
                                        value="<?= htmlspecialchars($statusOpt) ?>" 
                                        class="peer sr-only"
                                        <?= $selectedStatus === $statusOpt ? 'checked' : '' ?>
                                    >
                                    <span class="inline-block px-4 py-2 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-600 peer-checked:bg-[#2554E8] peer-checked:text-white peer-checked:border-[#2554E8] transition-all">
                                        <?= htmlspecialchars($statusOpt) ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-800 mb-2">Urutkan</label>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach (['Terbaru', 'Terdekat'] as $sortOpt): ?>
                                <label class="cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="urutkan" 
                                        value="<?= htmlspecialchars($sortOpt) ?>" 
                                        class="peer sr-only"
                                        <?= $selectedSort === $sortOpt ? 'checked' : '' ?>
                                    >
                                    <span class="inline-block px-4 py-2 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-600 peer-checked:bg-[#2554E8] peer-checked:text-white peer-checked:border-[#2554E8] transition-all">
                                        <?= htmlspecialchars($sortOpt) ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="pt-3 space-y-2.5">
                        <button type="submit" class="w-full py-3.5 rounded-2xl bg-[#2554E8] hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors">
                            Terapkan filter
                        </button>
                        <a href="jelajahi.php<?= $selectedCategory !== 'Semua' ? '?category=' . urlencode($selectedCategory) : '' ?>" class="block w-full py-3.5 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50 text-[#2554E8] text-xs font-semibold text-center transition-colors">
                            Hapus filter
                        </a>
                    </div>
                </form>

                <div class="w-32 h-1 bg-slate-900 rounded-full mx-auto mt-6"></div>
            </div>
        </div>

    </div>

    <script>
        const filterModal = document.getElementById('filterModal');
        const filterBackdrop = document.getElementById('filterBackdrop');
        const filterSheet = document.getElementById('filterSheet');

        function openFilterModal() {
            filterModal.classList.remove('hidden');
            requestAnimationFrame(() => {
                filterBackdrop.classList.remove('opacity-0');
                filterBackdrop.classList.add('opacity-100');
                filterSheet.classList.remove('translate-y-full');
                filterSheet.classList.add('translate-y-0');
            });
            document.body.style.overflow = 'hidden';
        }

        function closeFilterModal() {
            filterBackdrop.classList.remove('opacity-100');
            filterBackdrop.classList.add('opacity-0');
            filterSheet.classList.remove('translate-y-0');
            filterSheet.classList.add('translate-y-full');
            
            setTimeout(() => {
                filterModal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        let lastCheckedRadio = document.querySelector('input[name="kondisi"]:checked');
        function toggleRadio(radio) {
            if (lastCheckedRadio === radio) {
                radio.checked = false;
                lastCheckedRadio = null;
            } else {
                lastCheckedRadio = radio;
            }
        }

        function toggleLike(btn) {
            const svg = btn.querySelector('svg');
            const isLiked = svg.classList.contains('text-rose-500');
            if (isLiked) {
                btn.innerHTML = `<svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>`;
            } else {
                btn.innerHTML = `<svg class="w-4 h-4 text-rose-500 fill-rose-500" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>`;
            }
        }
    </script>
</body>
</html>
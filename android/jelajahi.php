
<?php
// =============================================
// DATA BARANG
// =============================================

$items = [
    ['id'=>1,'title'=>'Meja Belajar','category'=>'Furnitur','location'=>'Badung','condition'=>'Baik','status'=>'Tersedia','created_at'=>'2026-11-15','image'=>'../assets/meja-belajar.png'],
    ['id'=>2,'title'=>'Tas Sekolah','category'=>'Pakaian','location'=>'Denpasar','condition'=>'Seperti Baru','status'=>'Tersedia','created_at'=>'2026-11-14','image'=>'../assets/tas-sekolah.png'],
    ['id'=>3,'title'=>'Buku','category'=>'Alat Tulis','location'=>'Tabanan','condition'=>'Baik','status'=>'Tersedia','created_at'=>'2026-11-13','image'=>'../assets/buku.png'],
    ['id'=>4,'title'=>'Boneka','category'=>'Lainnya','location'=>'Badung','condition'=>'Baik','status'=>'Tersedia','created_at'=>'2026-11-12','image'=>'../assets/boneka.png'],
    ['id'=>5,'title'=>'Aksesori Elektronik','category'=>'Elektronik','location'=>'Tabanan','condition'=>'Seperti Baru','status'=>'Tersedia','created_at'=>'2026-11-11','image'=>'../assets/aksesoris-elektronik.png'],
    ['id'=>6,'title'=>'Makanan Kemasan','category'=>'Makanan','location'=>'Badung','condition'=>'Baru','status'=>'Tersedia','created_at'=>'2026-11-10','image'=>'../assets/makanan-kemasan.png'],
    ['id'=>7,'title'=>'Roti Gandum','category'=>'Makanan','location'=>'Badung','condition'=>'Baik','status'=>'Tersedia','created_at'=>'2026-11-09','image'=>'../assets/roti-gandum.png'],
    ['id'=>8,'title'=>'Peralatan Olahraga','category'=>'Lainnya','location'=>'Denpasar','condition'=>'Seperti Baru','status'=>'Tersedia','created_at'=>'2026-11-08','image'=>'../assets/peralatan-olahraga.png'],
    ['id'=>9,'title'=>'Cardigan','category'=>'Pakaian','location'=>'Denpasar','condition'=>'Baik','status'=>'Tersedia','created_at'=>'2026-11-07','image'=>'../assets/cardigan.png'],
    ['id'=>10,'title'=>'Gitar','category'=>'Lainnya','location'=>'Denpasar','condition'=>'Baik','status'=>'Tersedia','created_at'=>'2026-11-06','image'=>'../assets/gitar.png'],
    ['id'=>11,'title'=>'Beras Putih 1 kg','category'=>'Makanan','location'=>'Denpasar','condition'=>'Baru','status'=>'Tersedia','created_at'=>'2026-11-05','image'=>'../assets/beras-putih.png'],
    ['id'=>12,'title'=>'Mi Instan','category'=>'Makanan','location'=>'Karangasem','condition'=>'Baru','status'=>'Tersedia','created_at'=>'2026-11-04','image'=>'../assets/mi-instan.png'],
    ['id'=>13,'title'=>'Pensil Warna','category'=>'Alat Tulis','location'=>'Denpasar','condition'=>'Seperti Baru','status'=>'Tersedia','created_at'=>'2026-11-03','image'=>'../assets/pensil-warna.png'],
    ['id'=>14,'title'=>'Penggaris','category'=>'Alat Tulis','location'=>'Denpasar','condition'=>'Seperti Baru','status'=>'Tersedia','created_at'=>'2026-11-02','image'=>'../assets/penggaris.png'],
    ['id'=>15,'title'=>'Earphone Bluetooth','category'=>'Elektronik','location'=>'Denpasar','condition'=>'Baru','status'=>'Tersedia','created_at'=>'2026-11-01','image'=>'../assets/tws.png'],
    ['id'=>16,'title'=>'Pulpen','category'=>'Alat Tulis','location'=>'Denpasar','condition'=>'Baru','status'=>'Tersedia','created_at'=>'2026-10-30','image'=>'../assets/image 5.png']
];

// =============================================
// PILIHAN FILTER
// =============================================

$categories = ['Semua','Makanan','Pakaian','Elektronik','Alat Tulis','Furnitur','Lainnya'];
$locations = ['Semua Lokasi','Tabanan','Badung','Buleleng','Denpasar','Jembrana','Gianyar','Karangasem','Klungkung','Bangli'];
$conditions = ['Baru','Seperti Baru','Baik','Cukup'];
$sortOptions = ['Terbaru','Terlama'];

// =============================================
// INPUT FILTER
// =============================================

$category = $_GET['category'] ?? 'Semua';
$search = trim($_GET['q'] ?? '');
$location = $_GET['lokasi'] ?? 'Semua Lokasi';
$condition = $_GET['kondisi'] ?? '';
$availability = $_GET['ketersediaan'] ?? 'Hanya tersedia';
$sort = $_GET['urutkan'] ?? 'Terbaru';

if (!in_array($category,$categories,true)) $category = 'Semua';
if (!in_array($location,$locations,true)) $location = 'Semua Lokasi';
if (!in_array($condition,$conditions,true)) $condition = '';
if (!in_array($availability,['Semua barang','Hanya tersedia'],true)) $availability = 'Hanya tersedia';
if (!in_array($sort,$sortOptions,true)) $sort = 'Terbaru';

// =============================================
// FILTER BARANG
// =============================================

$filteredItems = array_filter($items,function($item) use ($category,$search,$location,$condition,$availability) {
    $matchCategory = $category === 'Semua' || $item['category'] === $category;
    $matchSearch = $search === '' || stripos($item['title'],$search) !== false;
    $matchLocation = $location === 'Semua Lokasi' || $item['location'] === $location;
    $matchCondition = $condition === '' || $item['condition'] === $condition;
    $matchAvailability = $availability === 'Semua barang' || $item['status'] === 'Tersedia';

    return $matchCategory && $matchSearch && $matchLocation && $matchCondition && $matchAvailability;
});

$filteredItems = array_values($filteredItems);

usort($filteredItems,function($a,$b) use ($sort) {
    return $sort === 'Terlama'
        ? strcmp($a['created_at'],$b['created_at'])
        : strcmp($b['created_at'],$a['created_at']);
});

function buildUrl($params = []) {
    return 'jelajahi.php?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi - ShareSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
        .product-card { animation: fadeIn .25s ease; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        body.modal-open { overflow: hidden; }
    </style>
</head>

<body class="min-h-screen bg-white lg:flex">

    <!-- SIDEBAR DESKTOP: mengikuti struktur dan posisi sidebar Beranda -->
    <aside class="hidden lg:flex lg:flex-col w-[267px] shrink-0 sticky top-0 h-screen bg-white border-r border-slate-200/70 px-6 pt-10 pb-8">
        <div>
            <a href="beranda.php" class="flex items-center gap-3">
                <div class="w-[52px] h-[52px] rounded-[14px] bg-[#2554E8] flex items-center justify-center shadow-xs shrink-0">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" transform="translate(3.5, -1) scale(0.65)" />
                        <path d="M2 14.5l3.5 3.5" />
                        <path d="M4.5 17l3.5-3.5a2 2 0 0 1 2.8 0l1.2 1.2a2 2 0 0 0 2.8 0l3.7-3.7a1.8 1.8 0 0 1 2.5 2.5l-5.3 5.3a4 4 0 0 1-2.8 1.2H7.5" />
                        <path d="M9.5 15.5h3" />
                    </svg>
                </div>
                <span class="text-[24px] font-bold text-slate-900 tracking-tight">ShareSpace</span>
            </a>

            <p class="mt-[78px] mb-2 px-2 text-sm tracking-wide text-slate-500">MENU</p>

            <nav class="flex flex-col gap-0.5">
                <a href="beranda.php" class="flex items-center gap-3 px-2 h-11 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Beranda
                </a>

                <a href="jelajahi.php" class="flex items-center gap-3 px-2 h-11 rounded-xl text-sm font-semibold text-[#2554E8]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 9.5l-2 5-5 2 2-5 5-2z"/>
                    </svg>
                    Jelajahi
                </a>

                <a href="../bagikan/index.php" class="flex items-center gap-3 px-2 h-11 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/>
                    </svg>
                    Bagikan
                </a>

                <a href="../aktivitas/index.php" class="flex items-center gap-3 px-2 h-11 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Aktivitas
                </a>

                <a href="../profil/index.php" class="flex items-center gap-3 px-2 h-11 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profil
                </a>
            </nav>
        </div>

        <a href="#" class="mt-auto flex items-center gap-3 px-2">
            <span class="w-9 h-9 rounded-full bg-[#EEF2FF] text-[#2554E8] text-xs font-bold flex items-center justify-center">AN</span>
            <span class="text-sm font-semibold text-slate-900">Username</span>
        </a>
    </aside>

    <!-- AREA KONTEN -->
    <div class="w-full flex-1 min-w-0 min-h-screen flex flex-col relative lg:bg-[#F8FAFD]">

        <!-- HEADER -->
        <header class="px-5 pt-6 pb-4 lg:px-12 lg:pt-8 lg:pb-5 bg-white lg:bg-transparent">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1 lg:hidden">
                        <div class="w-8 h-8 rounded-[10px] bg-[#2554E8] flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" transform="translate(3.5, -1) scale(0.65)" />
                                <path d="M2 14.5l3.5 3.5" />
                                <path d="M4.5 17l3.5-3.5a2 2 0 0 1 2.8 0l1.2 1.2a2 2 0 0 0 2.8 0l3.7-3.7a1.8 1.8 0 0 1 2.5 2.5l-5.3 5.3a4 4 0 0 1-2.8 1.2H7.5" />
                            </svg>
                        </div>
                        <span class="text-[19px] font-bold text-slate-900 tracking-tight">ShareSpace</span>
                    </div>
                    <h1 class="text-[26px] lg:text-[32px] font-bold text-slate-900 tracking-tight">Jelajahi</h1>
                    <p class="text-[13px] lg:text-sm text-slate-500 mt-1">Temukan barang berguna dari sesama.</p>
                </div>

                <div class="flex items-center">
                    <a href="../profil/index.php" class="lg:hidden w-10 h-10 rounded-full bg-[#EEF2FF] text-[#2554E8] text-xs font-bold flex items-center justify-center">AN</a>
                </div>
            </div>

            <!-- SEARCH -->
            <form method="GET" action="jelajahi.php" class="mt-5 flex gap-2.5">
                <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                <input type="hidden" name="lokasi" value="<?= htmlspecialchars($location) ?>">
                <input type="hidden" name="kondisi" value="<?= htmlspecialchars($condition) ?>">
                <input type="hidden" name="ketersediaan" value="<?= htmlspecialchars($availability) ?>">
                <input type="hidden" name="urutkan" value="<?= htmlspecialchars($sort) ?>">

                <div class="relative flex-1 min-w-0">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.35z"/>
                    </svg>
                    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari barang yang kamu butuhkan..."
                        class="w-full h-12 lg:h-14 bg-white border border-slate-200 text-slate-800 text-[13px] lg:text-sm rounded-2xl pl-12 pr-4 focus:outline-none focus:border-[#2554E8] placeholder-slate-400">
                </div>
                <button type="submit" class="hidden sm:block h-12 lg:h-14 px-6 lg:px-8 rounded-2xl bg-[#2554E8] hover:bg-[#1f47c9] text-white text-sm font-semibold transition-colors">
                    Cari barang
                </button>
            </form>

            <!-- KATEGORI -->
            <div id="categoryScroller" class="flex gap-2 overflow-x-auto no-scrollbar mt-4 pb-1">
                <?php foreach ($categories as $cat):
                    $params = $_GET;
                    $params['category'] = $cat;
                    $active = $category === $cat;
                ?>
                    <a href="<?= htmlspecialchars(buildUrl($params)) ?>" data-active="<?= $active ? 'true' : 'false' ?>"
                        class="shrink-0 px-4 py-2 rounded-full text-xs lg:text-sm font-medium transition-all <?= $active ? 'bg-[#2554E8] text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-100' ?>">
                        <?= htmlspecialchars($cat) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </header>

        <!-- LOKASI DAN FILTER -->
        <div class="px-5 lg:px-12 pt-2 pb-3">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 text-xs lg:text-sm font-medium text-slate-700 min-w-0">
                    <svg class="w-4 h-4 text-[#2554E8] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                        <circle cx="12" cy="11" r="2.5"/>
                    </svg>
                    <span class="truncate"><?= htmlspecialchars($location) ?></span>
                </div>

                <button type="button" onclick="openFilterModal()" class="flex items-center gap-2 bg-white border border-slate-200 px-3.5 py-2 rounded-xl text-xs lg:text-sm font-semibold text-slate-700 hover:border-[#2554E8] transition-colors shrink-0">
                    <svg class="w-4 h-4 text-[#2554E8]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M7 12h10M10 18h4"/>
                    </svg>
                    Filter
                    <?php if ($location !== 'Semua Lokasi' || $condition !== '' || $availability !== 'Hanya tersedia' || $sort !== 'Terbaru'): ?>
                        <span class="w-2 h-2 rounded-full bg-[#2554E8]"></span>
                    <?php endif; ?>
                </button>
            </div>
        </div>

        <!-- DAFTAR BARANG -->
        <main class="px-5 lg:px-12 pt-2 pb-24 lg:pb-12 flex-1">
            <?php if (empty($filteredItems)): ?>
                <div class="text-center py-16 bg-white rounded-2xl border border-slate-100 mt-2">
                    <div class="w-14 h-14 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20 20-4-4"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Barang tidak ditemukan.</p>
                    <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci atau filter kamu.</p>
                    <a href="jelajahi.php" class="inline-block mt-4 px-4 py-2 bg-blue-50 text-[#2554E8] rounded-full text-xs font-semibold">Reset filter</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4 lg:gap-5">
                    <?php foreach ($filteredItems as $item): ?>
                        <article class="product-card bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden flex flex-col hover:shadow-md transition-shadow min-w-0">
                            <div class="relative aspect-[4/3] lg:aspect-[16/10] bg-slate-100 overflow-hidden">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>" loading="lazy" class="w-full h-full object-cover">
                            </div>

                            <div class="p-3 lg:p-3.5 flex-1 flex flex-col justify-between gap-2">
                                <div>
                                    <h2 class="text-[13px] lg:text-base font-bold text-slate-900 truncate"><?= htmlspecialchars($item['title']) ?></h2>
                                    <p class="text-[11px] lg:text-sm text-slate-500 mt-0.5"><?= htmlspecialchars($item['category']) ?></p>

                                    <div class="flex items-center gap-1.5 text-[10px] lg:text-sm text-slate-500 mt-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6-5.686-6-10a6 6 0 1112 0c0 4.314-6 10-6 10z"/>
                                            <circle cx="12" cy="11" r="2"/>
                                        </svg>
                                        <span class="truncate"><?= htmlspecialchars($item['location']) ?></span>
                                    </div>
                                    <p class="text-[10px] lg:text-sm text-slate-500 mt-1 truncate"><?= htmlspecialchars($item['condition']) ?></p>
                                </div>

                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] lg:text-xs font-semibold bg-emerald-50 text-emerald-600">
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
    </div>

    <!-- BOTTOM NAVIGATION: mobile dan tablet, sama seperti Beranda -->
    <nav class="fixed inset-x-0 bottom-0 bg-white border-t border-slate-100 px-3 py-2 flex items-center justify-around z-30 lg:hidden">
        <a href="../beranda/beranda.php" class="flex flex-col items-center gap-1 py-1 px-2 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3"/>
            </svg>
            <span class="text-[10px] font-medium">Beranda</span>
        </a>
        <a href="jelajahi.php" class="flex flex-col items-center gap-1 py-1 px-2 text-[#2554E8]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 9.5l-2 5-5 2 2-5 5-2z"/>
            </svg>
            <span class="text-[10px] font-semibold">Jelajahi</span>
        </a>
        <a href="../bagikan/index.php" class="flex flex-col items-center gap-1 py-1 px-2 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/>
            </svg>
            <span class="text-[10px] font-medium">Bagikan</span>
        </a>
        <a href="../aktivitas/index.php" class="flex flex-col items-center gap-1 py-1 px-2 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span class="text-[10px] font-medium">Aktivitas</span>
        </a>
        <a href="../profil/index.php" class="flex flex-col items-center gap-1 py-1 px-2 text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-[10px] font-medium">Profil</span>
        </a>
    </nav>

    <!-- FILTER MODAL -->
<div id="filterModal" class="fixed inset-0 z-50 hidden flex justify-center items-end md:items-center">

    <div id="filterBackdrop" onclick="closeFilterModal()" class="fixed inset-0 bg-black/60 opacity-0 transition-opacity duration-300"></div>

    <div id="filterSheet" class="relative w-full max-w-[430px] bg-[#F8FAFC] rounded-t-[28px] md:rounded-[24px] px-6 pt-3 pb-7 z-10 shadow-2xl translate-y-full md:translate-y-5 transition-all duration-300 ease-out">

        <div class="w-10 h-1 bg-slate-200 rounded-full mx-auto mb-4 md:hidden"></div>

        <div class="flex items-center justify-between mb-5">
            <h2 class="text-[20px] font-bold text-slate-900">Filter barang</h2>
            <button type="button" onclick="closeFilterModal()" class="p-1.5 text-slate-500 hover:text-slate-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="GET" action="jelajahi.php" class="space-y-5">
            <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
            <input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>">

            <!-- LOKASI -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-2">Lokasi</label>
                <div class="relative">
                    <select name="lokasi" class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium text-slate-700 focus:outline-none focus:border-[#2554E8]">
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= htmlspecialchars($loc) ?>" <?= $location === $loc ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <svg class="absolute right-3.5 top-3.5 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </div>

            <!-- KONDISI -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-2">Kondisi</label>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($conditions as $cond): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="kondisi" value="<?= htmlspecialchars($cond) ?>" class="peer sr-only" <?= $condition === $cond ? 'checked' : '' ?>>
                            <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-600 peer-checked:bg-[#2554E8] peer-checked:text-white peer-checked:border-[#2554E8] transition-all">
                                <?= htmlspecialchars($cond) ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- KETERSEDIAAN -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-2">Ketersediaan</label>
                <div class="flex flex-wrap gap-2">
                    <?php foreach (['Semua barang','Hanya tersedia'] as $status): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="ketersediaan" value="<?= htmlspecialchars($status) ?>" class="peer sr-only" <?= $availability === $status ? 'checked' : '' ?>>
                            <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-600 peer-checked:bg-[#2554E8] peer-checked:text-white peer-checked:border-[#2554E8] transition-all">
                                <?= htmlspecialchars($status) ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- URUTKAN -->
            <div>
                <label class="block text-xs font-semibold text-slate-800 mb-2">Urutkan</label>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($sortOptions as $option): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="urutkan" value="<?= htmlspecialchars($option) ?>" class="peer sr-only" <?= $sort === $option ? 'checked' : '' ?>>
                            <span class="inline-block px-3.5 py-1.5 rounded-full text-xs font-medium border border-slate-200 bg-white text-slate-600 peer-checked:bg-[#2554E8] peer-checked:text-white peer-checked:border-[#2554E8] transition-all">
                                <?= htmlspecialchars($option) ?>
                            </span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- BUTTON -->
            <div class="pt-1 space-y-2.5">
                <button type="submit" class="w-full py-3.5 rounded-xl bg-[#2554E8] hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition-colors">
                    Terapkan filter
                </button>

                <a href="<?= htmlspecialchars(buildUrl(array_filter(['category'=>$category !== 'Semua' ? $category : null,'q'=>$search !== '' ? $search : null]))) ?>"
                    class="block w-full py-3.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-[#2554E8] text-xs font-semibold text-center">
                    Hapus filter
                </a>
            </div>
        </form>

        <div class="w-32 h-1 bg-slate-900 rounded-full mx-auto mt-6 md:hidden"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const scroller = document.getElementById('categoryScroller');
    if (!scroller) return;

    const active = scroller.querySelector('[data-active="true"]');
    if (active && window.innerWidth < 640) {
        const position = active.offsetLeft - (scroller.clientWidth / 2) + (active.offsetWidth / 2);
        scroller.scrollLeft = Math.max(0, Math.min(position, scroller.scrollWidth - scroller.clientWidth));
    }
});

const modal = document.getElementById('filterModal');
const backdrop = document.getElementById('filterBackdrop');
const sheet = document.getElementById('filterSheet');

function openFilterModal() {
    modal.classList.remove('hidden');
    document.body.classList.add('modal-open');

    requestAnimationFrame(() => {
        backdrop.classList.remove('opacity-0');
        backdrop.classList.add('opacity-100');
        sheet.classList.remove('translate-y-full', 'md:translate-y-5');
        sheet.classList.add('translate-y-0');
    });
}

function closeFilterModal() {
    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    sheet.classList.remove('translate-y-0');
    sheet.classList.add('translate-y-full', 'md:translate-y-5');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.classList.remove('modal-open');
    }, 300);
}
</script>

</body>
</html>

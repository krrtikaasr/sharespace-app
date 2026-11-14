<?php
session_start();

/* =====================================
   1. DATA PENGGUNA
===================================== */

// Sementara menggunakan username contoh.
// Nantinya sesuaikan dengan session dari proses login.
$username = $_SESSION['username'] ?? 'Username';

$inisial = strtoupper(substr($username, 0, 2));


/* =====================================
   2. DATA KATEGORI
===================================== */

$kategori = [
    [
        'nama' => 'Makanan',
        'icon' => 'utensils'
    ],
    [
        'nama' => 'Pakaian',
        'icon' => 'shirt'
    ],
    [
        'nama' => 'Elektronik',
        'icon' => 'headphones'
    ],
    [
        'nama' => 'Alat Tulis',
        'icon' => 'pencil'
    ],
    [
        'nama' => 'Furnitur',
        'icon' => 'armchair'
    ],
    [
        'nama' => 'Lainnya',
        'icon' => 'shapes'
    ]
];


/* =====================================
   3. DATA BARANG SEMENTARA
===================================== */

// Menggunakan gambar wood.jpg yang sudah tersedia.
// Ganti path gambar jika foto barang lainnya sudah tersedia.

$barang = [
    [
        'id' => 1,
        'nama' => 'Roti Artisan',
        'kategori' => 'Makanan',
        'lokasi' => 'Denpasar',
        'jarak' => '0,4 km',
        'waktu' => 'Baru hari ini',
        'status' => 'Tersedia',
        'gambar' => '../assets/images/wood.jpg'
    ],

    [
        'id' => 2,
        'nama' => 'Buku',
        'kategori' => 'Alat Tulis',
        'lokasi' => 'Jimbaran',
        'jarak' => '1,5 km',
        'waktu' => 'Kondisi baik',
        'status' => 'Tersedia',
        'gambar' => '../assets/images/wood.jpg'
    ],

    [
        'id' => 3,
        'nama' => 'Headphone',
        'kategori' => 'Elektronik',
        'lokasi' => 'Denpasar',
        'jarak' => '2,1 km',
        'waktu' => 'Baru kemarin',
        'status' => 'Tersedia',
        'gambar' => '../assets/images/wood.jpg'
    ]
];


/* =====================================
   4. PENCARIAN DAN FILTER
===================================== */

$cari = trim($_GET['q'] ?? '');

$filterKategori = trim($_GET['kategori'] ?? '');

$hasilBarang = array_filter(
    $barang,
    function ($item) use ($cari, $filterKategori) {

        $cocokCari = (
            $cari === '' ||
            stripos($item['nama'], $cari) !== false ||
            stripos($item['kategori'], $cari) !== false ||
            stripos($item['lokasi'], $cari) !== false
        );

        $cocokKategori = (
            $filterKategori === '' ||
            $item['kategori'] === $filterKategori
        );

        return $cocokCari && $cocokKategori;
    }
);


/* =====================================
   5. FUNGSI PENGAMANAN HTML
===================================== */

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/* =====================================
   6. FUNGSI IKON SVG
===================================== */

function icon($name)
{
    $icons = [

        'utensils' => '
            <path d="M4 2v7a3 3 0 0 0 6 0V2"/>
            <path d="M7 2v20"/>
            <path d="M17 2v20"/>
            <path d="M17 2c-2 2-3 5-3 8h6c0-3-1-6-3-8Z"/>
        ',

        'shirt' => '
            <path d="m8 3 4 2 4-2 5 4-3 4-2-1v12H8V10l-2 1-3-4 5-4Z"/>
        ',

        'headphones' => '
            <path d="M3 14v-3a9 9 0 0 1 18 0v3"/>
            <path d="M3 14h4v7H5a2 2 0 0 1-2-2v-5Z"/>
            <path d="M21 14h-4v7h2a2 2 0 0 0 2-2v-5Z"/>
        ',

        'pencil' => '
            <path d="m16 4 4 4"/>
            <path d="M4 20l4-.8L19 8a2.8 2.8 0 0 0-4-4L4 15l-.8 5Z"/>
        ',

        'armchair' => '
            <path d="M5 11V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v4"/>
            <path d="M5 11a3 3 0 0 0-3 3v3h20v-3a3 3 0 0 0-3-3H5Z"/>
            <path d="M6 17v4m12-4v4"/>
        ',

        'shapes' => '
            <rect x="3" y="3" width="8" height="8" rx="1"/>
            <circle cx="17" cy="7" r="4"/>
            <path d="m12 21 5-9 5 9h-10Z"/>
        '
    ];

    return '
        <svg viewBox="0 0 24 24"
             fill="none"
             stroke="currentColor"
             stroke-width="1.9"
             stroke-linecap="round"
             stroke-linejoin="round">
            ' . ($icons[$name] ?? $icons['shapes']) . '
        </svg>
    ';
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#f7f9fd">

    <title>Beranda | ShareSpace</title>

    <!-- CSS BERADA SATU FOLDER DENGAN INDEX.PHP -->
    <link rel="stylesheet" href="home.css?v=2">

</head>


<body>

<div class="app-shell">


    <!-- =================================
         HEADER
    ================================== -->

    <header class="topbar">

        <a href="index.php" class="brand">

            <span class="brand-mark">

                <svg viewBox="0 0 24 24">
                    <path d="M12 21s-8-4.5-8-10a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 11c0 5.5-8 10-8 10Z"/>
                    <path d="M7 12h3l1.3-2.2L13 15l1.2-3H17"/>
                </svg>

            </span>

            <span>ShareSpace</span>

        </a>


        <div class="top-actions">

            <button
                class="notification-btn"
                type="button"
                aria-label="Notifikasi"
            >

                <svg viewBox="0 0 24 24">
                    <path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z"/>
                    <path d="M10 21h4"/>
                </svg>

                <span class="notification-dot"></span>

            </button>


            <a href="../dashboard.php" class="avatar">
                <?= e($inisial) ?>
            </a>

        </div>

    </header>



    <main>


        <!-- =================================
             WELCOME
        ================================== -->

        <section class="welcome">

            <h1>
                Halo, <?= e($username) ?>!
            </h1>

            <p>
                Beri barang tak terpakai kehidupan baru.
            </p>

        </section>



        <!-- =================================
             SEARCH
        ================================== -->

        <form
            class="search-box"
            action="index.php"
            method="GET"
        >

            <?php if ($filterKategori !== ''): ?>

                <input
                    type="hidden"
                    name="kategori"
                    value="<?= e($filterKategori) ?>"
                >

            <?php endif; ?>


            <svg viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7"/>
                <path d="m16 16 5 5"/>
            </svg>


            <input
                type="search"
                name="q"
                value="<?= e($cari) ?>"
                placeholder="Cari barang yang kamu butuhkan..."
            >


            <?php if ($cari !== ''): ?>

                <a href="index.php" class="clear-search">
                    &times;
                </a>

            <?php endif; ?>

        </form>



        <!-- =================================
             KATEGORI
        ================================== -->

        <section class="category-grid">

            <?php foreach ($kategori as $item): ?>

                <a
                    href="index.php?kategori=<?= urlencode($item['nama']) ?>"
                    class="category-card <?= $filterKategori === $item['nama'] ? 'selected' : '' ?>"
                >

                    <span class="category-icon">

                        <?= icon($item['icon']) ?>

                    </span>


                    <span>
                        <?= e($item['nama']) ?>
                    </span>

                </a>

            <?php endforeach; ?>

        </section>



        <!-- =================================
             AKSES CEPAT
        ================================== -->

        <section class="quick-access">

            <h2>Akses Cepat</h2>


            <div class="quick-grid">


                <a
                    href="../dashboard.php"
                    class="quick-link"
                >

                    <svg viewBox="0 0 24 24">
                        <path d="m12 3 8 4.5v9L12 21l-8-4.5v-9L12 3Z"/>
                        <path d="m4.5 7.7 7.5 4.2 7.5-4.2"/>
                        <path d="M12 12v9"/>
                    </svg>

                    Donasi Saya

                </a>



                <a
                    href="../dashboard.php"
                    class="quick-link"
                >

                    <svg viewBox="0 0 24 24">
                        <path d="M20.8 8.8c0 5.2-8.8 11-8.8 11s-8.8-5.8-8.8-11A4.8 4.8 0 0 1 12 6.2a4.8 4.8 0 0 1 8.8 2.6Z"/>
                    </svg>

                    Favorit

                </a>


            </div>

        </section>



        <!-- =================================
             REKOMENDASI BARANG
        ================================== -->

        <section class="recommendations">


            <div class="section-heading">

                <h2>

                    <?php

                    if ($filterKategori !== '') {

                        echo 'Kategori ' . e($filterKategori);

                    } elseif ($cari !== '') {

                        echo 'Hasil Pencarian';

                    } else {

                        echo 'Rekomendasi untukmu';

                    }

                    ?>

                </h2>


                <a href="../jelajahi/index.php">
                    Lihat Semua
                </a>

            </div>



            <?php if (count($hasilBarang) > 0): ?>


                <div class="product-grid">


                    <?php foreach ($hasilBarang as $item): ?>


                        <article class="product-card">


                            <!-- GAMBAR BARANG -->

                            <div class="product-image">

                                <a
                                    href="../detail-barang.php?id=<?= (int) $item['id'] ?>"
                                    class="image-link"
                                >

                                    <img
                                        src="<?= e($item['gambar']) ?>"
                                        alt="<?= e($item['nama']) ?>"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                                    >

                                    <span
                                        class="image-placeholder"
                                        style="display:none;"
                                    >
                                        ShareSpace
                                    </span>

                                </a>



                                <!-- FAVORIT -->

                                <button
                                    class="favorite-btn"
                                    type="button"
                                    aria-label="Tambahkan ke favorit"
                                    onclick="toggleFavorite(this)"
                                >

                                    <svg viewBox="0 0 24 24">
                                        <path d="M20.8 8.8c0 5.2-8.8 11-8.8 11s-8.8-5.8-8.8-11A4.8 4.8 0 0 1 12 6.2a4.8 4.8 0 0 1 8.8 2.6Z"/>
                                    </svg>

                                </button>


                            </div>



                            <!-- INFORMASI BARANG -->

                            <div class="product-info">


                                <a
                                    href="../detail-barang.php?id=<?= (int) $item['id'] ?>"
                                    class="product-name"
                                >
                                    <?= e($item['nama']) ?>
                                </a>


                                <p class="product-category">
                                    <?= e($item['kategori']) ?>
                                </p>



                                <p class="product-location">

                                    <svg viewBox="0 0 24 24">
                                        <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/>
                                        <circle cx="12" cy="10" r="2.5"/>
                                    </svg>

                                    <?= e($item['lokasi']) ?>
                                    ·
                                    <?= e($item['jarak']) ?>

                                </p>



                                <p class="product-time">
                                    <?= e($item['waktu']) ?>
                                </p>



                                <span class="availability">

                                    <i></i>

                                    <?= e($item['status']) ?>

                                </span>


                            </div>


                        </article>


                    <?php endforeach; ?>


                </div>


            <?php else: ?>


                <div class="empty-state">

                    <strong>Barang tidak ditemukan</strong>

                    <p>
                        Coba gunakan kata kunci atau kategori lainnya.
                    </p>

                    <a href="index.php">
                        Tampilkan semua barang
                    </a>

                </div>


            <?php endif; ?>


        </section>


    </main>



    <!-- =================================
         BOTTOM NAVIGATION
    ================================== -->

    <nav class="bottom-nav">


        <a href="index.php" class="nav-item active">

            <svg viewBox="0 0 24 24">
                <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-6v-7h-4v7H4a1 1 0 0 1-1-1V10Z"/>
            </svg>

            <span>Beranda</span>

        </a>



        <a href="../jelajahi/index.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path d="m15.5 8.5-2.2 5.2-5.2 2.2 2.2-5.2 5.2-2.2Z"/>
            </svg>

            <span>Jelajahi</span>

        </a>



        <a href="../dashboard.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v10M7 12h10"/>
            </svg>

            <span>Bagikan</span>

        </a>



        <a href="../atur-jadwal.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <path d="M3 8h18v12H3z"/>
                <path d="M3 8l3-5h12l3 5"/>
                <path d="M8 13h8"/>
            </svg>

            <span>Aktivitas</span>

        </a>



        <a href="../dashboard.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 21a8 8 0 0 1 16 0"/>
            </svg>

            <span>Profil</span>

        </a>


    </nav>


</div>



<!-- =================================
     JAVASCRIPT
================================== -->

<script>

function toggleFavorite(button) {

    button.classList.toggle('is-favorite');

}

</script>


</body>
</html>
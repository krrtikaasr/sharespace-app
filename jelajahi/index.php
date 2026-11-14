
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// ==========================================
// 1. DATA BARANG DONASI (DATA SEMENTARA)
// ==========================================

$barang = [
    [
        "id" => 1,
        "nama" => "Meja Belajar",
        "kategori" => "Furnitur",
        "lokasi" => "Badung",
        "jarak" => 1.2,
        "kondisi" => "Baik",
        "status" => "Tersedia",
        "tanggal" => "2026-11-10",
        "foto" => "meja-belajar.png"
    ],
    [
        "id" => 2,
        "nama" => "Tas Sekolah",
        "kategori" => "Busana",
        "lokasi" => "Pancoran",
        "jarak" => 1.2,
        "kondisi" => "Seperti Baru",
        "status" => "Tersedia",
        "tanggal" => "2026-11-10",
        "foto" => "tas-sekolah.png"
    ],
    [
        "id" => 3,
        "nama" => "Buku",
        "kategori" => "Alat Tulis",
        "lokasi" => "Tebet",
        "jarak" => 1.5,
        "kondisi" => "Baik",
        "status" => "Tersedia",
        "tanggal" => "2026-11-09",
        "foto" => "buku.png"
    ],
    [
        "id" => 4,
        "nama" => "Boneka",
        "kategori" => "Lainnya",
        "lokasi" => "Manggarai",
        "jarak" => 2.0,
        "kondisi" => "Baik",
        "status" => "Tersedia",
        "tanggal" => "2026-11-09",
        "foto" => "boneka.png"
    ],
    [
        "id" => 5,
        "nama" => "Aksesoris Elektronik",
        "kategori" => "Elektronik",
        "lokasi" => "Setiabudi",
        "jarak" => 2.4,
        "kondisi" => "Seperti Baru",
        "status" => "Tersedia",
        "tanggal" => "2026-11-08",
        "foto" => "aksesoris-elektronik.png"
    ],
    [
        "id" => 6,
        "nama" => "Makanan Kemasan",
        "kategori" => "Makanan",
        "lokasi" => "Tebet",
        "jarak" => 0.6,
        "kondisi" => "Baru · Tersegel",
        "status" => "Tersedia",
        "tanggal" => "2026-11-08",
        "foto" => "makanan-kemasan.png"
    ],
    [
        "id" => 7,
        "nama" => "Roti Gandum",
        "kategori" => "Makanan",
        "lokasi" => "Jimbaran",
        "jarak" => 0.8,
        "kondisi" => "Baru · Layak konsumsi",
        "status" => "Tersedia",
        "tanggal" => "2026-11-07",
        "foto" => "roti-gandum.png"
    ],
    [
        "id" => 8,
        "nama" => "Peralatan Olahraga",
        "kategori" => "Lainnya",
        "lokasi" => "Pancoran",
        "jarak" => 1.2,
        "kondisi" => "Seperti Baru",
        "status" => "Tersedia",
        "tanggal" => "2026-11-07",
        "foto" => "peralatan-olahraga.png"
    ],
    [
        "id" => 9,
        "nama" => "Cardigan",
        "kategori" => "Busana",
        "lokasi" => "Denpasar",
        "jarak" => 15,
        "kondisi" => "Baru · Belum Dipakai",
        "status" => "Tersedia",
        "tanggal" => "2026-11-06",
        "foto" => "cardigan.png"
    ],
    [
        "id" => 10,
        "nama" => "Gitar",
        "kategori" => "Lainnya",
        "lokasi" => "Denpasar",
        "jarak" => 15,
        "kondisi" => "Baru · Belum Dipakai",
        "status" => "Tersedia",
        "tanggal" => "2026-11-06",
        "foto" => "gitar.png"
    ],
    [
        "id" => 11,
        "nama" => "Beras Putih 1 kg",
        "kategori" => "Makanan",
        "lokasi" => "Jimbaran",
        "jarak" => 1.2,
        "kondisi" => "Baru · Tersegel",
        "status" => "Tersedia",
        "tanggal" => "2026-11-05",
        "foto" => "beras-putih.png"
    ],
    [
        "id" => 12,
        "nama" => "Mi Instan",
        "kategori" => "Makanan",
        "lokasi" => "Kedonganan",
        "jarak" => 1.8,
        "kondisi" => "Baru · Tersegel",
        "status" => "Tersedia",
        "tanggal" => "2026-11-05",
        "foto" => "mi-instan.png"
    ],
    [
        "id" => 13,
        "nama" => "Pensil Warna",
        "kategori" => "Alat Tulis",
        "lokasi" => "Pancoran",
        "jarak" => 1.2,
        "kondisi" => "Seperti Baru",
        "status" => "Tersedia",
        "tanggal" => "2026-11-04",
        "foto" => "pensil-warna.png"
    ],
    [
        "id" => 14,
        "nama" => "Penggaris",
        "kategori" => "Alat Tulis",
        "lokasi" => "Pancoran",
        "jarak" => 1.2,
        "kondisi" => "Seperti Baru",
        "status" => "Tersedia",
        "tanggal" => "2026-11-04",
        "foto" => "penggaris.png"
    ]
];


// ==========================================
// 2. DATA KATEGORI
// ==========================================

$kategori = [
    "Semua",
    "Makanan",
    "Busana",
    "Elektronik",
    "Alat Tulis",
    "Furnitur",
    "Lainnya"
];


// ==========================================
// 3. MENGAMBIL INPUT PENGGUNA
// ==========================================

$kataKunci = trim($_GET["q"] ?? "");

$kategoriAktif = $_GET["kategori"] ?? "Semua";

$lokasiFilter = $_GET["lokasi"] ?? "";

$kondisiFilter = $_GET["kondisi"] ?? "";

$ketersediaanFilter = $_GET["ketersediaan"] ?? "tersedia";

$urutan = $_GET["urutan"] ?? "terbaru";


// Validasi input kategori
if (!in_array($kategoriAktif, $kategori)) {
    $kategoriAktif = "Semua";
}

// Validasi urutan
if (!in_array($urutan, ["terbaru", "terlama", "nama", "terdekat"])) {
    $urutan = "terbaru";
}

// Validasi kondisi
if (!in_array($kondisiFilter, ["", "Baru", "Seperti Baru", "Baik", "Cukup"])) {
    $kondisiFilter = "";
}

// Validasi ketersediaan
if (!in_array($ketersediaanFilter, ["semua", "tersedia"])) {
    $ketersediaanFilter = "tersedia";
}

// Validasi lokasi
$daftarLokasi = array_unique(array_column($barang, "lokasi"));

if ($lokasiFilter !== "" && !in_array($lokasiFilter, $daftarLokasi)) {
    $lokasiFilter = "";
}


// ==========================================
// 4. INISIALISASI FAVORIT
// ==========================================

if (!isset($_SESSION["favorit"])) {
    $_SESSION["favorit"] = [];
}


// ==========================================
// 5. PROSES FAVORIT
// ==========================================

if (isset($_GET["favorit"])) {

    $id = (int) $_GET["favorit"];

    if (in_array($id, $_SESSION["favorit"])) {

        $_SESSION["favorit"] = array_values(
            array_diff($_SESSION["favorit"], [$id])
        );

    } else {

        $_SESSION["favorit"][] = $id;

    }

    $parameter = $_GET;

    unset($parameter["favorit"]);

    header("Location: index.php?" . http_build_query($parameter));
    exit;
}


// ==========================================
// 6. PROSES PENCARIAN DAN FILTER
// ==========================================

$hasil = array_filter($barang, function ($item) use (
    $kataKunci,
    $kategoriAktif,
    $lokasiFilter,
    $kondisiFilter,
    $ketersediaanFilter
) {

    // Pencarian nama barang
    $cocokNama = $kataKunci === "" ||
        stripos($item["nama"], $kataKunci) !== false;

    // Filter kategori
    $cocokKategori = $kategoriAktif === "Semua" ||
        $item["kategori"] === $kategoriAktif;

    // Filter lokasi
    $cocokLokasi = $lokasiFilter === "" ||
        $item["lokasi"] === $lokasiFilter;

    // Filter kondisi
    $cocokKondisi = true;

    if ($kondisiFilter !== "") {

        if ($kondisiFilter === "Baru") {

            $cocokKondisi = strpos($item["kondisi"], "Baru") === 0;

        } elseif ($kondisiFilter === "Seperti Baru") {

            $cocokKondisi = $item["kondisi"] === "Seperti Baru";

        } else {

            $cocokKondisi = $item["kondisi"] === $kondisiFilter;

        }
    }

    // Filter ketersediaan
    $cocokKetersediaan = $ketersediaanFilter === "semua" ||
        $item["status"] === "Tersedia";

    return
        $cocokNama &&
        $cocokKategori &&
        $cocokLokasi &&
        $cocokKondisi &&
        $cocokKetersediaan;
});


// ==========================================
// 7. PROSES PENGURUTAN
// ==========================================

usort($hasil, function ($a, $b) use ($urutan) {

    if ($urutan === "terlama") {
        return strcmp($a["tanggal"], $b["tanggal"]);
    }

    if ($urutan === "nama") {
        return strcasecmp($a["nama"], $b["nama"]);
    }

    if ($urutan === "terdekat") {
        return $a["jarak"] <=> $b["jarak"];
    }

    return strcmp($b["tanggal"], $a["tanggal"]);
});


// ==========================================
// 8. FUNGSI PENGAMANAN OUTPUT
// ==========================================

function e($teks) {
    return htmlspecialchars(
        (string) $teks,
        ENT_QUOTES,
        "UTF-8"
    );
}


// ==========================================
// 9. FUNGSI MEMPERTAHANKAN PARAMETER URL
// ==========================================

function urlDengan($perubahan = []) {

    $parameter = $_GET;

    unset($parameter["favorit"]);

    foreach ($perubahan as $key => $value) {

        if ($value === null || $value === "") {
            unset($parameter[$key]);
        } else {
            $parameter[$key] = $value;
        }
    }

    return "index.php?" . http_build_query($parameter);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Jelajahi - ShareSpace</title>

    <link rel="stylesheet" href="./style.css?v=2">

</head>

<body>

<div class="app-shell">

    <main class="content">

        <!-- HEADER -->
        <header class="page-header">

            <h1>Jelajahi</h1>

            <p>
                Temukan barang berguna dari sesama.
            </p>

        </header>


        <!-- SEARCH BAR -->
        <form class="search-form"
              method="GET"
              action="index.php">

            <input type="hidden"
                   name="kategori"
                   value="<?= e($kategoriAktif) ?>">

            <input type="hidden"
                   name="urutan"
                   value="<?= e($urutan) ?>">

            <input type="hidden"
                   name="lokasi"
                   value="<?= e($lokasiFilter) ?>">

            <input type="hidden"
                   name="kondisi"
                   value="<?= e($kondisiFilter) ?>">

            <input type="hidden"
                   name="ketersediaan"
                   value="<?= e($ketersediaanFilter) ?>">

            <span class="search-icon">⌕</span>

            <input
                type="search"
                name="q"
                value="<?= e($kataKunci) ?>"
                placeholder="Cari barang yang kamu butuhkan..."
            >

        </form>


        <!-- FILTER KATEGORI -->
        <nav class="category-list">

            <?php foreach ($kategori as $namaKategori): ?>

                <a
                    href="<?= e(urlDengan([
                        "kategori" => $namaKategori === "Semua"
                            ? "Semua"
                            : $namaKategori
                    ])) ?>"
                    class="category-chip
                    <?= $kategoriAktif === $namaKategori
                        ? "active"
                        : "" ?>"
                >

                    <?= e($namaKategori) ?>

                </a>

            <?php endforeach; ?>

        </nav>


        <!-- LOKASI DAN FILTER -->
        <section class="location-row">

            <div class="location-label">

                <span class="pin">⌖</span>

                Jimbaran, Bali

                <span class="chevron">⌄</span>

            </div>


            <button
                type="button"
                class="filter-trigger"
                id="openFilter"
            >

                <span class="filter-icon">☷</span>

                Filter

                <span class="filter-arrow">⌄</span>

            </button>

        </section>


        <!-- JUMLAH DAN PENGURUTAN -->
        <section class="results-heading">

            <strong>
                <?= count($hasil) ?>
                barang di sekitar
            </strong>


            <form method="GET"
                  action="index.php"
                  class="sort-form">

                <input type="hidden"
                       name="q"
                       value="<?= e($kataKunci) ?>">

                <input type="hidden"
                       name="kategori"
                       value="<?= e($kategoriAktif) ?>">

                <input type="hidden"
                       name="lokasi"
                       value="<?= e($lokasiFilter) ?>">

                <input type="hidden"
                       name="kondisi"
                       value="<?= e($kondisiFilter) ?>">

                <input type="hidden"
                       name="ketersediaan"
                       value="<?= e($ketersediaanFilter) ?>">


                <select name="urutan"
                        onchange="this.form.submit()">

                    <option value="terbaru"
                        <?= $urutan === "terbaru" ? "selected" : "" ?>>
                        Terbaru
                    </option>

                    <option value="terlama"
                        <?= $urutan === "terlama" ? "selected" : "" ?>>
                        Terlama
                    </option>

                    <option value="terdekat"
                        <?= $urutan === "terdekat" ? "selected" : "" ?>>
                        Terdekat
                    </option>

                    <option value="nama"
                        <?= $urutan === "nama" ? "selected" : "" ?>>
                        Nama A-Z
                    </option>

                </select>

            </form>

        </section>


        <!-- DAFTAR BARANG -->
        <?php if (count($hasil) > 0): ?>

            <section class="product-grid">

                <?php foreach ($hasil as $item): ?>

                    <?php
                    $disukai = in_array(
                        $item["id"],
                        $_SESSION["favorit"]
                    );
                    ?>

                    <article class="product-card">

                        <div class="product-photo">

                            <img
                                src="assets/<?= e($item["foto"]) ?>"
                                alt="<?= e($item["nama"]) ?>"
                            >

                            <a
                                href="<?= e(urlDengan([
                                    "favorit" => $item["id"]
                                ])) ?>"
                                class="favorite-button
                                <?= $disukai ? "liked" : "" ?>"
                                aria-label="Favorit <?= e($item["nama"]) ?>"
                            >

                                <?= $disukai ? "♥" : "♡" ?>

                            </a>

                        </div>


                        <div class="product-info">

                            <h2>
                                <?= e($item["nama"]) ?>
                            </h2>

                            <p class="product-category">
                                <?= e($item["kategori"]) ?>
                            </p>

                            <p class="product-location">

                                <span>⌖</span>

                                <?= e($item["lokasi"]) ?>

                                ·

                                <?= e(number_format(
                                    $item["jarak"],
                                    1,
                                    ",",
                                    "."
                                )) ?>

                                km

                            </p>

                            <p class="product-condition">
                                <?= e($item["kondisi"]) ?>
                            </p>

                            <span class="availability">

                                <i></i>

                                <?= e($item["status"]) ?>

                            </span>

                        </div>

                    </article>

                <?php endforeach; ?>

            </section>

        <?php else: ?>

            <!-- JIKA BARANG TIDAK DITEMUKAN -->
            <div class="empty-state">

                <div class="empty-icon">⌕</div>

                <h2>Barang tidak ditemukan</h2>

                <p>
                    Coba gunakan kata kunci lain
                    atau pilih kategori berbeda.
                </p>

                <a href="index.php" class="reset-button">
                    Tampilkan semua barang
                </a>

            </div>

        <?php endif; ?>

    </main>


    <!-- BOTTOM NAVIGATION -->
    <nav class="bottom-nav">

        <a href="../beranda/index.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <path d="M3 10.5L12 3l9 7.5V21h-6v-6H9v6H3z"/>
            </svg>

            <span>Beranda</span>

        </a>


        <a href="../jelajahi/index.php" class="nav-item active">

            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path d="M15.5 8.5l-2.5 7-4.5 1.5 2.5-7z"/>
            </svg>

            <span>Jelajahi</span>

        </a>


        <a href="../bagikan/index.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 8v8M8 12h8"/>
            </svg>

            <span>Bagikan</span>

        </a>


        <a href="../aktivitas/index.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <path d="M3 5h18v14H3z"/>
                <path d="M7 19v2h10v-2"/>
            </svg>

            <span>Aktivitas</span>

        </a>


        <a href="../profil/index.php" class="nav-item">

            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="7" r="4"/>
                <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
            </svg>

            <span>Profil</span>

        </a>

    </nav>

</div>


<!-- ==========================================
     FILTER BOTTOM SHEET
========================================== -->

<!-- OVERLAY -->
<div
    class="filter-overlay"
    id="filterOverlay"
></div>


<!-- FILTER PANEL -->
<div
    class="filter-sheet"
    id="filterSheet"
    role="dialog"
    aria-modal="true"
    aria-labelledby="filterTitle"
>

    <div class="sheet-handle"></div>


    <!-- HEADER -->
    <div class="filter-header">

        <h3 id="filterTitle">Filter barang</h3>

        <button
            type="button"
            class="close-filter"
            id="closeFilter"
            aria-label="Tutup filter"
        >
            &times;
        </button>

    </div>


    <!-- FORM FILTER -->
    <form
        action="index.php"
        method="GET"
        id="filterForm"
    >

        <!-- PERTAHANKAN PENCARIAN DAN KATEGORI -->
        <input
            type="hidden"
            name="q"
            value="<?= e($kataKunci) ?>"
        >

        <input
            type="hidden"
            name="kategori"
            value="<?= e($kategoriAktif) ?>"
        >


        <!-- LOKASI -->
        <div class="filter-group">

            <label for="lokasiFilter">Lokasi</label>

            <select
                name="lokasi"
                id="lokasiFilter"
            >

                <option value=""
                    <?= $lokasiFilter === "" ? "selected" : "" ?>>
                    Semua lokasi
                </option>

                <?php foreach ($daftarLokasi as $lokasi): ?>

                    <option
                        value="<?= e($lokasi) ?>"
                        <?= $lokasiFilter === $lokasi ? "selected" : "" ?>
                    >
                        <?= e($lokasi) ?>, Bali
                    </option>

                <?php endforeach; ?>

            </select>

        </div>


        <!-- KONDISI -->
        <div class="filter-group">

            <label>Kondisi</label>

            <div class="filter-options">

                <?php
                $pilihanKondisi = [
                    "Baru",
                    "Seperti Baru",
                    "Baik",
                    "Cukup"
                ];
                ?>

                <?php foreach ($pilihanKondisi as $kondisi): ?>

                    <label class="filter-option">

                        <input
                            type="radio"
                            name="kondisi"
                            value="<?= e($kondisi) ?>"
                            <?= $kondisiFilter === $kondisi ? "checked" : "" ?>
                        >

                        <span><?= e($kondisi) ?></span>

                    </label>

                <?php endforeach; ?>

            </div>

        </div>


        <!-- KETERSEDIAAN -->
        <div class="filter-group">

            <label>Ketersediaan</label>

            <div class="filter-options">

                <label class="filter-option">

                    <input
                        type="radio"
                        name="ketersediaan"
                        value="semua"
                        <?= $ketersediaanFilter === "semua" ? "checked" : "" ?>
                    >

                    <span>Semua barang</span>

                </label>


                <label class="filter-option">

                    <input
                        type="radio"
                        name="ketersediaan"
                        value="tersedia"
                        <?= $ketersediaanFilter === "tersedia" ? "checked" : "" ?>
                    >

                    <span>Hanya tersedia</span>

                </label>

            </div>

        </div>


        <!-- URUTKAN -->
        <div class="filter-group">

            <label>Urutkan</label>

            <div class="filter-options">

                <label class="filter-option">

                    <input
                        type="radio"
                        name="urutan"
                        value="terbaru"
                        <?= $urutan === "terbaru" ? "checked" : "" ?>
                    >

                    <span>Terbaru</span>

                </label>


                <label class="filter-option">

                    <input
                        type="radio"
                        name="urutan"
                        value="terdekat"
                        <?= $urutan === "terdekat" ? "checked" : "" ?>
                    >

                    <span>Terdekat</span>

                </label>

            </div>

        </div>


        <!-- TOMBOL FILTER -->
        <div class="filter-actions">

            <button
                type="submit"
                class="apply-filter"
            >
                Terapkan filter
            </button>


            <a
                href="<?= e(urlDengan([
                    "lokasi" => null,
                    "kondisi" => null,
                    "ketersediaan" => null,
                    "urutan" => "terbaru"
                ])) ?>"
                class="reset-filter"
            >
                Hapus filter
            </a>

        </div>

    </form>

</div>


<!-- ==========================================
     JAVASCRIPT FILTER
========================================== -->

<script>

const openFilter = document.getElementById('openFilter');

const closeFilter = document.getElementById('closeFilter');

const filterOverlay = document.getElementById('filterOverlay');

const filterSheet = document.getElementById('filterSheet');


function showFilter() {

    filterOverlay.classList.add('active');

    filterSheet.classList.add('active');

    document.body.classList.add('filter-open');

}


function hideFilter() {

    filterOverlay.classList.remove('active');

    filterSheet.classList.remove('active');

    document.body.classList.remove('filter-open');

}


openFilter.addEventListener('click', showFilter);

closeFilter.addEventListener('click', hideFilter);

filterOverlay.addEventListener('click', hideFilter);


document.addEventListener('keydown', function(event) {

    if (event.key === 'Escape') {
        hideFilter();
    }

});

</script>

</body>
</html>

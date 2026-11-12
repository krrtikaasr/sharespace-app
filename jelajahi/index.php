
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// DATA BARANG DONASI (DATA SEMENTARA)
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

// MENGAMBIL INPUT PENGGUNA
$kataKunci = trim($_GET["q"] ?? "");
$kategoriAktif = $_GET["kategori"] ?? "Semua";
$urutan = $_GET["urutan"] ?? "terbaru";

$kategori = [
    "Semua",
    "Makanan",
    "Busana",
    "Elektronik",
    "Alat Tulis",
    "Furnitur",
    "Lainnya"
];

// INISIALISASI FAVORIT
if (!isset($_SESSION["favorit"])) {
    $_SESSION["favorit"] = [];
}

// PROSES FAVORIT
if (isset($_GET["favorit"])) {

    $id = (int) $_GET["favorit"];

    if (in_array($id, $_SESSION["favorit"])) {
        $_SESSION["favorit"] = array_diff(
            $_SESSION["favorit"],
            [$id]
        );
    } else {
        $_SESSION["favorit"][] = $id;
    }

    $parameter = $_GET;
    unset($parameter["favorit"]);

    header("Location: index.php?" . http_build_query($parameter));
    exit;
}

// PROSES PENCARIAN DAN FILTER
$hasil = array_filter($barang, function ($item) use (
    $kataKunci,
    $kategoriAktif
) {

    $cocokNama = $kataKunci === "" ||
        stripos($item["nama"], $kataKunci) !== false;

    $cocokKategori = $kategoriAktif === "Semua" ||
        $item["kategori"] === $kategoriAktif;

    return $cocokNama && $cocokKategori;
});

// PROSES PENGURUTAN
usort($hasil, function ($a, $b) use ($urutan) {

    if ($urutan === "terlama") {
        return strcmp($a["tanggal"], $b["tanggal"]);
    }

    if ($urutan === "nama") {
        return strcasecmp($a["nama"], $b["nama"]);
    }

    return strcmp($b["tanggal"], $a["tanggal"]);
});

// FUNGSI MENGAMANKAN OUTPUT
function e($teks) {
    return htmlspecialchars(
        (string) $teks,
        ENT_QUOTES,
        "UTF-8"
    );
}

// FUNGSI MEMPERTAHANKAN FILTER SAAT PINDAH KATEGORI
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

    <link rel="stylesheet" href="./style.css?v=1">
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
                            ? null
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

            <form method="GET"
                  action="index.php"
                  class="filter-form">

                <input type="hidden"
                       name="q"
                       value="<?= e($kataKunci) ?>">

                <label class="filter-select-wrap">

                    <span>☷</span>

                    <select name="kategori"
                            onchange="this.form.submit()">

                        <option value="Semua">Filter</option>

                        <?php foreach ($kategori as $namaKategori): ?>

                            <?php if ($namaKategori === "Semua") continue; ?>

                            <option
                                value="<?= e($namaKategori) ?>"
                                <?= $kategoriAktif === $namaKategori
                                    ? "selected"
                                    : "" ?>
                            >
                                <?= e($namaKategori) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </label>

            </form>

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
        <a href="../index.php" class="nav-item">
            <svg viewBox="0 0 24 24">
                <path d="M3 10.5L12 3l9 7.5V21h-6v-6H9v6H3z"/>
            </svg>
            <span>Beranda</span>
        </a>

        <a href="index.php" class="nav-item active">
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

</body>
</html>

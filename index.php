<?php
$kategori = [
  ['Makanan', '🍴'], ['Pakaian', '👕'], ['Elektronik', '🎧'],
  ['Alat Tulis', '✏️'], ['Furnitur', '🛋️'], ['Lainnya', '📦'],
];
$dampak = [
  '100 barang telah dibagikan kembali',
  '100 donasi dalam seharian',
];
?>
<?php include 'includes/head.php'; ?>
<style>
:root { --biru:#2563eb; --teks:#0f172a; --bg:#f8fafc; --garis:#e2e8f0; }
* { box-sizing:border-box; margin:0; }
html { scroll-behavior:smooth; scroll-padding-top:72px; }
body { font-family:'Inter',sans-serif; color:var(--teks); background:var(--bg); line-height:1.6; }
a { color:inherit; text-decoration:none; }
.container { width:min(1100px,100% - 40px); margin-inline:auto; }
.biru { color:var(--biru); }
.center { text-align:center; }
section { padding-block:48px; }

/* tombol */
.btn { display:inline-block; padding:10px 22px; border-radius:6px; font-size:14px; font-weight:600; text-align:center; }
.btn--primary { background:var(--biru); color:#fff; }
.btn--outline { border:1px solid var(--biru); color:var(--biru); background:#fff; }
.btn:focus-visible, a:focus-visible, input:focus-visible { outline:2px solid var(--biru); outline-offset:2px; }

/* navbar */
.navbar { position:sticky; top:0; z-index:10; background:#fff; border-bottom:1px solid var(--garis); }
.navbar__inner { display:flex; align-items:center; gap:24px; height:64px; }
.brand { display:flex; align-items:center; gap:8px; font-weight:700; margin-right:auto; }
.brand__logo { width:24px; height:24px; background:var(--biru); border-radius:6px; }
.navbar__links { display:flex; gap:24px; font-size:13px; font-weight:500; }
.navbar__links a:hover, .navbar__links .aktif { color:var(--biru); }
.navbar__toggle { display:none; background:none; border:0; font-size:22px; cursor:pointer; }

/* hero */
.hero { display:grid; grid-template-columns:1fr 1fr; gap:40px; align-items:center; padding-block:40px; }
.hero h1 { font-size:clamp(32px,4.5vw,48px); line-height:1.15; font-weight:700; }
.hero__sub { margin:20px 0 14px; font-size:13px; font-weight:600; }
.search input { width:100%; max-width:340px; padding:12px 16px; border:1px solid var(--garis); border-radius:8px; background:#fff; font:inherit; font-size:13px; box-shadow:0 2px 8px rgba(15,23,42,.06); }
.hero__img { width:100%; aspect-ratio:4/5; max-height:460px; object-fit:cover; border-radius:20px; background:var(--garis); }

/* judul section */
.judul { font-size:clamp(24px,3.5vw,34px); font-weight:700; line-height:1.25; margin-bottom:28px; }

/* kategori */
.kategori__grid { display:grid; grid-template-columns:repeat(3,1fr); gap:28px; max-width:760px; margin-inline:auto; }
.kartu { display:flex; flex-direction:column; justify-content:flex-end; gap:6px; min-height:110px; padding:16px; background:#fff; border-radius:12px; font-size:12px; box-shadow:0 2px 10px rgba(15,23,42,.06); transition:box-shadow .2s; }
.kartu:hover { box-shadow:0 4px 16px rgba(37,99,235,.18); }
.kartu__ikon { font-size:18px; }

/* tentang */
.tentang { max-width:800px; }
.tentang p { margin-bottom:14px; font-size:15px; }

/* dampak */
.dampak__grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
.dampak__kotak { display:flex; align-items:center; min-height:300px; padding:28px; background:var(--biru); color:#fff; border-radius:12px; font-size:clamp(22px,3vw,32px); font-weight:800; line-height:1.2; text-transform:uppercase; }

/* cta */
.cta { padding-block:72px 96px; }
.cta__tombol { display:flex; flex-direction:column; gap:12px; width:min(280px,100%); margin:40px auto 0; }

/* mobile */
@media (max-width:767px) {
  .navbar__toggle { display:block; }
  .navbar__links { display:none; position:absolute; top:64px; left:0; right:0; flex-direction:column; gap:0; background:#fff; border-bottom:1px solid var(--garis); }
  .navbar__links.buka { display:flex; }
  .navbar__links a { padding:14px 20px; }
  .hero, .dampak__grid { grid-template-columns:1fr; }
  .hero__img { order:-1; aspect-ratio:4/3; }
  .kategori__grid { grid-template-columns:repeat(2,1fr); gap:16px; }
  .dampak__kotak { min-height:180px; }
}
@media (prefers-reduced-motion:reduce) { html { scroll-behavior:auto; } }
</style>
</head>
<body>
  <?php include 'includes/landing-navbar.php'; ?>

  <!-- BERANDA / HERO -->
  <section id="beranda" class="container hero">
    <div class="hero__text">
      <h1>Dapat barang, <span class="biru">bersih limbah</span></h1>
      <p class="hero__sub">Cari kebutuhanmu di ShareSpace!</p>
      <form class="search" action="cari.php" method="get">
        <input type="search" name="q" placeholder="Search" aria-label="Cari barang">
      </form>
    </div>
    <img class="hero__img" src="assets/img/hero.jpg" alt="Dua orang saling berbagi kotak berisi bahan makanan">
  </section>

  <!-- KATEGORI -->
  <section class="container kategori">
    <h2 class="judul center"><span class="biru">Bagikan</span> dan <span class="biru">Gunakan</span> Kembali!</h2>
    <div class="kategori__grid">
      <?php foreach ($kategori as [$nama, $ikon]): ?>
        <a class="kartu" href="cari.php?kategori=<?= urlencode($nama) ?>">
          <span class="kartu__ikon"><?= $ikon ?></span>
          <span><?= $nama ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- TENTANG -->
  <section id="tentang" class="container tentang">
    <h2 class="judul"><span class="biru">Share</span>Space</h2>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed purus urna, pretium quis mattis nec, sollicitudin vitae lacus. Etiam volutpat ullamcorper velit, non posuere metus.</p>
    <p>Fusce ut mollis ex. Sed mattis id metus iaculis fringilla. Vestibulum enim tortor, efficitur nec laoreet non, pharetra eu mi. Duis nec mauris nibh.</p>
    <p>Vestibulum ipsum odio, malesuada id blandit at, blandit vel ex. Sed aliquam augue et mattis varius. Fusce laoreet diam condimentum, consectetur augue ut.</p>
  </section>

  <!-- DONASI / DAMPAK -->
  <section id="donasi" class="container dampak">
    <h2 class="judul"><span class="biru">DAMPAK</span> BAGI SEKITAR</h2>
    <div class="dampak__grid">
      <?php foreach ($dampak as $teks): ?>
        <div class="dampak__kotak"><?= htmlspecialchars($teks) ?></div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- DAFTAR / CTA -->
  <section id="daftar" class="container cta">
    <h2 class="judul center">MULAI GUNAKAN <span class="biru">SHARESPACE</span> DAN RASAKAN <span class="biru">MANFAATNYA</span></h2>
    <div class="cta__tombol">
      <a href="masuk.php" class="btn btn--primary">Masuk</a>
      <a href="daftar.php" class="btn btn--outline">Daftar</a>
    </div>
  </section>
</body>
</html>
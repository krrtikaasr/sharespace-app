<?php
$kategori = [
  ['Makanan',    '<path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2M7 2v20M21 15V2a5 5 0 00-5 5v6c0 1.1.9 2 2 2h3v7"/>'],
  ['Pakaian',    '<path d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.34 2.23l.58 3.47a1 1 0 00.99.84H6v10c0 1.1.9 2 2 2h8a2 2 0 002-2V10h2.15a1 1 0 00.99-.84l.58-3.47a2 2 0 00-1.34-2.23z"/>'],
  ['Elektronik', '<path d="M3 18v-6a9 9 0 0118 0v6"/><path d="M21 19a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3zM3 19a2 2 0 002 2h1a2 2 0 002-2v-3a2 2 0 00-2-2H3z"/>'],
  ['Alat Tulis', '<path d="M17 3a2.83 2.83 0 114 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>'],
  ['Furnitur',   '<path d="M20 9V6a2 2 0 00-2-2H6a2 2 0 00-2 2v3"/><path d="M2 11v5a2 2 0 002 2h16a2 2 0 002-2v-5a2 2 0 00-4 0v2H6v-2a2 2 0 00-4 0z"/><path d="M4 18v2M20 18v2"/>'],
  ['Lainnya',    '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>'],
];

$dampak = [
  ['angka' => '100', 'label' => 'Barang telah dibagikan kembali'],
  ['angka' => '100', 'label' => 'Donasi dalam sehari'],
];

$langkah = [
  ['1', 'Unggah barang yang ingin kamu donasikan'],
  ['2', 'Ajukan permintaan untuk barang yang kamu butuhkan'],
  ['3', 'Jemput barang dan beri kesempatan kedua'],
];
?>
<?php include 'includes/head.php'; ?>
<style>
:root { --biru:#2563eb; --biru-muda:#eff6ff; --teks:#0f172a; --muted:#475569; --bg:#f8fafc; --garis:#e2e8f0; }
* { box-sizing:border-box; margin:0; }
html { scroll-behavior:smooth; scroll-padding-top:72px; }
body { font-family:'Inter',sans-serif; color:var(--teks); background:var(--bg); line-height:1.6; }
a { color:inherit; text-decoration:none; }
.container { width:min(1100px,100% - 40px); margin-inline:auto; }
.biru { color:var(--biru); }
.center { text-align:center; }
section { padding-block:64px; }

/* tombol */
.btn { display:inline-flex; align-items:center; justify-content:center; padding:11px 24px; border-radius:8px; font-size:14px; font-weight:600; text-align:center; cursor:pointer; border:1px solid transparent; transition:background .2s, border-color .2s, color .2s; }
.btn--primary { background:var(--biru); color:#fff; }
.btn--primary:hover { background:#1d4ed8; }
.btn--outline { border-color:var(--biru); color:var(--biru); background:#fff; }
.btn--outline:hover { background:var(--biru-muda); }
.btn:focus-visible, a:focus-visible { outline:2px solid var(--biru); outline-offset:2px; }

/* navbar */
.navbar { position:sticky; top:0; z-index:20; background:#fff; border-bottom:1px solid var(--garis); }
.navbar__inner { display:flex; align-items:center; gap:24px; height:68px; }
.brand { display:flex; align-items:center; gap:10px; font-weight:700; font-size:16px; margin-right:auto; }
.brand__logo { width:32px; height:32px; background:var(--biru); color:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center; }
.navbar__links { display:flex; align-items:center; gap:28px; font-size:14px; font-weight:500; color:var(--muted); }
.navbar__links a { position:relative; padding-block:6px; transition:color .2s; }
.navbar__links a:hover, .navbar__links a.aktif { color:var(--biru); font-weight:600; }
.navbar__links a.aktif::after { content:''; position:absolute; left:0; right:0; bottom:-2px; height:2px; background:var(--biru); border-radius:2px; }
.navbar__aksi { display:flex; align-items:center; gap:10px; }
.navbar__aksi .btn { padding:8px 18px; font-size:13px; }
.navbar__toggle { display:none; background:none; border:0; font-size:22px; cursor:pointer; color:var(--teks); }

/* judul section */
.judul { font-size:clamp(26px,3.6vw,36px); font-weight:800; line-height:1.2; margin-bottom:32px; letter-spacing:-0.02em; }

/* 1. BERANDA - HERO */
.hero { display:grid; grid-template-columns:1.05fr 0.95fr; gap:48px; align-items:center; padding-block:48px 64px; }
.hero h1 { font-size:clamp(34px,4.6vw,50px); line-height:1.14; font-weight:800; letter-spacing:-0.02em; }
.hero__sub { margin:24px 0 18px; font-size:15px; font-weight:500; color:var(--muted); }
.hero__tombol { display:flex; flex-wrap:wrap; gap:12px; }
.hero__tombol .btn { min-width:130px; padding:12px 26px; }
.hero__img { width:100%; aspect-ratio:4/4.3; max-height:460px; object-fit:cover; border-radius:20px; background:var(--garis); }

/* 2. BERANDA - KATEGORI */
.kategori__grid { display:grid; grid-template-columns:repeat(4,1fr); gap:24px; }
.kartu { display:flex; flex-direction:column; justify-content:space-between; gap:24px; min-height:148px; padding:22px; background:#fff; border:1px solid var(--garis); border-radius:16px; transition:box-shadow .2s, border-color .2s, transform .2s; }
.kartu:hover { border-color:#bfdbfe; box-shadow:0 8px 24px rgba(37,99,235,.10); transform:translateY(-2px); }
.kartu__ikon { width:44px; height:44px; border-radius:12px; background:var(--biru-muda); color:var(--biru); display:flex; align-items:center; justify-content:center; }
.kartu__nama { font-size:15px; font-weight:700; color:var(--teks); }

/* 3. BERANDA - DAMPAK */
.dampak__grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
.dampak__kotak { display:flex; flex-direction:column; justify-content:center; min-height:240px; padding:36px 40px; background:var(--biru); color:#fff; border-radius:18px; }
.dampak__angka { font-size:clamp(44px,5vw,60px); font-weight:800; line-height:1; margin-bottom:14px; }
.dampak__label { font-size:clamp(18px,2.2vw,24px); font-weight:700; line-height:1.3; }

/* 4. TENTANG (1 HALAMAN) */
.tentang__grid { display:grid; grid-template-columns:1fr 1fr; gap:48px; align-items:center; }
.tentang__teks p { margin-bottom:16px; font-size:15px; color:var(--muted); line-height:1.75; }
.langkah__list { display:flex; flex-direction:column; gap:16px; }
.langkah__item { display:flex; align-items:center; gap:18px; padding:20px 22px; background:#fff; border:1px solid var(--garis); border-radius:14px; box-shadow:0 2px 8px rgba(15,23,42,.03); }
.langkah__nomor { width:36px; height:36px; border-radius:10px; background:var(--biru-muda); color:var(--biru); font-size:15px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.langkah__judul { font-size:15px; font-weight:600; color:var(--teks); line-height:1.4; }

/* 5. DONASI / MULAI (1 HALAMAN) */
.cta { padding-block:88px 110px; }
.cta .judul { max-width:680px; margin-inline:auto; }
.cta__tombol { display:flex; flex-direction:column; gap:12px; width:min(280px,100%); margin:36px auto 0; }

/* mobile */
@media (max-width:860px) {
  .kategori__grid { grid-template-columns:repeat(2,1fr); gap:16px; }
  .tentang__grid { grid-template-columns:1fr; gap:32px; }
}
@media (max-width:767px) {
  .navbar__toggle { display:block; }
  .navbar__links { display:none; position:absolute; top:68px; left:0; right:0; flex-direction:column; align-items:stretch; gap:0; background:#fff; border-bottom:1px solid var(--garis); padding-bottom:8px; }
  .navbar__links.buka { display:flex; }
  .navbar__links a { padding:12px 20px; }
  .navbar__links a.aktif::after { display:none; }
  .hero, .dampak__grid { grid-template-columns:1fr; }
  .hero__img { order:-1; aspect-ratio:4/3; }
  .dampak__kotak { min-height:180px; padding:28px; }
}
@media (prefers-reduced-motion:reduce) { html { scroll-behavior:auto; } }
</style>
</head>
<body>
  <!-- NAVBAR -->
  <header class="navbar">
    <div class="container navbar__inner">
      <a href="#beranda" class="brand">
        <span class="brand__logo">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M11 14h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 16"/><path d="m7 20 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9"/><path d="m2 15 6 6"/><path d="M19.5 8.5c.7-.7 1.5-1.6 1.5-2.7A2.73 2.73 0 0 0 16 4a2.78 2.78 0 0 0-5 1.8c0 1.2.8 2 1.5 2.8L16 12Z"/>
          </svg>
        </span>
        ShareSpace
      </a>

      <nav class="navbar__links" id="navMenu" aria-label="Navigasi utama">
        <a href="#beranda" class="nav-item aktif" data-target="beranda">Beranda</a>
        <a href="#tentang" class="nav-item" data-target="tentang">Tentang</a>
        <a href="#donasi"  class="nav-item" data-target="donasi">Donasi</a>
      </nav>

      <div class="navbar__aksi">
        <a href="daftar.php" class="btn btn--outline">Daftar</a>
        <a href="masuk.php" class="btn btn--primary">Masuk</a>
        <button type="button" class="navbar__toggle" aria-label="Buka menu" onclick="document.getElementById('navMenu').classList.toggle('buka')">☰</button>
      </div>
    </div>
  </header>

  <!-- ==================== BAGIAN BERANDA (3 HALAMAN) ==================== -->
  <div id="beranda" class="grup-halaman" data-nav="beranda">
    <!-- Halaman 1: Hero -->
    <section class="container hero">
      <div class="hero__text">
        <h1>Dapat barang, <span class="biru">bersih limbah</span></h1>
        <p class="hero__sub">Cari kebutuhanmu di ShareSpace!</p>
        <div class="hero__tombol">
          <a href="daftar.php" class="btn btn--primary">Mulai</a>
          <a href="masuk.php" class="btn btn--outline">Masuk</a>
        </div>
      </div>
      <img class="hero__img" src="assets/img/hero.jpg" alt="Dua orang saling berbagi kotak berisi bahan makanan">
    </section>

    <!-- Halaman 2: Kategori -->
    <section class="container kategori">
      <h2 class="judul center"><span class="biru">Bagikan</span> dan <span class="biru">Gunakan</span> Kembali!</h2>
      <div class="kategori__grid">
        <?php foreach ($kategori as [$nama, $svg]): ?>
          <a class="kartu" href="cari.php?kategori=<?= urlencode($nama) ?>">
            <span class="kartu__ikon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $svg ?></svg>
            </span>
            <span class="kartu__nama"><?= htmlspecialchars($nama) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Halaman 3: Dampak Bagi Sekitar -->
    <section class="container dampak">
      <h2 class="judul"><span class="biru">DAMPAK</span> BAGI SEKITAR</h2>
      <div class="dampak__grid">
        <?php foreach ($dampak as $d): ?>
          <div class="dampak__kotak">
            <span class="dampak__angka"><?= htmlspecialchars($d['angka']) ?></span>
            <span class="dampak__label"><?= htmlspecialchars($d['label']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </div>

  <!-- ==================== BAGIAN TENTANG (1 HALAMAN) ==================== -->
  <section id="tentang" class="container tentang grup-halaman" data-nav="tentang">
    <div class="tentang__grid">
      <div class="tentang__teks">
        <h2 class="judul"><span class="biru">Share</span>Space</h2>
        <p>ShareSpace adalah platform donasi barang yang mempertemukan orang yang ingin berbagi dengan mereka yang membutuhkan.</p>
        <p>Barang layak pakai yang tidak lagi terpakai bisa diunggah, diminta, lalu dijemput orang lain, sehingga tidak berakhir menjadi limbah.</p>
        <p>Dari makanan, pakaian, elektronik, alat tulis, hingga furnitur, setiap barang punya kesempatan kedua.</p>
      </div>

      <div class="langkah__list">
        <?php foreach ($langkah as [$no, $teks]): ?>
          <div class="langkah__item">
            <span class="langkah__nomor"><?= htmlspecialchars($no) ?></span>
            <p class="langkah__judul"><?= htmlspecialchars($teks) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ==================== BAGIAN DONASI / MULAI (1 HALAMAN) ==================== -->
  <section id="donasi" class="container cta grup-halaman" data-nav="donasi">
    <h2 class="judul center">MULAI GUNAKAN <span class="biru">SHARESPACE</span> DAN RASAKAN <span class="biru">MANFAATNYA</span></h2>
    <div class="cta__tombol">
      <a href="masuk.php" class="btn btn--primary">Masuk</a>
      <a href="daftar.php" class="btn btn--outline">Daftar</a>
    </div>
  </section>

  <script>
    const navItems = document.querySelectorAll('.nav-item');
    const grupHalaman = document.querySelectorAll('.grup-halaman');
    const navMenu = document.getElementById('navMenu');

    function setAktif(id) {
      navItems.forEach(a => {
        a.classList.toggle('aktif', a.dataset.target === id);
      });
    }

    navItems.forEach(a => {
      a.addEventListener('click', () => {
        setAktif(a.dataset.target);
        navMenu.classList.remove('buka');
      });
    });

    window.addEventListener('scroll', () => {
      const posisiScroll = window.scrollY + 140;
      grupHalaman.forEach(sec => {
        const atas = sec.offsetTop;
        const bawah = atas + sec.offsetHeight;
        if (posisiScroll >= atas && posisiScroll < bawah) {
          setAktif(sec.dataset.nav);
        }
      });
    });
  </script>
</body>
</html>
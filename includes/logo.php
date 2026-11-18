<?php
// Logo ShareSpace: satu sumber untuk semua halaman (sama seperti di index.php).
// logo_brand()         -> logo biru, untuk latar terang (form daftar/masuk)
// logo_brand('putih')  -> logo putih, untuk latar biru (panel kiri / auth-kiri.php)
function logo_brand(string $varian = 'biru'): string {
  $svg = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 14h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 16"/><path d="m7 20 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9"/><path d="m2 15 6 6"/><path d="M19.5 8.5c.7-.7 1.5-1.6 1.5-2.7A2.73 2.73 0 0 0 16 4a2.78 2.78 0 0 0-5 1.8c0 1.2.8 2 1.5 2.8L16 12Z"/></svg>';
  $v = $varian === 'putih' ? 'putih' : 'biru';
  return '<a href="index.php" class="brand brand--' . $v . '"><span class="brand__logo">' . $svg . '</span><span>ShareSpace</span></a>';
}

function logo_style(): void { ?>
<style>
.brand { display:flex; align-items:center; gap:10px; font-weight:700; font-size:16px; color:#0f172a; text-decoration:none; }
.brand .brand__logo { width:32px; height:32px; border-radius:8px; background:#2563eb; color:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.brand--putih { color:#fff; }
.brand--putih .brand__logo { background:#fff; color:#2563eb; }
.form .brand { margin-bottom:28px; }
</style>
<?php }
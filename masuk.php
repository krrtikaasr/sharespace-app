<?php
require 'includes/auth.php';

// sudah login? langsung arahkan sesuai role
if (!empty($_SESSION['user'])) { header('Location: ' . arah_role($_SESSION['user']['role'])); exit; }

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  if (!csrf_ok()) {
    $error = 'Sesi tidak valid, muat ulang halaman.';
  } else {
    $st = $db->prepare('SELECT id, nama, password, role FROM users WHERE email = ?');
    $st->execute([$email]);
    $u = $st->fetch();

    if ($u && password_verify($pass, $u['password'])) {
      session_regenerate_id(true);
      $_SESSION['user'] = ['id' => $u['id'], 'nama' => $u['nama'], 'role' => $u['role']];
      if (!empty($_POST['ingat'])) { // ingat saya: cookie sesi 30 hari
        setcookie(session_name(), session_id(), time() + 60*60*24*30, '/', '', false, true);
      }
      header('Location: ' . arah_role($u['role'])); // admin -> admin/beranda.php, user -> beranda.php
      exit;
    }
    $error = 'Email atau kata sandi salah.';
  }
}

$svg_email = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>';
$svg_lock  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>';
$svg_eye   = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
$svg_arrow = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block;vertical-align:middle;margin-left:6px" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/auth-style.php'; ?>
<body>
<div class="auth">
  <?php include 'includes/auth-kiri.php'; ?>

  <div class="auth__kanan">
    <form class="form" method="post" novalidate>
      <span class="brand__logo" style="display:inline-flex;align-items:center;justify-content:center;color:#fff;">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M11 14h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 16"/><path d="m7 20 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9"/><path d="m2 15 6 6"/><path d="M19.5 8.5c.7-.7 1.5-1.6 1.5-2.7A2.73 2.73 0 0 0 16 4a2.78 2.78 0 0 0-5 1.8c0 1.2.8 2 1.5 2.8L16 12Z"/>
  </svg>
</span>
      <h2>Masuk</h2>
      <p class="sub">Selamat datang kembali. Masuk untuk melanjutkan.</p>

      <?php if (!empty($_GET['daftar'])): ?><div class="pesan pesan--ok">Akun berhasil dibuat. Silakan masuk.</div><?php endif; ?>
      <?php if ($error): ?><div class="pesan pesan--error"><?= e($error) ?></div><?php endif; ?>

      <?= csrf_field() ?>
      <label class="lbl" for="email">Email</label>
      <div class="input"><span class="ikon"><?= $svg_email ?></span>
        <input type="email" id="email" name="email" placeholder="nama@gmail.com" value="<?= e($email) ?>" required>
      </div>

      <label class="lbl" for="password">Kata Sandi</label>
      <div class="input"><span class="ikon"><?= $svg_lock ?></span>
        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
        <button type="button" onclick="lihat(this)" aria-label="Lihat kata sandi"><?= $svg_eye ?></button>
      </div>

      <div class="baris">
        <label class="cek" style="margin:0"><input type="checkbox" name="ingat" checked> Ingat saya</label>
        <a href="#">Lupa Kata Sandi?</a>
      </div>

      <button class="tombol" type="submit">Masuk<?= $svg_arrow ?></button>
      <p class="bawah">Baru di ShareSpace? <a href="daftar.php">Daftar Akun Baru</a></p>
    </form>
  </div>
</div>
</body>
</html>
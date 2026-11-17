<?php
require 'includes/auth.php';
const MODE_PREVIEW = true;

if (!empty($_SESSION['user'])) { header('Location: ' . arah_role($_SESSION['user']['role'])); exit; }

$errors = [];
$nama = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama    = trim($_POST['nama'] ?? '');
  $email   = trim($_POST['email'] ?? '');
  $pass    = $_POST['password'] ?? '';
  $konfirm = $_POST['konfirmasi'] ?? '';

  if (!csrf_ok())                                   $errors[] = 'Sesi tidak valid, muat ulang halaman.';
  if ($nama === '')                                 $errors[] = 'Nama lengkap wajib diisi.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL))   $errors[] = 'Format email tidak valid.';
  if (strlen($pass) < 8)                            $errors[] = 'Kata sandi minimal 8 karakter.';
  if ($pass !== $konfirm)                           $errors[] = 'Konfirmasi kata sandi tidak sama.';
  if (empty($_POST['setuju']))                      $errors[] = 'Setujui Terms of Service dan Privacy Policy.';

  if (!$errors) {
    $cek = $db->prepare('SELECT id FROM users WHERE email = ?');
    $cek->execute([$email]);
    if ($cek->fetch()) {
      $errors[] = 'Email sudah terdaftar.';
    } else {
      // role selalu 'user'. Admin diset manual lewat database.
      $ins = $db->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')");
      $ins->execute([$nama, $email, password_hash($pass, PASSWORD_DEFAULT)]);
      header('Location: masuk.php?daftar=1');
      exit;
    }
  }
}

$svg_user  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 20c0-3.8 3.1-6.5 7-6.5s7 2.7 7 6.5"/></svg>';
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
      <div class="brand" style="margin-bottom:28px"><span class="brand__logo"></span> ShareSpace</div>
      <h2>Daftar Akun Baru</h2>
      <p class="sub">A fresh start. All in one place.</p>

      <?php foreach ($errors as $er): ?><div class="pesan pesan--error"><?= e($er) ?></div><?php endforeach; ?>

      <?= csrf_field() ?>
      <label class="lbl" for="nama">Nama Lengkap</label>
      <div class="input"><span class="ikon"><?= $svg_user ?></span>
        <input type="text" id="nama" name="nama" placeholder="Masukkan Nama Lengkap Anda" value="<?= e($nama) ?>" required>
      </div>

      <label class="lbl" for="email">Email</label>
      <div class="input"><span class="ikon"><?= $svg_email ?></span>
        <input type="email" id="email" name="email" placeholder="nama@gmail.com" value="<?= e($email) ?>" required>
      </div>

      <label class="lbl" for="password">Kata Sandi</label>
      <div class="input"><span class="ikon"><?= $svg_lock ?></span>
        <input type="password" id="password" name="password" placeholder="Buat Kata Sandi" required>
        <button type="button" onclick="lihat(this)" aria-label="Lihat kata sandi"><?= $svg_eye ?></button>
      </div>

      <label class="lbl" for="konfirmasi">Konfirmasi Kata Sandi</label>
      <div class="input"><span class="ikon"><?= $svg_lock ?></span>
        <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Masukkan Kata Sandi Anda Kembali" required>
        <button type="button" onclick="lihat(this)" aria-label="Lihat kata sandi"><?= $svg_eye ?></button>
      </div>

      <label class="cek"><input type="checkbox" name="setuju">
        <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy.</a></span></label>

      <button class="tombol" type="submit">Buat Akun<?= $svg_arrow ?></button>
      <p class="bawah">Sudah Punya Akun? <a href="masuk.php">Masuk</a></p>
    </form>
  </div>
</div>
</body>
</html>
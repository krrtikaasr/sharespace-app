<?php
require 'includes/auth.php';
require 'includes/logo.php';
require 'includes/ikon.php';

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
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/auth-style.php'; ?>
<?php logo_style(); ?>
<body>
<div class="auth">
  <?php include 'includes/auth-kiri.php'; ?>

  <div class="auth__kanan">
    <form class="form" method="post" novalidate>
      <?= logo_brand() ?>

      <h2>Daftar Akun Baru</h2>
      <p class="sub">Ayo bergabung bersama kami dan mulai menebar dampak!</p>

      <?php foreach ($errors as $er): ?><div class="pesan pesan--error"><?= e($er) ?></div><?php endforeach; ?>

      <?= csrf_field() ?>

      <label class="lbl" for="nama">Nama Lengkap</label>
      <div class="input"><span class="ikon"><?= ikon('user') ?></span>
        <input type="text" id="nama" name="nama" placeholder="Masukkan Nama Lengkap Anda" value="<?= e($nama) ?>" autocomplete="name" required>
      </div>

      <label class="lbl" for="email">Email</label>
      <div class="input"><span class="ikon"><?= ikon('email') ?></span>
        <input type="email" id="email" name="email" placeholder="nama@gmail.com" value="<?= e($email) ?>" autocomplete="email" required>
      </div>

      <label class="lbl" for="password">Kata Sandi</label>
      <div class="input"><span class="ikon"><?= ikon('lock') ?></span>
        <input type="password" id="password" name="password" placeholder="Buat Kata Sandi" autocomplete="new-password" required>
        <button type="button" onclick="lihat(this)" aria-label="Lihat kata sandi"><?= ikon('eye') ?></button>
      </div>

      <label class="lbl" for="konfirmasi">Konfirmasi Kata Sandi</label>
      <div class="input"><span class="ikon"><?= ikon('lock') ?></span>
        <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Masukkan Kata Sandi Anda Kembali" autocomplete="new-password" required>
        <button type="button" onclick="lihat(this)" aria-label="Lihat kata sandi"><?= ikon('eye') ?></button>
      </div>

      <label class="cek"><input type="checkbox" name="setuju">
        <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy.</a></span></label>

      <button class="tombol" type="submit">Buat Akun<?= ikon('arrow') ?></button>
      <p class="bawah">Sudah Punya Akun? <a href="masuk.php">Masuk</a></p>
    </form>
  </div>
</div>
</body>
</html>
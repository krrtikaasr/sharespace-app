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
?>
<?php include 'includes/head.php'; ?>
<?php include 'includes/auth-style.php'; ?>
<body>
<div class="auth">
  <?php include 'includes/auth-kiri.php'; ?>

  <div class="auth__kanan">
    <form class="form" method="post" novalidate>
      <div class="brand brand--mobile"><span class="brand__logo"></span> ShareSpace</div>
      <h2>Masuk</h2>
      <p class="sub">Selamat datang kembali. Masuk untuk melanjutkan.</p>

      <?php if (!empty($_GET['daftar'])): ?><div class="pesan pesan--ok">Akun berhasil dibuat. Silakan masuk.</div><?php endif; ?>
      <?php if ($error): ?><div class="pesan pesan--error"><?= e($error) ?></div><?php endif; ?>

      <?= csrf_field() ?>
      <label class="lbl" for="email">Email</label>
      <div class="input"><span class="ikon">✉️</span>
        <input type="email" id="email" name="email" placeholder="nama@gmail.com" value="<?= e($email) ?>" required>
      </div>

      <label class="lbl" for="password">Kata Sandi</label>
      <div class="input"><span class="ikon">🔒</span>
        <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
        <button type="button" onclick="lihat(this)" aria-label="Lihat kata sandi">👁</button>
      </div>

      <div class="baris">
        <label class="cek" style="margin:0"><input type="checkbox" name="ingat" checked> Ingat saya</label>
        <a href="#">Lupa Kata Sandi?</a>
      </div>

      <button class="tombol" type="submit">Masuk →</button>
      <p class="bawah">Baru di ShareSpace? <a href="daftar.php">Daftar Akun Baru</a></p>
    </form>
  </div>
</div>
</body>
</html>
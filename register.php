<?php
session_start();
// require_once 'config/database.php';

// Kalau sudah login, langsung ke beranda
if (isset($_SESSION['user_id'])) {
    header('Location: beranda.php');
    exit;
}

$errors = [];
$nama   = '';
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';
    $setuju     = isset($_POST['setuju']);

    if ($nama === '') {
        $errors[] = 'Nama lengkap wajib diisi.';
    }
    if ($email === '') {
        $errors[] = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email belum benar.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Kata sandi minimal 8 karakter.';
    }
    if ($password !== $konfirmasi) {
        $errors[] = 'Konfirmasi kata sandi tidak sama.';
    }
    if (!$setuju) {
        $errors[] = 'Centang persetujuan Ketentuan Layanan dan Kebijakan Privasi dulu, ya.';
    }

    if (empty($errors)) {
        // --- Cek email sudah dipakai atau belum (aktifkan setelah database siap) ---
        // $cek = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
        // mysqli_stmt_bind_param($cek, 's', $email);
        // mysqli_stmt_execute($cek);
        // if (mysqli_fetch_assoc(mysqli_stmt_get_result($cek))) {
        //     $errors[] = 'Email ini sudah terdaftar. Silakan masuk.';
        // }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // --- Simpan user baru ---
            // $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
            // mysqli_stmt_bind_param($stmt, 'sss', $nama, $email, $hash);
            // mysqli_stmt_execute($stmt);
            // $id_baru = mysqli_insert_id($conn);

            $id_baru = 1; // contoh, ganti dengan mysqli_insert_id($conn)

            session_regenerate_id(true);
            $_SESSION['user_id']   = $id_baru;
            $_SESSION['user_nama'] = $nama;

            header('Location: beranda.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun - ShareSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100">
<div class="max-w-md mx-auto min-h-screen bg-slate-50 relative px-6 pt-12 pb-10 flex flex-col">

    <!-- Logo -->
    <div class="flex items-center gap-2.5">
        <span class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 007.07 0l2.83-2.83a5 5 0 00-7.07-7.07L11.5 4.43"/><path d="M14 11a5 5 0 00-7.07 0L4.1 13.83a5 5 0 007.07 7.07L12.5 19.57"/></svg>
        </span>
        <span class="text-lg font-bold text-slate-800">ShareSpace</span>
    </div>

    <!-- Judul -->
    <div class="mt-10">
        <h1 class="text-3xl font-bold text-slate-800">Buat akunmu</h1>
        <p class="text-sm text-slate-500 mt-2">Awal yang baru. Semua di satu tempat.</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="mt-5 bg-red-50 text-red-700 p-3 rounded-xl text-sm space-y-1">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Form daftar -->
    <form method="POST" class="mt-6" novalidate>
        <label for="nama" class="block text-sm font-semibold text-slate-800">Nama lengkap</label>
        <div class="mt-2 flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3.5 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100">
            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0116 0"/></svg>
            <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama); ?>" placeholder="Masukkan nama lengkapmu" autocomplete="name" class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 outline-none">
        </div>

        <label for="email" class="block text-sm font-semibold text-slate-800 mt-5">Email</label>
        <div class="mt-2 flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3.5 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100">
            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email); ?>" placeholder="kamu@contoh.com" autocomplete="email" class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 outline-none">
        </div>

        <label for="password" class="block text-sm font-semibold text-slate-800 mt-5">Kata sandi</label>
        <div class="mt-2 flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3.5 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100">
            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
            <input type="password" id="password" name="password" placeholder="Buat kata sandi (min. 8 karakter)" autocomplete="new-password" class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 outline-none">
            <button type="button" data-toggle="password" aria-label="Tampilkan kata sandi" class="text-slate-400 hover:text-slate-600">
                <svg class="ikon-mata w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="ikon-mata-coret w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.9 10.9 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.06-5.94M9.9 4.24A10.9 10.9 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19M14.12 14.12a3 3 0 11-4.24-4.24M1 1l22 22"/></svg>
            </button>
        </div>

        <label for="konfirmasi" class="block text-sm font-semibold text-slate-800 mt-5">Konfirmasi kata sandi</label>
        <div class="mt-2 flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3.5 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100">
            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
            <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Masukkan ulang kata sandi" autocomplete="new-password" class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 outline-none">
            <button type="button" data-toggle="konfirmasi" aria-label="Tampilkan kata sandi" class="text-slate-400 hover:text-slate-600">
                <svg class="ikon-mata w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="ikon-mata-coret w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.9 10.9 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.06-5.94M9.9 4.24A10.9 10.9 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19M14.12 14.12a3 3 0 11-4.24-4.24M1 1l22 22"/></svg>
            </button>
        </div>

        <label class="flex items-start gap-3 mt-5 text-xs text-slate-600 cursor-pointer">
            <input type="checkbox" name="setuju" class="w-5 h-5 mt-0.5 rounded border-slate-300 accent-blue-600 shrink-0" <?= isset($_POST['setuju']) ? 'checked' : ''; ?>>
            <span>Saya setuju dengan <a href="syarat.php" class="font-semibold text-blue-600 hover:underline">Ketentuan Layanan</a> dan <a href="privasi.php" class="font-semibold text-blue-600 hover:underline">Kebijakan Privasi</a>.</span>
        </label>

        <button type="submit" class="mt-6 flex items-center justify-center gap-2 w-full py-3.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
            Buat Akun
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
    </form>

    <!-- Sudah punya akun -->
    <p class="text-center text-sm text-slate-500 mt-auto pt-10">
        Sudah punya akun?
        <a href="login.php" class="font-semibold text-blue-600 hover:underline">Masuk</a>
    </p>
</div>

<script>
    // Tombol mata untuk semua kolom kata sandi
    document.querySelectorAll('[data-toggle]').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            const input = document.getElementById(tombol.dataset.toggle);
            const tersembunyi = input.type === 'password';
            input.type = tersembunyi ? 'text' : 'password';
            tombol.querySelector('.ikon-mata').classList.toggle('hidden', tersembunyi);
            tombol.querySelector('.ikon-mata-coret').classList.toggle('hidden', !tersembunyi);
            tombol.setAttribute('aria-label', tersembunyi ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        });
    });
</script>
</body>
</html>
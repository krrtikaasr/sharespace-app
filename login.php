<?php
session_start();
// require_once 'config/database.php';

// Kalau sudah login, langsung ke beranda
if (isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = null;
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $ingat    = isset($_POST['ingat']);

    if ($email === '' || $password === '') {
        $error = 'Email dan kata sandi wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email belum benar.';
    } else {
        // --- Ambil user dari database (aktifkan setelah config/database.php siap) ---
        // $stmt = mysqli_prepare($conn, "SELECT id, nama, password FROM users WHERE email = ? LIMIT 1");
        // mysqli_stmt_bind_param($stmt, 's', $email);
        // mysqli_stmt_execute($stmt);
        // $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        // Data contoh supaya halaman bisa dicoba tanpa database
        // Email: kiaraanastasia@gmail.com | Kata sandi: sharespace123
        $user = [
            'id'       => 1,
            'nama'     => 'Kiara Anastasia',
            'email'    => 'kiaraanastasia@gmail.com',
            'password' => password_hash('sharespace123', PASSWORD_DEFAULT),
        ];
        if ($email !== $user['email']) {
            $user = null;
        }

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];

            // "Ingat saya": perpanjang cookie sesi jadi 30 hari
            if ($ingat) {
                setcookie(session_name(), session_id(), [
                    'expires'  => time() + 60 * 60 * 24 * 30,
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }

            header('Location: login.php');
            exit;
        }

        $error = 'Email atau kata sandi salah. Coba lagi, ya.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - ShareSpace</title>
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

    <!-- Sapaan -->
    <div class="mt-10">
        <h1 class="text-3xl font-bold text-slate-800">Halo!</h1>
        <p class="text-sm text-slate-500 mt-2">Senang bertemu lagi. Yuk, lanjutkan dari yang terakhir.</p>
    </div>

    <?php if ($error): ?>
        <div class="mt-5 bg-red-50 text-red-700 p-3 rounded-xl text-sm"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Form login -->
    <form method="POST" class="mt-6" novalidate>
        <label for="email" class="block text-sm font-semibold text-slate-800">Email</label>
        <div class="mt-2 flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3.5 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100">
            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email); ?>" placeholder="kamu@contoh.com" autocomplete="email" class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 outline-none">
        </div>

        <label for="password" class="block text-sm font-semibold text-slate-800 mt-5">Kata sandi</label>
        <div class="mt-2 flex items-center gap-3 bg-white border border-slate-200 rounded-xl px-4 py-3.5 focus-within:border-blue-600 focus-within:ring-2 focus-within:ring-blue-100">
            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 outline-none">
            <button type="button" id="toggle-password" aria-label="Tampilkan kata sandi" class="text-slate-400 hover:text-slate-600">
                <svg id="ikon-mata" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg id="ikon-mata-coret" class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17.94 17.94A10.9 10.9 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.06-5.94M9.9 4.24A10.9 10.9 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19M14.12 14.12a3 3 0 11-4.24-4.24M1 1l22 22"/></svg>
            </button>
        </div>

        <div class="flex items-center justify-between mt-4">
            <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                <input type="checkbox" name="ingat" class="w-4 h-4 rounded border-slate-300 accent-blue-600" checked>
                Ingat saya
            </label>
            <a href="lupa_password.php" class="text-xs font-semibold text-blue-600 hover:underline">Lupa kata sandi?</a>
        </div>

        <button type="submit" class="mt-6 flex items-center justify-center gap-2 w-full py-3.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
            Masuk
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
    </form>

    <!-- Pemisah -->
    <div class="flex items-center gap-3 mt-6">
        <span class="flex-1 h-px bg-slate-200"></span>
        <span class="text-xs text-slate-400">atau lanjutkan dengan</span>
        <span class="flex-1 h-px bg-slate-200"></span>
    </div>

    <!-- Login sosial -->
    <div class="grid grid-cols-2 gap-3 mt-5">
        <a href="login_google.php" class="flex items-center justify-center gap-2 py-3 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-800 hover:bg-slate-50">
            <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M23 12.27c0-.8-.07-1.56-.2-2.27H12v4.3h6.17a5.28 5.28 0 01-2.29 3.47v2.88h3.7C21.74 18.66 23 15.7 23 12.27z"/><path fill="#34A853" d="M12 23c3.1 0 5.7-1.03 7.58-2.8l-3.7-2.88c-1.03.69-2.34 1.1-3.88 1.1-2.98 0-5.5-2.01-6.4-4.72H1.78v2.97A11 11 0 0012 23z"/><path fill="#FBBC05" d="M5.6 13.7a6.6 6.6 0 010-4.2V6.53H1.78a11 11 0 000 9.94l3.82-2.77z"/><path fill="#EA4335" d="M12 5.58c1.69 0 3.2.58 4.39 1.72l3.29-3.29C17.7 2.15 15.1 1 12 1A11 11 0 001.78 6.53L5.6 9.5C6.5 6.79 9.02 5.58 12 5.58z"/></svg>
            Google
        </a>
        <a href="login_apple.php" class="flex items-center justify-center gap-2 py-3 rounded-xl bg-white border border-slate-200 text-sm font-semibold text-slate-800 hover:bg-slate-50">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M16.37 12.6c-.02-2.2 1.8-3.26 1.88-3.31-1.02-1.5-2.62-1.7-3.19-1.73-1.35-.14-2.65.8-3.34.8-.69 0-1.75-.78-2.88-.76-1.48.02-2.85.86-3.61 2.19-1.54 2.67-.39 6.63 1.1 8.8.73 1.06 1.6 2.25 2.74 2.21 1.1-.04 1.52-.71 2.85-.71 1.33 0 1.7.71 2.87.69 1.19-.02 1.94-1.08 2.66-2.15.84-1.23 1.19-2.42 1.2-2.48-.03-.01-2.3-.88-2.28-3.55zM14.2 6.1c.6-.73 1.01-1.75.9-2.76-.87.04-1.92.58-2.54 1.31-.56.65-1.05 1.68-.92 2.67.97.07 1.96-.49 2.56-1.22z"/></svg>
            Apple
        </a>
    </div>

    <!-- Info keamanan -->
    <p class="flex items-center justify-center gap-1.5 text-xs text-slate-500 mt-6">
        <svg class="w-3.5 h-3.5 text-amber-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Data kamu aman bersama kami.
    </p>

    <!-- register -->
    <p class="text-center text-sm text-slate-500 mt-auto pt-10">
        Baru di ShareSpace?
        <a href="register.php" class="font-semibold text-blue-600 hover:underline">Buat Akun</a>
    </p>
</div>

<script>
    const inputPassword = document.getElementById('password');
    const tombolMata    = document.getElementById('toggle-password');
    const ikonMata      = document.getElementById('ikon-mata');
    const ikonMataCoret = document.getElementById('ikon-mata-coret');

    tombolMata.addEventListener('click', function () {
        const tersembunyi = inputPassword.type === 'password';
        inputPassword.type = tersembunyi ? 'text' : 'password';
        ikonMata.classList.toggle('hidden', tersembunyi);
        ikonMataCoret.classList.toggle('hidden', !tersembunyi);
        tombolMata.setAttribute('aria-label', tersembunyi ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
    });
</script>
</body>
</html>
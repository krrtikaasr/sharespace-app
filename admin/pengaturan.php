<?php
/* ============================================================
   pengaturan.php  -  profil admin, kata sandi, pengaturan platform
   Mode preview / database diatur di admin-layout.php

   Tabel yang dipakai (sesuaikan bila berbeda):
   - users(id, nama, email, password, foto)
   - pengaturan(kunci VARCHAR PRIMARY KEY, nilai TEXT)
       CREATE TABLE pengaturan (kunci VARCHAR(60) PRIMARY KEY, nilai TEXT);
   ============================================================ */
require_once 'admin-layout.php';

/* ---------- Definisi pengaturan ---------- */
$bawaan = [
    'nama_platform'    => 'ShareSpace',
    'email_kontak'     => 'halo@sharespace.com',
    'wa_kontak'        => '',
    'verifikasi_donasi' => '1',
    'wajib_foto'       => '1',
    'batasi_permintaan' => '0',
    'pendaftaran_baru' => '1',
    'notif_email'      => '1',
    'mode_perawatan'   => '0',
];
$toggle_donasi = [
    'verifikasi_donasi' => ['Verifikasi donasi sebelum tayang', 'Donasi baru harus disetujui admin dulu.'],
    'wajib_foto'        => ['Wajib unggah foto barang',         'Donatur harus menyertakan foto barang.'],
    'batasi_permintaan' => ['Batasi 1 permintaan per barang',   'Satu barang hanya bisa diminta satu orang.'],
];
$toggle_akses = [
    'pendaftaran_baru' => ['Pendaftaran pengguna baru', 'Izinkan akun baru mendaftar.'],
    'notif_email'      => ['Notifikasi email',          'Kirim email untuk aktivitas penting.'],
    'mode_perawatan'   => ['Mode perawatan',            'Tutup sementara akses pengguna biasa.'],
];

/* ---------- Data admin & pengaturan saat ini ---------- */
$id_admin = (int) ($_SESSION['user']['id'] ?? 0);

if (MODE_PREVIEW) {
    $admin = ['id' => 1, 'nama' => 'Admin ShareSpace', 'email' => 'admin@sharespace.com', 'foto' => ''];
    $atur  = $bawaan;
} else {
    $admin = ambil_satu($conn, "SELECT id, nama, email, foto FROM users WHERE id = ?", 'i', $id_admin);
    if (!$admin) { header('Location: ../masuk.php'); exit; }
    $atur = $bawaan;
    foreach (ambil_semua($conn, "SELECT kunci, nilai FROM pengaturan") as $row) {
        if (array_key_exists($row['kunci'], $atur)) $atur[$row['kunci']] = $row['nilai'];
    }
}

function simpan_pengaturan($conn, array $pasangan) {
    $stmt = mysqli_prepare($conn, "INSERT INTO pengaturan (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai = VALUES(nilai)");
    foreach ($pasangan as $k => $v) {
        mysqli_stmt_bind_param($stmt, "ss", $k, $v);
        mysqli_stmt_execute($stmt);
    }
    mysqli_stmt_close($stmt);
}

/* ---------- Proses form ---------- */
$galat = [];   // pesan error per form
$ok    = $_GET['ok'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!MODE_PREVIEW && !hash_equals($csrf, $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die("Permintaan tidak valid.");
    }
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'profil') {
        $nama  = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $foto  = $admin['foto'] ?? '';

        if ($nama === '') $galat['profil'] = 'Nama lengkap wajib diisi.';
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $galat['profil'] = 'Format email tidak valid.';

        // Unggah foto (opsional): jpg/png/webp, maks 2 MB
        if (!$galat && !empty($_FILES['foto']['name']) && !MODE_PREVIEW) {
            $f   = $_FILES['foto'];
            $mim = $f['error'] === UPLOAD_ERR_OK ? mime_content_type($f['tmp_name']) : '';
            $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mim] ?? null;
            if (!$ext || $f['size'] > 2 * 1024 * 1024) {
                $galat['profil'] = 'Foto harus JPG/PNG/WEBP dan maksimal 2 MB.';
            } else {
                $dir = __DIR__ . '/../assets/img/avatar/';
                if (!is_dir($dir)) mkdir($dir, 0755, true);
                $nama_file = 'admin-' . (int) $admin['id'] . '-' . time() . '.' . $ext;
                if (move_uploaded_file($f['tmp_name'], $dir . $nama_file)) $foto = 'assets/img/avatar/' . $nama_file;
                else $galat['profil'] = 'Foto gagal diunggah.';
            }
        }

        if (!$galat) {
            if (!MODE_PREVIEW) {
                $stmt = mysqli_prepare($conn, "UPDATE users SET nama = ?, email = ?, foto = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "sssi", $nama, $email, $foto, $admin['id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $_SESSION['user']['nama'] = $nama;
            }
            header('Location: pengaturan.php?ok=profil'); exit;
        }
        $admin['nama'] = $nama; $admin['email'] = $email;

    } elseif ($aksi === 'sandi') {
        $lama = $_POST['sandi_lama'] ?? '';
        $baru = $_POST['sandi_baru'] ?? '';
        $ulang = $_POST['sandi_ulang'] ?? '';

        if (strlen($baru) < 8)      $galat['sandi'] = 'Kata sandi baru minimal 8 karakter.';
        elseif ($baru !== $ulang)   $galat['sandi'] = 'Konfirmasi kata sandi tidak sama.';
        elseif (!MODE_PREVIEW) {
            $row = ambil_satu($conn, "SELECT password FROM users WHERE id = ?", 'i', $admin['id']);
            if (!$row || !password_verify($lama, $row['password'])) {
                $galat['sandi'] = 'Kata sandi lama salah.';
            } else {
                $hash = password_hash($baru, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $hash, $admin['id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }
        if (!$galat) { header('Location: pengaturan.php?ok=sandi'); exit; }

    } elseif ($aksi === 'umum') {
        $nilai = [
            'nama_platform' => trim($_POST['nama_platform'] ?? ''),
            'email_kontak'  => trim($_POST['email_kontak'] ?? ''),
            'wa_kontak'     => preg_replace('/[^0-9+]/', '', $_POST['wa_kontak'] ?? ''),
        ];
        if ($nilai['nama_platform'] === '') $galat['umum'] = 'Nama platform wajib diisi.';
        elseif (!filter_var($nilai['email_kontak'], FILTER_VALIDATE_EMAIL)) $galat['umum'] = 'Format email kontak tidak valid.';

        if (!$galat) {
            if (!MODE_PREVIEW) simpan_pengaturan($conn, $nilai);
            header('Location: pengaturan.php?ok=umum'); exit;
        }
        $atur = array_merge($atur, $nilai);

    } elseif ($aksi === 'donasi' || $aksi === 'akses') {
        $daftar = $aksi === 'donasi' ? $toggle_donasi : $toggle_akses;
        $nilai  = [];
        foreach ($daftar as $k => $_) $nilai[$k] = isset($_POST[$k]) ? '1' : '0';
        if (!MODE_PREVIEW) simpan_pengaturan($conn, $nilai);
        header('Location: pengaturan.php?ok=' . $aksi); exit;
    }
}

$pesan_ok = [
    'profil' => 'Profil berhasil disimpan.',
    'sandi'  => 'Kata sandi berhasil diubah.',
    'umum'   => 'Pengaturan umum berhasil disimpan.',
    'donasi' => 'Aturan donasi berhasil disimpan.',
    'akses'  => 'Akses pengguna berhasil disimpan.',
];

/* ---------- Komponen kecil ---------- */
$input = 'block w-full mt-1.5 px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-sm text-slate-800 focus:outline-none focus:border-blue-500';
$tombol = 'px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold';

function kotak_pesan($kunci, $ok, $pesan_ok, $galat) {
    if (isset($galat[$kunci])) {
        echo '<div class="mb-4 bg-red-50 text-red-600 text-sm rounded-xl px-4 py-3">' . e($galat[$kunci]) . '</div>';
    } elseif ($ok === $kunci) {
        echo '<div class="mb-4 bg-green-50 text-green-700 text-sm rounded-xl px-4 py-3">' . e($pesan_ok[$kunci])
           . (MODE_PREVIEW ? ' (mode preview, perubahan tidak disimpan)' : '') . '</div>';
    }
}

function baris_toggle($kunci, $judul, $ket, $aktif) { ?>
    <label class="flex items-center justify-between gap-4 py-3 border-t border-slate-100 first:border-t-0 cursor-pointer">
        <span>
            <span class="block text-sm font-semibold text-slate-800"><?= e($judul); ?></span>
            <span class="block text-xs text-slate-400 mt-0.5"><?= e($ket); ?></span>
        </span>
        <input type="checkbox" name="<?= e($kunci); ?>" value="1" <?= $aktif ? 'checked' : ''; ?> class="peer sr-only">
        <span class="relative shrink-0 w-10 h-6 rounded-full bg-slate-300 peer-checked:bg-blue-600 transition-colors
                     after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:rounded-full after:bg-white after:transition-transform
                     peer-checked:after:translate-x-4"></span>
    </label>
<?php }

layout_awal('Pengaturan', 'pengaturan');
?>

            <!-- Judul -->
            <h1 class="text-3xl lg:text-4xl font-bold text-slate-900">Pengaturan</h1>
            <p class="text-sm text-slate-500 mt-2">Atur akun admin dan aturan dasar ShareSpace.</p>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6 items-start">

                <!-- ===== Kolom kiri ===== -->
                <div class="space-y-6">

                    <!-- Profil admin -->
                    <form method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 p-5 lg:p-6">
                        <input type="hidden" name="aksi" value="profil">
                        <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                        <h2 class="text-base font-bold text-slate-800 mb-4">Profil admin</h2>
                        <?php kotak_pesan('profil', $ok, $pesan_ok, $galat); ?>

                        <div class="flex items-center gap-4">
                            <?php if (!empty($admin['foto'])): ?>
                                <img src="../<?= e($admin['foto']); ?>" alt="Foto admin" class="w-14 h-14 rounded-full object-cover border border-slate-200">
                            <?php else: ?>
                                <span class="w-14 h-14 rounded-full bg-slate-100 border border-slate-200 text-slate-500 text-lg font-semibold flex items-center justify-center">
                                    <?= e(inisial($admin['nama'])); ?>
                                </span>
                            <?php endif; ?>
                            <label class="text-xs font-semibold text-blue-600 cursor-pointer hover:underline">
                                Ubah foto
                                <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="hidden"
                                       onchange="this.nextElementSibling.textContent = this.files[0] ? this.files[0].name : ''">
                                <span class="ml-2 font-normal text-slate-400"></span>
                            </label>
                        </div>

                        <label class="block text-xs font-semibold text-slate-600 mt-4">Nama lengkap
                            <input type="text" name="nama" value="<?= e($admin['nama']); ?>" required class="<?= $input; ?>">
                        </label>
                        <label class="block text-xs font-semibold text-slate-600 mt-4">Email
                            <input type="email" name="email" value="<?= e($admin['email']); ?>" required class="<?= $input; ?>">
                        </label>
                        <button type="submit" class="<?= $tombol; ?> mt-5">Simpan profil</button>
                    </form>

                    <!-- Ubah kata sandi -->
                    <form method="POST" class="bg-white rounded-2xl border border-slate-200 p-5 lg:p-6">
                        <input type="hidden" name="aksi" value="sandi">
                        <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                        <h2 class="text-base font-bold text-slate-800 mb-4">Ubah kata sandi</h2>
                        <?php kotak_pesan('sandi', $ok, $pesan_ok, $galat); ?>

                        <label class="block text-xs font-semibold text-slate-600">Kata sandi lama
                            <input type="password" name="sandi_lama" required autocomplete="current-password" class="<?= $input; ?>">
                        </label>
                        <label class="block text-xs font-semibold text-slate-600 mt-4">Kata sandi baru
                            <input type="password" name="sandi_baru" required minlength="8" autocomplete="new-password" class="<?= $input; ?>">
                        </label>
                        <label class="block text-xs font-semibold text-slate-600 mt-4">Konfirmasi kata sandi baru
                            <input type="password" name="sandi_ulang" required minlength="8" autocomplete="new-password" class="<?= $input; ?>">
                        </label>
                        <button type="submit" class="<?= $tombol; ?> mt-5">Ubah kata sandi</button>
                    </form>
                </div>

                <!-- ===== Kolom kanan ===== -->
                <div class="space-y-6">

                    <!-- Pengaturan umum -->
                    <form method="POST" class="bg-white rounded-2xl border border-slate-200 p-5 lg:p-6">
                        <input type="hidden" name="aksi" value="umum">
                        <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                        <h2 class="text-base font-bold text-slate-800 mb-4">Pengaturan umum</h2>
                        <?php kotak_pesan('umum', $ok, $pesan_ok, $galat); ?>

                        <label class="block text-xs font-semibold text-slate-600">Nama platform
                            <input type="text" name="nama_platform" value="<?= e($atur['nama_platform']); ?>" required class="<?= $input; ?>">
                        </label>
                        <label class="block text-xs font-semibold text-slate-600 mt-4">Email kontak
                            <input type="email" name="email_kontak" value="<?= e($atur['email_kontak']); ?>" required class="<?= $input; ?>">
                        </label>
                        <label class="block text-xs font-semibold text-slate-600 mt-4">Nomor WhatsApp kontak
                            <input type="tel" name="wa_kontak" value="<?= e($atur['wa_kontak']); ?>" placeholder="+62..." class="<?= $input; ?>">
                        </label>
                        <button type="submit" class="<?= $tombol; ?> mt-5">Simpan pengaturan</button>
                    </form>

                    <!-- Aturan donasi -->
                    <form method="POST" class="bg-white rounded-2xl border border-slate-200 p-5 lg:p-6">
                        <input type="hidden" name="aksi" value="donasi">
                        <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                        <h2 class="text-base font-bold text-slate-800 mb-2">Aturan donasi</h2>
                        <?php kotak_pesan('donasi', $ok, $pesan_ok, $galat); ?>
                        <?php foreach ($toggle_donasi as $k => $t) baris_toggle($k, $t[0], $t[1], $atur[$k] === '1'); ?>
                        <button type="submit" class="<?= $tombol; ?> mt-4">Simpan aturan</button>
                    </form>

                    <!-- Akses pengguna -->
                    <form method="POST" class="bg-white rounded-2xl border border-slate-200 p-5 lg:p-6">
                        <input type="hidden" name="aksi" value="akses">
                        <input type="hidden" name="csrf" value="<?= e($csrf); ?>">
                        <h2 class="text-base font-bold text-slate-800 mb-2">Akses pengguna</h2>
                        <?php kotak_pesan('akses', $ok, $pesan_ok, $galat); ?>
                        <?php foreach ($toggle_akses as $k => $t) baris_toggle($k, $t[0], $t[1], $atur[$k] === '1'); ?>
                        <button type="submit" class="<?= $tombol; ?> mt-4">Simpan akses</button>
                    </form>
                </div>
            </div>

<?php layout_akhir(); ?>
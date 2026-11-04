<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Proses jika tombol Verifikasi (Terima/Tolak) diklik
if (isset($_POST['aksi_verifikasi'])) {
    $id_permintaan = $_POST['id_permintaan'];
    $status_baru = $_POST['status_verifikasi']; // Disetujui / Ditolak
    
    $update = mysqli_query($conn, "UPDATE permintaan SET status_verifikasi = '$status_baru' WHERE id_permintaan = '$id_permintaan'");
    if ($update) {
        $sukses = "Status permintaan berhasil diperbarui!";
    }
}

// Proses jika tombol Jadwalkan Penjemputan diklik
if (isset($_POST['atur_jadwal'])) {
    $id_permintaan = $_POST['id_permintaan'];
    $lokasi = mysqli_real_escape_string($conn, $_POST['lokasi_penjemputan']);
    $jadwal = $_POST['jadwal_penjemputan'];

    // Masukkan ke tabel penjemputan
    $query_pickup = "INSERT INTO penjemputan (id_permintaan, lokasi_penjemputan, jadwal_penjemputan, status_pickup) 
                     VALUES ('$id_permintaan', '$lokasi', '$jadwal', 'Menunggu Jemput')
                     ON DUPLICATE KEY UPDATE lokasi_penjemputan='$lokasi', jadwal_penjemputan='$jadwal'";
    mysqli_query($conn, $query_pickup);
    $sukses = "Jadwal penjemputan berhasil disimpan!";
}

// Ambil data permintaan masuk untuk barang yang didonasikan oleh user yang sedang login
$query = "SELECT p.*, i.nama_barang, u.nama AS nama_pencari, u.no_hp 
          FROM permintaan p 
          JOIN items i ON p.id_barang = i.id 
          JOIN users u ON p.id_penerima = u.id 
          WHERE i.user_id = '$user_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status & Riwayat Donasi - ShareSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Status & Riwayat Donasi</h1>
            <a href="dashboard.php" class="text-blue-600 hover:underline text-sm font-medium">&larr; Kembali ke Dashboard</a>
        </div>

        <?php if (isset($sukses)) { echo "<div class='bg-green-50 text-green-700 p-3 rounded-lg mb-4 text-sm'>$sukses</div>"; } ?>

        <!-- List Permintaan Masuk -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 border-b border-slate-100 font-semibold text-slate-700">Daftar Permintaan Barang Masuk</div>
            <div class="divide-y divide-slate-100">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="p-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <h3 class="font-bold text-slate-800"><?= htmlspecialchars($row['nama_barang']); ?></h3>
                                <p class="text-sm text-slate-600">Diminta oleh: <span class="font-medium text-slate-800"><?= htmlspecialchars($row['nama_pencari']); ?></span> (No HP: <?= htmlspecialchars($row['no_hp']); ?>)</p>
                                <p class="text-xs text-slate-400 mt-1">Tanggal: <?= $row['tanggal_pengajuan']; ?></p>
                                
                                <div class="mt-2">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold 
                                        <?= $row['status_verifikasi'] == 'Disetujui' ? 'bg-green-100 text-green-700' : ($row['status_verifikasi'] == 'Ditolak' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'); ?>">
                                        Status: <?= $row['status_verifikasi']; ?>
                                    </span>
                                </div>
                            </div>

                            <!-- Aksi / Tombol Verifikasi & Jadwal -->
                            <div class="flex flex-col gap-2 w-full md:w-auto">
                                <form action="" method="POST" class="flex gap-2">
                                    <input type="hidden" name="id_permintaan" value="<?= $row['id_permintaan']; ?>">
                                    <?php if ($row['status_verifikasi'] == 'Pending'): ?>
                                        <button type="submit" name="aksi_verifikasi" value="Disetujui" class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-green-700">Terima</button>
                                        <button type="submit" name="aksi_verifikasi" value="Ditolak" class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-600">Tolak</button>
                                    <?php endif; ?>
                                </form>

                                <!-- Form Jadwal Penjemputan (Muncul jika disetujui) -->
                                <?php if ($row['status_verifikasi'] == 'Disetujui'): ?>
                                    <form action="" method="POST" class="bg-slate-50 p-3 rounded-xl border border-slate-200 mt-2 space-y-2">
                                        <input type="hidden" name="id_permintaan" value="<?= $row['id_permintaan']; ?>">
                                        <p class="text-xs font-semibold text-slate-700">Atur Jadwal Penjemputan:</p>
                                        <input type="text" name="lokasi_penjemputan" placeholder="Lokasi jemput/alamat" required class="w-full p-1.5 text-xs border rounded">
                                        <input type="datetime-local" name="jadwal_penjemputan" required class="w-full p-1.5 text-xs border rounded">
                                        <button type="submit" name="atur_jadwal" class="w-full bg-blue-600 text-white py-1.5 rounded text-xs font-semibold hover:bg-blue-700">Simpan Jadwal</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="p-6 text-center text-slate-400 text-sm">Belum ada permintaan masuk untuk barangmu.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
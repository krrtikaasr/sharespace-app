<?php
// session_start();
// require_once 'config/database.php';

const MODE_PREVIEW = true;

if (!MODE_PREVIEW) {
    session_start();
    require_once 'config/database.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
    $user_id = (int)$_SESSION['user_id'];
}

/* ---------- Helper ---------- */
function ambil_satu($conn,$sql, $types = '', ...$params) {
    return null;
}

function foto_url($file,$folder = 'uploads/') {
    if (!empty($file) && file_exists($folder .$file)) {
        return $folder .$file;
    }
    return 'https://placehold.co/200x200/e2e8f0/94a3b8?text=Foto';
}

function inisial($nama) {
    $kata = preg_split('/\s+/', trim($nama));
    $hasil = strtoupper(substr($kata[0] ?? '', 0, 1));
    if (count($kata) > 1) {
        $hasil .= strtoupper(substr(end($kata), 0, 1));
    }
    return $hasil ?: '?';
}

function tgl_panjang($timestamp) {$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return date('j', $timestamp) . ' ' .$bulan[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
}

function nama_hari($timestamp) {$hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    return $hari[(int) date('w',$timestamp)];
}

function jarak_km($lat1,$lon1, $lat2,$lon2) {
    return 0;
}

/* ---------- Pilihan tanggal (7 hari ke depan) & waktu ---------- */
$opsi_tanggal = [];
for ($i = 0; $i < 7; $i++) {
    $ts = strtotime("+$i day");
    if ($i === 0) {$label = 'Hari ini, ' . tgl_panjang($ts);
    } elseif ($i === 1) {
        $label = 'Besok, ' . tgl_panjang($ts);
    } else {
        $label = nama_hari($ts) . ', ' . tgl_panjang($ts);
    }
    $opsi_tanggal[date('Y-m-d', $ts)] =$label;
}

$opsi_waktu = ['16.00–17.00', '17.00–18.00', '18.00–19.00'];

/* ---------- Data barang & pemohon (Kosong/Bersih) ---------- */
$id_barang = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$barang = [
    'id'            => $id_barang,
    'nama_barang'   => 'Lampu Belajar Kayu',
    'foto'          => 'meja-belajar.png',
    'jumlah'        => 1,
    'kondisi'       => 'Baik',
    'lokasi'        => 'Denpasar',
    'id_pemberi'    => 0,
    'nama_pemberi'  => 'Githa',
    'jarak'         => '20 Km',
];
$pemohon = ['nama' => '', 'no_hp' => ''];

/* ---------- Layar sukses ---------- */
$tampil_sukses = isset($_GET['berhasil']) &&$_GET['berhasil'] == '1';

/* ---------- Proses form ---------- */
$error = [];$input = [
    'nama'    => $pemohon['nama'] ?? '',
    'no_hp'   => $pemohon['no_hp'] ?? '',
    'tanggal' => array_key_first($opsi_tanggal),
    'waktu'   => $opsi_waktu[1],
    'pesan'   => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajukan'])) {
    $input['nama']    = trim($_POST['nama'] ?? '');
    $input['no_hp']   = trim($_POST['no_hp'] ?? '');
    $input['tanggal'] =$_POST['tanggal'] ?? '';
    $input['waktu']   =$_POST['waktu'] ?? '';
    $input['pesan']   = trim($_POST['pesan'] ?? '');

    if ($input['nama'] === '') {$error[] = "Nama lengkap wajib diisi.";
    }
    if (!preg_match('/^\+?[0-9\s\-]{9,20}$/', $input['no_hp'])) {$error[] = "Nomor WhatsApp tidak valid.";
    }
    if (!isset($opsi_tanggal[$input['tanggal']])) {$error[] = "Tanggal pengambilan tidak valid.";
    }
    if (!in_array($input['waktu'], $opsi_waktu, true)) {$error[] = "Pilih waktu pengambilan.";
    }
    if (empty($_POST['setuju'])) {$error[] = "Kamu perlu menyetujui pernyataan kehadiran.";
    }

    if (empty($error)) {
        header("Location: ajukan-permintaan.php?id=" . $id_barang . "&berhasil=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Permintaan - ShareSpace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100">

<?php if ($tampil_sukses): ?>
    <!-- ================= LAYAR SUKSES ================= -->
    <div class="max-w-md mx-auto min-h-screen bg-slate-50 flex flex-col items-center justify-center px-6 text-center">
        <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-center">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
        </div>
        <p class="mt-6 text-slate-500">Permintaan berhasil diajukan!</p>
        <a href="profil.php" class="mt-6 text-sm font-semibold text-blue-600 hover:underline">Lihat permintaanku</a>
    </div>
    <script>
        setTimeout(function () { window.location.href = 'profil.php'; }, 3500);
    </script>

<?php else: ?>
    <!-- ================= FORM AJUKAN PERMINTAAN ================= -->
    <form method="POST" class="max-w-md mx-auto min-h-screen bg-slate-50 relative pb-40">

        <!-- Header -->
        <div class="flex items-center gap-3 px-5 pt-6 pb-4">
            <a href="detail_barang.php?id=<?= (int) $barang['id']; ?>" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-lg font-bold text-slate-800">Ajukan permintaan</h1>
        </div>

        <div class="px-5 space-y-5">

            <?php if (!empty($error)): ?>
                <div class="bg-red-50 text-red-700 text-xs rounded-xl p-3 space-y-1">
                    <?php foreach ($error as$e): ?>
                        <p>• <?= htmlspecialchars($e); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Banner -->
            <div class="bg-blue-50 rounded-xl px-4 py-3 flex items-start gap-3">
                <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="8" width="18" height="4" rx="1"/><path d="M12 8v13M19 12v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7M7.5 8a2.5 2.5 0 010-5C11 3 12 8 12 8s1-5 4.5-5a2.5 2.5 0 010 5"/></svg>
                <div>
                    <p class="text-xs font-semibold text-blue-700">Berbagi, bukan berbelanja.</p>
                    <p class="text-[11px] text-blue-600">Barang ini gratis. Tidak ada pembayaran apa pun.</p>
                </div>
            </div>

            <!-- Barang yang kamu minta -->
            <section>
                <h2 class="font-bold text-slate-800 mb-3">Barang yang kamu minta</h2>
                <div class="bg-white rounded-2xl border border-slate-200 p-3">
                    <div class="flex gap-3 pb-3 border-b border-slate-100">
                        <img src="<?= htmlspecialchars(foto_url($barang['foto'])); ?>" alt="<?= htmlspecialchars($barang['nama_barang']); ?>" class="w-16 h-16 rounded-lg object-cover">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800"><?= htmlspecialchars($barang['nama_barang']); ?></h3>
                            <p class="text-[11px] text-slate-400 mt-0.5"><?= (int) $barang['jumlah']; ?> unit • <?= htmlspecialchars($barang['kondisi']); ?></p>
                            <p class="text-xs font-semibold text-blue-600 mt-1">Gratis</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold flex items-center justify-center"><?= htmlspecialchars(inisial($barang['nama_pemberi'])); ?></span>
                            <span class="text-xs font-semibold text-slate-700"><?= htmlspecialchars($barang['nama_pemberi']); ?></span>
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.5 2.5 4.5-5"/></svg>
                        </div>
                        <?php if (!empty($barang['jarak'])): ?>
                            <span class="text-[11px] text-slate-400"><?= htmlspecialchars($barang['jarak']); ?> km darimu</span>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- Informasi pemohon -->
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold text-slate-800">Informasi pemohon</h2>
                    <span class="text-[11px] text-slate-400">Dari profilmu</span>
                </div>

                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama lengkap</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($input['nama']); ?>" required
                       class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500">

                <label class="block text-[11px] font-semibold text-slate-700 mt-3 mb-1">Nomor WhatsApp</label>
                <input type="tel" name="no_hp" value="<?= htmlspecialchars($input['no_hp']); ?>" required
                       class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500">
                <p class="text-[10px] text-slate-400 mt-1.5">Hanya dibagikan ke pemberi setelah permintaan disetujui.</p>
            </section>

<!-- Rencana pengambilan -->
            <section>
                <h2 class="font-bold text-slate-800 mb-3">Rencana pengambilan</h2>

                <div class="bg-white rounded-xl border border-slate-200 p-3 flex gap-3">
                    <svg class="w-4 h-4 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s7-7 7-12a7 7 0 00-14 0c0 5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    <div>
                        <p class="text-xs font-semibold text-slate-800"><?= htmlspecialchars($barang['lokasi']); ?></p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Alamat lengkap tersedia setelah permintaan disetujui. Ambil langsung, tanpa ongkir.</p>
                    </div>
                </div>

                <label class="block text-[11px] font-semibold text-slate-700 mt-4 mb-1">Tanggal pengambilan</label>
                <div class="relative">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                    <select name="tanggal" class="w-full appearance-none bg-white border border-slate-200 rounded-lg pl-9 pr-9 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500">
                        <?php foreach ($opsi_tanggal as $nilai => $label): ?>
                            <option value="<?= $nilai; ?>" <?= $input['tanggal'] === $nilai ? 'selected' : ''; ?>><?= htmlspecialchars($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <svg class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
                </div>

                <label class="block text-[11px] font-semibold text-slate-700 mt-4 mb-1.5">Pilih waktu</label>
                <div class="grid grid-cols-3 gap-2">
                    <?php foreach ($opsi_waktu as $w): ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="waktu" value="<?= htmlspecialchars($w); ?>" class="peer sr-only" <?= $input['waktu'] === $w ? 'checked' : ''; ?>>
                            <span class="block text-center text-[11px] py-2.5 rounded-lg border border-slate-200 bg-white text-slate-600 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-checked:font-semibold"><?= htmlspecialchars($w); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <label class="block text-[11px] font-semibold text-slate-700 mt-4 mb-1">Pesan untuk pemberi (opsional)</label>
                <textarea name="pesan" rows="3" maxlength="300" placeholder="Tulis pesan singkat untuk pemberi..."
                          class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2.5 text-xs text-slate-700 focus:outline-none focus:border-blue-500"><?= htmlspecialchars($input['pesan']); ?></textarea>

                <label class="flex items-start gap-2.5 mt-3 cursor-pointer">
                    <input type="checkbox" name="setuju" value="1" required class="mt-0.5 w-4 h-4 rounded accent-blue-600" <?= !empty($_POST['setuju']) ? 'checked' : ''; ?>>
                    <span class="text-[11px] text-slate-500">Saya akan datang sesuai jadwal dan mengabari pemberi jika ada perubahan.</span>
                </label>
            </section>
        </div>

        <!-- Tombol bawah -->
        <div class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md bg-white border-t border-slate-200 px-5 pt-3 pb-5">
            <button type="submit" name="ajukan" class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                Ajukan permintaan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </button>
            <p class="text-center text-[10px] text-slate-400 mt-2">Pemberi akan mengonfirmasi melalui chat ShareSpace.</p>
        </div>
    </form>
<?php endif; ?>

</body>
</html>
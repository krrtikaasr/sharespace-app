<?php
/* ============================================================
   laporan-fungsi.php  -  fungsi bantu halaman laporan (admin)
   Dipakai oleh kelola-laporan.php dan detail-laporan.php
   ============================================================ */

function kode_laporan($id) {
    return 'RPT-' . str_pad((string) (int) $id, 3, '0', STR_PAD_LEFT);
}

// data contoh untuk mode preview
function data_contoh_laporan() {
    $barang   = ['Meja Belajar', 'Beras 5 kg', 'Buku Pelajaran', 'Pakaian Layak Pakai', 'Kursi Plastik', 'Kipas Angin', 'Lampu Belajar', 'Selimut Tebal', 'Tas Ransel', 'Rak Buku'];
    $kategori = ['Perabot', 'Makanan', 'Buku', 'Pakaian', 'Perabot', 'Elektronik', 'Elektronik', 'Perabot', 'Pakaian', 'Perabot'];
    $donatur  = ['Made Wijaya', 'Ni Luh Putri', 'Komang Ayu', 'Kadek Arya', 'Nadya Prameswari'];
    $peminta  = ['Rani Putri', 'Putu Dharma', 'Ayu Lestari', 'Wayan Sudira', 'Gede Pratama'];
    $lokasi   = ['Denpasar', 'Badung', 'Gianyar', 'Tabanan', 'Denpasar'];
    $status   = ['terverifikasi', 'terverifikasi', 'ditolak', 'menunggu', 'menunggu', 'terverifikasi', 'ditolak', 'menunggu', 'terverifikasi', 'menunggu'];
    $hasil    = [];
    for ($i = 1; $i <= 23; $i++) {
        $k = ($i - 1) % 10;
        $p = $peminta[($i - 1) % count($peminta)];
        $hasil[] = [
            'id'            => $i,
            'nama'          => $barang[$k],
            'kategori'      => $kategori[$k],
            'donatur'       => $donatur[($i - 1) % count($donatur)],
            'peminta'       => $p,
            'peminta_email' => strtolower(str_replace(' ', '.', $p)) . '@gmail.com',
            'lokasi'        => $lokasi[($i - 1) % count($lokasi)],
            'created_at'    => date('Y-m-d', strtotime('2026-06-23 +' . ($i * 2) . ' day')),
            'status'        => $status[$k],
            'deskripsi'     => $barang[$k] . ' dalam kondisi baik, baru dipakai 1x.',
        ];
    }
    return $hasil;
}

$badge_laporan = [
    'menunggu'      => ['Menunggu Verifikasi', 'bg-amber-100 text-amber-700'],
    'terverifikasi' => ['Terverifikasi',       'bg-green-100 text-green-700'],
    'ditolak'       => ['Ditolak',             'bg-red-100 text-red-600'],
];
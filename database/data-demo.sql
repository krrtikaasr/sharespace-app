-- Data demo (opsional). Butuh MINIMAL 2 akun di tabel users (daftar lewat register.php).
-- Pemilik donasi = akun pertama, peminta = akun kedua.
USE sharespace_db;

SET @u1 := (SELECT id FROM users ORDER BY id ASC LIMIT 1);
SET @u2 := (SELECT id FROM users ORDER BY id ASC LIMIT 1 OFFSET 1);

INSERT INTO donations (user_id, nama_barang, jumlah, foto, status, kategori, kondisi, lokasi, deskripsi, created_at) VALUES
 (@u1, 'Lampu Belajar', 4, 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=300&q=80',
  'verified', 'Perabot', 'Baik', 'Denpasar, Bali', 'Lampu belajar masih menyala normal.', NOW()),
 (@u1, 'Mainan Kayu', 3, 'assets/images/wood.jpg',
  'verified', 'Mainan Anak', 'Baik, layak pakai', 'Denpasar, Bali', 'Set mainan balok kayu yang masih bagus dan bersih.', NOW() - INTERVAL 4 HOUR);

INSERT INTO requests (donation_id, requester_id, pesan, jarak_km)
SELECT d.id, @u2, 'Halo kak, saya otw ke lokasi yaa', 0.8
  FROM donations d WHERE d.nama_barang = 'Lampu Belajar' AND d.user_id = @u1 AND @u2 IS NOT NULL LIMIT 1;

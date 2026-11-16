-- =====================================================================
-- ShareSpace - skema database final (aman dijalankan berulang kali)
-- Jalankan di phpMyAdmin -> tab SQL. Tidak menghapus data yang sudah ada.
-- =====================================================================
CREATE DATABASE IF NOT EXISTS sharespace_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sharespace_db;

-- 1) users: sudah dibuat kelompok. IF NOT EXISTS = dilewati bila tabel sudah ada.
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  no_hp VARCHAR(20) NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'user',
  foto VARCHAR(255) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- tambah kolom foto di users hanya jika belum ada
SET @ada := (SELECT COUNT(*) FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'foto');
SET @sql := IF(@ada = 0, 'ALTER TABLE users ADD COLUMN foto VARCHAR(255) NULL', 'SELECT 1');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

-- 2) donations: barang yang didonasikan
CREATE TABLE IF NOT EXISTS donations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  nama_barang VARCHAR(150) NOT NULL,
  jumlah INT NOT NULL DEFAULT 1,
  foto VARCHAR(255) NULL,
  status ENUM('pending','verified','atur_jadwal') NOT NULL DEFAULT 'pending',
  kategori VARCHAR(80) NULL,
  kondisi VARCHAR(80) NULL,
  lokasi VARCHAR(150) NULL,
  deskripsi TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_donations_user (user_id),
  INDEX idx_donations_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) requests: permintaan atas suatu donasi (satu orang hanya boleh sekali per barang)
CREATE TABLE IF NOT EXISTS requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  donation_id INT NOT NULL,
  requester_id INT NOT NULL,
  pesan TEXT NULL,
  jarak_km DECIMAL(5,1) NULL,
  status ENUM('menunggu','diterima','ditolak') NOT NULL DEFAULT 'menunggu',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_request (donation_id, requester_id),
  INDEX idx_requests_requester (requester_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) notifications: untuk lonceng notifikasi
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  judul VARCHAR(200) NOT NULL,
  deskripsi VARCHAR(255) NULL,
  link VARCHAR(255) NULL,
  dibaca TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_notif_user (user_id, dibaca)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

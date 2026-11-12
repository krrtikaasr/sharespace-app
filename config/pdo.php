<?php
/**
 * Koneksi PDO untuk halaman status donasi.
 * Memakai kredensial dari config/database.php milik kelompok
 * ($host, $user, $pass, $db), jadi file asli tidak perlu diubah.
 */
require_once __DIR__ . '/database.php';

$pdo = null;
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    $pdo = null;
}
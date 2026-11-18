<?php
$host    = "localhost";
$user    = "root";
$pass    = "admin147*Kar"; // Sesuaikan dengan password MySQL/Laragon/XAMPP kalian (biasanya "" atau "password")
$db_name = "sharespace_db";

// 1. Koneksi MySQLi
$conn = mysqli_connect($host,$user, $pass,$db_name);
if (!$conn) {
    die("Koneksi MySQLi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// 2. Koneksi PDO
try {
    $db = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $user, $pass, [ PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, ]);$pdo = $db; // Alias agar file yang memanggil$pdo tetap berfungsi
} catch (PDOException $e) {
    die("Koneksi PDO gagal: " . $e->getMessage());
}
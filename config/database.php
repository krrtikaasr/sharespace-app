<?php
$host = "localhost";
$user = "root";
$pass = "admin147*Kar";
$db   = "sharespace_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
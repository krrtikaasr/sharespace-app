<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function csrf_field() {
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
  return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}
function csrf_ok() {
  return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

// tujuan setelah login, sesuai role
function arah_role($role) { return $role === 'admin' ? 'admin/beranda-admin.php' : 'beranda.php'; }

// pakai di halaman yang wajib login
function wajib_login($ke = 'masuk.php') {
  if (empty($_SESSION['user'])) { header("Location: $ke"); exit; }
}
// pakai di halaman admin (taruh di folder admin/)
function wajib_admin() {
  wajib_login('../masuk.php');
  if ($_SESSION['user']['role'] !== 'admin') { header('Location: ../beranda.php'); exit; }
}


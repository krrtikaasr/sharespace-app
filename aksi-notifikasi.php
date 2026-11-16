<?php
/** Menandai semua notifikasi pengguna sebagai dibaca (dipanggil lewat fetch POST). */
require_once __DIR__ . '/config/pdo.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');
$userId = current_user_id();
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId && $pdo && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    try {
        $pdo->prepare('UPDATE notifications SET dibaca = 1 WHERE user_id = ?')->execute([$userId]);
        $ok = true;
    } catch (Throwable $e) {
        error_log($e->getMessage());
    }
}
echo json_encode(['ok' => $ok]);

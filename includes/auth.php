<?php
/** Helper login & keamanan form. */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function require_login(): int {
    $id = current_user_id();
    if (!$id) {
        header('Location: login.php');
        exit;
    }
    return $id;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }
}

function tambah_notifikasi(PDO $pdo, int $userId, string $judul, ?string $deskripsi = null, ?string $link = null): void {
    $pdo->prepare('INSERT INTO notifications (user_id, judul, deskripsi, link) VALUES (?, ?, ?, ?)')
        ->execute([$userId, $judul, $deskripsi, $link]);
}

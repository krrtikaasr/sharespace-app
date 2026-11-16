<?php
session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: status-donasi.php');
} else {
    header('Location: login.php');
}
exit;
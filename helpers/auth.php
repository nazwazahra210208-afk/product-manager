<?php
session_start();

// Fungsi untuk mengunci halaman agar wajib login
function require_login() {
    if (!isset($_SESSION['user'])) {
        header("Location: /inventory-app/public/login.php");
        exit;
    }
}

// Fungsi untuk mengunci halaman agar HANYA bisa dibuka oleh Admin
function require_admin() {
    require_login();
    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        exit('Forbidden: Anda tidak memiliki akses ke halaman ini.');
    }
}
?>
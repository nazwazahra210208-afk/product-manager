<?php
require_once '../../config/db.php';
require_once '../../helpers/auth.php';

// Kunci halaman
require_login();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        // Coba hapus produk
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        
        header("Location: index.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        // Error 23000 (Integrity Constraint Violation) berarti produk ini terikat dengan tabel stock_movements
        if ($e->getCode() == 23000) {
            echo "<div style='font-family: Arial; padding: 20px; text-align: center; margin-top: 50px;'>";
            echo "<h2 style='color: red;'>Akses Ditolak!</h2>";
            echo "<p>Produk ini tidak bisa dihapus karena sudah memiliki histori Transaksi Stok.</p>";
            echo "<a href='index.php' style='padding: 10px 15px; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px;'>Kembali ke Daftar Produk</a>";
            echo "</div>";
            exit;
        } else {
            die("Gagal menghapus produk: " . $e->getMessage());
        }
    }
} else {
    header("Location: index.php");
    exit;
}
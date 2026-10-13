<?php
require_once '../config/db.php';

$password = 'admin123';
// Membuat hash baru yang valid untuk 'admin123'
$hash = password_hash($password, PASSWORD_DEFAULT); 

try {
    // Kita gunakan UPDATE untuk menimpa password yang lama di database
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE username = 'admin'");
    $stmt->execute(['hash' => $hash]);
    
    echo "<h3 style='color: green;'>Password admin berhasil di-reset ke: admin123!</h3>";
    echo "<a href='login.php'>Klik di sini untuk kembali ke halaman Login</a>";
} catch (PDOException $e) {
    echo "Gagal me-reset: " . $e->getMessage();
}
?>
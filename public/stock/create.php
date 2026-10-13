<?php
require_once '../../config/db.php';
require_once '../../helpers/auth.php';

require_login();

// Ambil daftar produk untuk dropdown
$stmt = $pdo->query("SELECT id, sku, name, stock FROM products ORDER BY name ASC");
$products = $stmt->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $type = $_POST['type'] ?? '';
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    // Validasi input dasar
    if (!$product_id || !in_array($type, ['IN', 'OUT']) || $quantity === false || $quantity <= 0) {
        $error = "Input tidak valid. Pastikan jumlah lebih dari 0.";
    } else {
        try {
            // MULAI TRANSAKSI DATABASE (Penting!)
            $pdo->beginTransaction();

            // 1. Kunci baris produk ini sementara (FOR UPDATE) agar tidak bentrok jika diakses bersamaan
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = :id FOR UPDATE");
            $stmt->execute(['id' => $product_id]);
            $product = $stmt->fetch();

            if (!$product) throw new Exception("Produk tidak ditemukan.");

            // 2. Cek Aturan Bisnis: Barang Keluar tidak boleh melebihi stok!
            if ($type === 'OUT' && $product['stock'] < $quantity) {
                throw new Exception("Gagal! Stok tidak mencukupi. Sisa stok saat ini: " . $product['stock']);
            }

            // 3. Update angka stok di tabel products
            if ($type === 'IN') {
                $sql = "UPDATE products SET stock = stock + :qty WHERE id = :id";
            } else {
                $sql = "UPDATE products SET stock = stock - :qty WHERE id = :id";
            }
            $updateStmt = $pdo->prepare($sql);
            $updateStmt->execute(['qty' => $quantity, 'id' => $product_id]);

            // 4. Catat riwayatnya di tabel stock_movements
            $insertStmt = $pdo->prepare("INSERT INTO stock_movements (product_id, type, quantity) VALUES (:id, :type, :qty)");
            $insertStmt->execute(['id' => $product_id, 'type' => $type, 'qty' => $quantity]);

            // SIMPAN SEMUA PERUBAHAN
            $pdo->commit();
            $success = "Transaksi berhasil! Stok telah diperbarui.";

            // Refresh data produk agar tampilan dropdown update
            $stmt = $pdo->query("SELECT id, sku, name, stock FROM products ORDER BY name ASC");
            $products = $stmt->fetchAll();

        } catch (Exception $e) {
            // BATALKAN SEMUA jika ada 1 langkah saja yang gagal
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Stok - Luxury Inventory</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;600&display=swap');

        body { font-family: 'Montserrat', sans-serif; background: #ffffff; color: #333; padding: 40px; }
        
        /* Link Kembali */
        .back-link { text-decoration: none; color: #111; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 30px; display: inline-block; border-bottom: 1px solid #111; padding-bottom: 3px; transition: color 0.3s, border-color 0.3s; }
        .back-link:hover { color: #d4af37; border-color: #d4af37; }
        
        /* Judul Halaman */
        h2 { font-family: 'Playfair Display', serif; font-size: 28px; text-transform: uppercase; letter-spacing: 2px; color: #111; border-bottom: 2px solid #d4af37; padding-bottom: 10px; margin-top: 0; margin-bottom: 35px; display: inline-block; }

        /* Kotak Form */
        .form-container { background: #ffffff; padding: 40px; border: 1px solid #eaeaea; border-top: 4px solid #111; max-width: 500px; box-shadow: 0 15px 40px rgba(0,0,0,0.03); }
        
        /* Label dan Input Form */
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 10px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: #111; }
        select, input[type="number"] { width: 100%; padding: 14px 15px; border: 1px solid #ddd; background: #fcfcfc; font-family: 'Montserrat', sans-serif; font-size: 13px; outline: none; transition: all 0.3s; box-sizing: border-box; }
        select:focus, input[type="number"]:focus { border-color: #111; background: #fff; box-shadow: 0 0 0 1px #111; }
        
        /* Tombol Simpan */
        .btn-submit { background: #111; color: #d4af37; padding: 16px 30px; border: none; text-transform: uppercase; font-size: 12px; font-weight: 600; letter-spacing: 2px; cursor: pointer; transition: all 0.3s ease; width: 100%; margin-top: 10px; }
        .btn-submit:hover { background: #d4af37; color: #111; }
        
        /* Notifikasi Error jika ada */
        .alert { padding: 15px; margin-bottom: 25px; font-size: 13px; border-left: 4px solid #8b0000; background: #fdf5f5; color: #8b0000; max-width: 500px; }
    </style>
</head>
<body>

    <a href="../dashboard.php" class="back-link">← Kembali ke Dashboard</a>
    <br>
    <h2>Form Transaksi Stok</h2>

    <!-- Menampilkan pesan error jika PHP mendeteksi kesalahan -->
    <?php if (isset($error)): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <label>Pilih Produk</label>
                <select name="product_id" required>
                    <option value="">-- Pilih Produk --</option>
                    <!-- Looping data produk dari PHP -->
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>">
                            <?= htmlspecialchars($p['sku'] . ' - ' . $p['name']) ?> 
                            (Sisa Stok: <?= $p['stock'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jenis Transaksi</label>
                <select name="type" required>
                    <option value="IN">Barang Masuk (Tambah Stok)</option>
                    <option value="OUT">Barang Keluar (Kurangi Stok)</option>
                </select>
            </div>

            <div class="form-group">
                <label>Jumlah (Qty)</label>
                <input type="number" name="quantity" min="1" placeholder="Contoh: 10" required>
            </div>

            <button type="submit" class="btn-submit">Simpan Transaksi</button>
        </form>
    </div>

</body>
</html>
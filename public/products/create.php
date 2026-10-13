<?php
require_once '../../config/db.php';
require_once '../../helpers/auth.php';
require_login();

// Mengambil data kategori untuk pilihan dropdown
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sku = trim($_POST['sku']);
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $minimum_stock = $_POST['minimum_stock'];

    try {
        $sql = "INSERT INTO products (category_id, sku, name, price, stock, minimum_stock) 
                VALUES (:category_id, :sku, :name, :price, :stock, :minimum_stock)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':category_id' => $category_id,
            ':sku' => $sku,
            ':name' => $name,
            ':price' => $price,
            ':stock' => $stock,
            ':minimum_stock' => $minimum_stock
        ]);
        // Kembali ke halaman produk jika sukses
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk - Luxury Inventory</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;600&display=swap');

        body { font-family: 'Montserrat', sans-serif; background: #faf9f6; color: #333; padding: 40px; }
        
        .back-link { text-decoration: none; color: #111; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 30px; display: inline-block; border-bottom: 1px solid #111; padding-bottom: 3px; transition: all 0.3s; }
        .back-link:hover { color: #d4af37; border-color: #d4af37; }
        
        h2 { font-family: 'Playfair Display', serif; font-size: 28px; text-transform: uppercase; letter-spacing: 2px; color: #111; border-bottom: 2px solid #d4af37; padding-bottom: 10px; margin-top: 0; margin-bottom: 35px; display: inline-block; }

        .form-container { background: #ffffff; padding: 40px; border: 1px solid #eaeaea; border-top: 4px solid #111; max-width: 500px; box-shadow: 0 15px 40px rgba(0,0,0,0.03); }
        
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 10px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; color: #111; }
        select, input[type="text"], input[type="number"] { width: 100%; padding: 14px 15px; border: 1px solid #ddd; background: #fcfcfc; font-family: 'Montserrat', sans-serif; font-size: 13px; outline: none; transition: all 0.3s; box-sizing: border-box; }
        select:focus, input[type="text"]:focus, input[type="number"]:focus { border-color: #111; background: #fff; box-shadow: 0 0 0 1px #111; }
        
        .btn-submit { background: #111; color: #d4af37; padding: 16px 30px; border: none; text-transform: uppercase; font-size: 12px; font-weight: 600; letter-spacing: 2px; cursor: pointer; transition: all 0.3s ease; width: 100%; margin-top: 10px; }
        .btn-submit:hover { background: #d4af37; color: #111; }
        
        .alert { padding: 15px; margin-bottom: 25px; font-size: 13px; border-left: 4px solid #8b0000; background: #fdf5f5; color: #8b0000; max-width: 500px; }
    </style>
</head>
<body>

    <a href="index.php" class="back-link">← Kembali ke Daftar Produk</a>
    <br>
    <h2>Tambah Produk Baru</h2>

    <?php if (isset($error)): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <label>Kode SKU</label>
                <input type="text" name="sku" placeholder="Contoh: TAS-001" required>
            </div>

            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="name" placeholder="Contoh: Louis Vuitton Neverfull" required>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="category_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="price" min="0" required>
            </div>

            <div class="form-group">
                <label>Stok Awal</label>
                <input type="number" name="stock" min="0" required>
            </div>

            <div class="form-group">
                <label>Batas Stok Minimum</label>
                <input type="number" name="minimum_stock" min="1" value="5" required>
            </div>

            <button type="submit" class="btn-submit">Simpan Produk</button>
        </form>
    </div>

</body>
</html>
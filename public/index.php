<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :q1 OR category LIKE :q2 ORDER BY id DESC");
    $stmt->execute([
        'q1' => "%$q%",
        'q2' => "%$q%"
    ]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}

$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Product Manager - Luxury Gallery</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <!-- Header Luxury Store -->
        <header class="branded-header">
            <div class="header-text">
                <h1>✨ Luxury Brand Gallery</h1>
                <p>Sistem Manajemen Inventaris Barang Luxury & Branded</p>
            </div>
            <a href="create.php" class="btn btn-luxury">+ Tambah Produk</a>
        </header>

        <!-- Notifikasi Status -->
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'created'): ?>
                <div class="alert alert-success">Produk berhasil ditambahkan!</div>
            <?php elseif ($_GET['status'] === 'updated'): ?>
                <div class="alert alert-success">Produk berhasil diperbarui!</div>
            <?php elseif ($_GET['status'] === 'deleted'): ?>
                <div class="alert alert-success">Produk berhasil dihapus!</div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Form Pencarian -->
        <form method="GET" class="search-form">
            <input type="text" name="q" placeholder="Cari nama atau kategori produk..." value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-secondary">Cari</button>
            <?php if ($q !== ''): ?>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            <?php endif; ?>
        </form>

        <!-- Container Grid Flexbox -->
        <div class="products-grid">
            <?php if (empty($products)): ?>
                <p>Tidak ada produk ditemukan.</p>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                    <div class="card">
                        <div>
                            <span class="card-tag"><?= htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8') ?></span>
                            <h2 class="card-title"><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <div class="card-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                            <div class="card-stock">Stok: <?= (int)$p['stock'] ?></div>
                        </div>
                        <div class="actions">
                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-warning" style="flex: 1; text-align: center;">Edit</a>
                            
                            <!-- Form Delete dengan POST + CSRF -->
                            <form method="POST" action="delete.php" style="flex: 1;" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                                <button type="submit" class="btn btn-danger" style="width: 100%;">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
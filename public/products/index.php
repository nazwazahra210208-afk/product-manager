<?php
require_once '../../config/db.php';
require_once '../../helpers/auth.php';

// Kunci halaman ini!
require_login();

// 1. Fitur Pencarian (Search)
$search = $_GET['search'] ?? '';

// 2. Fitur Pagination (Pembagian Halaman)
$limit = 5; // Tampilkan maksimal 5 produk per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 3. Query dengan relasi JOIN ke tabel kategori
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.name LIKE :search1 OR p.sku LIKE :search2 
        ORDER BY p.id DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
// Perbaikan: gunakan nama parameter yang unik
$stmt->bindValue(':search1', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// Menghitung total halaman untuk Pagination
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name LIKE :search1 OR sku LIKE :search2");
$countStmt->bindValue(':search1', "%$search%", PDO::PARAM_STR);
$countStmt->bindValue(':search2', "%$search%", PDO::PARAM_STR);
$countStmt->execute();
$totalData = $countStmt->fetchColumn();
$totalPages = ceil($totalData / $limit);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk - Luxury Inventory</title>
    <style>
        /* Import Font Elegan dari Google */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;600&display=swap');

        /* Pengaturan Dasar */
        body { font-family: 'Montserrat', sans-serif; background: #faf9f6; color: #333; padding: 40px; margin: 0; }
        
        /* Navigasi / Link Kembali */
        .back-link { text-decoration: none; color: #111; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 25px; display: inline-block; border-bottom: 1px solid #111; padding-bottom: 3px; transition: all 0.3s; }
        .back-link:hover { color: #d4af37; border-color: #d4af37; }

        /* Header Area (Judul & Tombol Tambah) */
        .header-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; border-bottom: 2px solid #d4af37; padding-bottom: 15px; }
        h2 { font-family: 'Playfair Display', serif; font-size: 28px; text-transform: uppercase; letter-spacing: 3px; color: #111; margin: 0; }
        .btn-add { background: #111; color: #d4af37; padding: 12px 25px; text-decoration: none; text-transform: uppercase; font-size: 11px; font-weight: 600; letter-spacing: 2px; border: 1px solid #111; transition: all 0.3s ease; }
        .btn-add:hover { background: #d4af37; color: #111; border-color: #d4af37; }

        /* Area Pencarian Minimalis */
        .search-area { margin-bottom: 30px; display: flex; gap: 10px; }
        .search-input { width: 300px; padding: 12px 15px; border: 1px solid #eaeaea; border-bottom: 1px solid #111; background: #fcfcfc; font-family: 'Montserrat', sans-serif; font-size: 12px; outline: none; transition: border-color 0.3s; }
        .search-input:focus { border-bottom: 2px solid #d4af37; background: #fff; }
        .btn-search { background: transparent; color: #111; padding: 12px 20px; border: 1px solid #111; text-transform: uppercase; font-size: 11px; font-weight: 600; letter-spacing: 1.5px; cursor: pointer; transition: all 0.3s ease; }
        .btn-search:hover { background: #111; color: #fff; }

        /* Tabel Elegan */
        .table-produk { width: 100%; border-collapse: collapse; background: #ffffff; }
        .table-produk th, .table-produk td { padding: 20px 15px; border-bottom: 1px solid #eaeaea; text-align: left; font-size: 13px; }
        .table-produk th { background: #ffffff; color: #111; text-transform: uppercase; font-size: 11px; font-weight: 600; letter-spacing: 1.5px; border-bottom: 2px solid #111; }
        .table-produk tr:hover { background-color: #fcfcfc; }
        
        /* Kolom Aksi (Edit/Hapus) */
        .action-links a { text-decoration: none; text-transform: uppercase; font-size: 11px; letter-spacing: 1px; font-weight: 600; margin-right: 15px; transition: color 0.3s; }
        .link-edit { color: #111; border-bottom: 1px solid #111; }
        .link-edit:hover { color: #d4af37; border-color: #d4af37; }
        .link-delete { color: #8b0000; border-bottom: 1px solid #8b0000; }
        .link-delete:hover { color: #ff0000; border-color: #ff0000; }

        /* Peringatan Stok & Pagination */
        .low-stock { color: #8b0000; font-size: 11px; display: block; margin-top: 5px; font-weight: 600; text-transform: uppercase; }
        .pagination { margin-top: 30px; display: flex; gap: 10px; }
        .pagination a, .pagination span { padding: 10px 15px; text-decoration: none; color: #111; border: 1px solid #eaeaea; font-size: 12px; font-weight: 600; transition: all 0.3s; }
        .pagination a:hover { border-color: #111; }
        .pagination .active { background: #111; color: #d4af37; border-color: #111; }
    </style>
</head>
<body>

    <a href="../dashboard.php" class="back-link">← Kembali ke Dashboard</a>

    <div class="header-area">
        <h2>Daftar Produk</h2>
        <a href="create.php" class="btn-add">+ Tambah Produk Baru</a>
    </div>

    <form method="GET" class="search-area">
        <input type="text" name="search" class="search-input" placeholder="Cari nama atau kode SKU..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn-search">Cari</button>
    </form>

    <table class="table-produk">
        <thead>
            <tr>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['sku']) ?></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['category_name'] ?? 'Tanpa Kategori') ?></td>
                <td>Rp <?= number_format($p['price'], 0, ',', '.') ?></td>
                <td>
                    <strong><?= $p['stock'] ?></strong>
                    <?php if ($p['stock'] <= $p['minimum_stock']): ?>
                        <span class="low-stock">(Butuh Restock!)</span>
                    <?php endif; ?>
                </td>
                <td class="action-links">
                    <a href="edit.php?id=<?= $p['id'] ?>" class="link-edit">Edit</a>
                    <a href="delete.php?id=<?= $p['id'] ?>" class="link-delete" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</body>
</html>
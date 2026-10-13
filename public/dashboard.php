<?php
require_once '../config/db.php';
require_once '../helpers/auth.php';

// Kunci halaman ini! Hanya yang sudah login yang bisa masuk
require_login();

// Mengambil data pengguna dari session
$user = $_SESSION['user'];

// Query Laporan Dashboard (Menggunakan fungsi Agregat)
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$lowStock = $pdo->query("SELECT COUNT(*) FROM products WHERE stock <= minimum_stock")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Luxury Inventory</title>
    <style>
        /* Import Font Elegan dari Google */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Montserrat:wght@300;400;600;700&display=swap');

        /* Background & Text Utama */
        body {
            font-family: 'Montserrat', sans-serif;
            background: #faf9f6; /* Putih bersih */
            color: #333;
            margin: 0;
            padding: 30px;
        }

        /* Header Bar Mewah (Hitam & Emas) */
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #111111;
            color: #ffffff;
            padding: 25px 30px;
            border-bottom: 3px solid #d4af37; /* Garis emas bawah */
            margin-bottom: 40px;
        }
        .header-bar h2 {
            font-family: 'Playfair Display', serif;
            margin: 0 0 5px 0;
            font-size: 24px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #d4af37; /* Teks emas */
        }
        .header-bar small {
            font-weight: 300;
            letter-spacing: 1px;
            font-size: 12px;
            color: #ccc;
        }
        .header-bar a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.3s;
        }
        .header-bar a:hover {
            color: #d4af37;
        }

        /* Tombol Elegan */
        .action-buttons {
            margin-bottom: 40px;
        }
        .btn-primary {
            background: #111111;
            color: #d4af37;
            padding: 15px 30px;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 2px;
            border: 1px solid #111111;
            transition: all 0.3s ease;
            display: inline-block;
            margin-right: 15px;
        }
        .btn-primary:hover {
            background: #d4af37;
            color: #111111;
            border-color: #d4af37;
        }

        /* Widget / Kartu Info Minimalis */
        .dashboard-widgets {
            display: flex;
            gap: 30px;
        }
        .widget {
            background: #ffffff;
            padding: 35px;
            flex: 1;
            border: 1px solid #eaeaea;
            border-top: 4px solid #111111; /* Aksen atas hitam */
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); /* Bayangan sangat tipis */
        }
        .widget.alert {
            border-top: 4px solid #8b0000; /* Aksen atas merah gelap untuk peringatan */
        }
        .widget h3 {
            margin-top: 0;
            color: #111111;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 600;
        }
        .widget .number {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: bold;
            color: #d4af37; /* Angka warna emas */
            margin: 15px 0;
        }
        .widget small {
            color: #888;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>
    <div class="header-bar">
        <div>
            <h2>Inventory System</h2>
            <small>Selamat datang, <?= htmlspecialchars($user['username']) ?>! (Hak Akses: <?= htmlspecialchars($user['role']) ?>)</small>
        </div>
        <div>
            <a href="logout.php">Keluar (Logout)</a>
        </div>
    </div>

    <div class="action-buttons">
        <a href="products/index.php" class="btn-primary">Kelola Produk</a>
        <a href="stock/create.php" class="btn-primary">Transaksi Stok</a>
    </div>

    <div class="dashboard-widgets">
        <div class="widget">
            <h3>Total Jenis Produk</h3>
            <div class="number"><?= $totalProducts ?></div>
            <small>Jumlah varian item di dalam katalog</small>
        </div>
        <div class="widget alert">
            <h3>Peringatan Stok Menipis</h3>
            <!-- Jika ada stok menipis, warna angkanya jadi merah gelap, jika aman jadi emas -->
            <div class="number" style="<?= $lowStock > 0 ? 'color: #8b0000;' : '' ?>"><?= $lowStock ?></div>
            <small>Item yang membutuhkan restock segera</small>
        </div>
    </div>
</body>
</html>
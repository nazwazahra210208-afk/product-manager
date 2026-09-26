CREATE DATABASE IF NOT EXISTS store_db;
USE store_db;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  category VARCHAR(50) NOT NULL DEFAULT 'Umum',
  price DECIMAL(12,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal Barang-Barang Branded
INSERT INTO products (name, category, price, stock) VALUES
('Louis Vuitton Neverfull MM', 'Tas Mewah', 32500000.00, 5),
('Gucci GG Marmont Shoulder Bag', 'Tas Mewah', 28000000.00, 3),
('Rolex Submariner Date', 'Jam Tangan', 185000000.00, 2),
('Nike x Off-White Air Jordan 1', 'Sepatu Sneaker', 22500000.00, 4),
('Hermès Oran Sandals', 'Sepatu & Sandal', 11500000.00, 6),
('Balenciaga Triple S Sneakers', 'Sepatu Sneaker', 16000000.00, 8),
('Dior Saddle Bag Black', 'Tas Mewah', 65000000.00, 2)
ON DUPLICATE KEY UPDATE name=name;
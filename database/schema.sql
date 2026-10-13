CREATE DATABASE IF NOT EXISTS inventory_db;
USE inventory_db;

-- Tabel Users (Untuk fitur Login dan Role Admin/Staff)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'staff'
);

-- Tabel Kategori
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL
);

-- Tabel Produk (Tabel lama dimodifikasi agar berelasi dengan Kategori)
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 5,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON UPDATE CASCADE ON DELETE RESTRICT
);

-- Tabel Supplier
CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(150) NOT NULL
);

-- Tabel Relasi Many-to-Many (Produk & Supplier)
CREATE TABLE product_suppliers (
    product_id INT NOT NULL,
    supplier_id INT NOT NULL,
    supplier_price DECIMAL(12,2),
    PRIMARY KEY (product_id, supplier_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
);

-- Tabel Histori Keluar/Masuk Barang
CREATE TABLE stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- Masukkan akun Admin default untuk uji coba (Passwordnya: admin123)
INSERT INTO users (username, password_hash, role) 
VALUES ('admin', '$2y$10$eE0LOn/wD83zU/l0.TfA.egN39FpIh3p8eKByUj3fA89I66x4oO4i', 'admin');
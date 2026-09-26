# Mini Project: Product Manager (Barang Branded)

Aplikasi web manajemen produk (*Product Manager*) berbasis **PHP Native** dan **MySQL** yang memfasilitasi pengelolaan data barang-barang *branded* (seperti tas, jam tangan, dan sepatu mewah). 

Aplikasi ini dirancang sesuai standar modul praktikum Pemrograman Web Pertemuan 3 dengan menerapkan arsitektur *separation of concerns*, validasi server-side, pola PRG (*Post-Redirect-Get*), serta proteksi keamanan dasar.

---

## 🚀 Fitur Utama & Keamanan

1. **Create (Tambah Produk):**
   * Form pengisian nama, kategori, harga, dan stok produk.
   * Validasi server-side (nama minimal 3 karakter, harga > 0, stok ≥ 0, dan nama produk harus unik).
   * Penerapan pola **Post-Redirect-Get (PRG)** untuk mencegah duplikasi data saat *refresh* halaman.

2. **Read (Tampil & Cari Produk):**
   * Tampilan daftar produk responsif berbentuk kartu (*card grid*) menggunakan **CSS Box Model & Flexbox**.
   * Output escaping menggunakan `htmlspecialchars()` untuk memproteksi aplikasi dari serangan **XSS (Cross-Site Scripting)**.
   * **Fitur Pencarian (GET):** Mencari produk berdasarkan nama atau kategori secara aman.

3. **Update (Edit Produk):**
   * Pengubahan data produk berdasarkan ID yang tervalidasi.

4. **Delete (Hapus Produk):**
   * Penghapusan produk menggunakan metode `POST` yang dilengkapi verifikasi **Token CSRF** (*Cross-Site Request Forgery*) dan konfirmasi dialog modal.

5. **Database & Interaksi Data:**
   * Menggunakan **PDO (PHP Data Objects)** dengan *Prepared Statements* untuk mencegah serangan **SQL Injection**.

---

## 📁 Struktur Proyek

```text
product-manager/
├── config/
│   └── db.php          # Konfigurasi koneksi PDO MySQL (Di luar public)
├── database/
│   └── store_db.sql    # Skema tabel & data dummy produk branded
├── public/             # Document Root (Pintu masuk akses browser)
│   ├── assets/
│   │   └── style.css   # Styling UI (Box Model & Flexbox Layout)
│   ├── create.php      # Form & proses tambah produk
│   ├── delete.php      # Proses hapus produk + token CSRF
│   ├── edit.php        # Form & proses update produk
│   └── index.php       # Halaman utama (Daftar produk & pencarian)
└── README.md           # Panduan & dokumentasi proyek
-- Database: db_pemesanan_makanan
CREATE DATABASE IF NOT EXISTS `db_pemesanan_makanan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_pemesanan_makanan`;

-- 1. Tabel Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL,
  `role` ENUM('admin', 'pelanggan') DEFAULT 'pelanggan',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel Categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(50) NOT NULL,
  `slug` VARCHAR(50) NOT NULL,
  `icon` VARCHAR(50) DEFAULT 'fa-utensils',
  `urutan` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel Menus
CREATE TABLE IF NOT EXISTS `menus` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `nama_menu` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT,
  `harga` INT NOT NULL,
  `stok` INT DEFAULT 50,
  `gambar` VARCHAR(255) DEFAULT '',
  `is_popular` TINYINT(1) DEFAULT 0,
  `is_promo` TINYINT(1) DEFAULT 0,
  `diskon_persen` INT DEFAULT 0,
  `status` ENUM('tersedia', 'habis') DEFAULT 'tersedia',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabel Orders
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_invoice` VARCHAR(50) UNIQUE NOT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `customer_address` TEXT NOT NULL,
  `patokan` VARCHAR(255) DEFAULT '',
  `delivery_notes` TEXT,
  `payment_method` ENUM('COD', 'QRIS', 'TRANSFER_BANK', 'EWALLET') DEFAULT 'COD',
  `payment_status` ENUM('belum_bayar', 'lunas') DEFAULT 'belum_bayar',
  `order_status` ENUM('menunggu', 'dimasak', 'diantar', 'selesai', 'dibatalkan') DEFAULT 'menunggu',
  `subtotal` INT NOT NULL DEFAULT 0,
  `ongkir` INT NOT NULL DEFAULT 10000,
  `total_bayar` INT NOT NULL DEFAULT 0,
  `driver_name` VARCHAR(100) DEFAULT 'Budi Santoso',
  `driver_phone` VARCHAR(20) DEFAULT '081234567890',
  `driver_plate` VARCHAR(20) DEFAULT 'B 4567 XYZ',
  `estimasi_waktu` INT DEFAULT 25,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabel Order Items
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `menu_id` INT NOT NULL,
  `nama_menu` VARCHAR(100) NOT NULL,
  `harga_satuan` INT NOT NULL,
  `qty` INT NOT NULL,
  `catatan` VARCHAR(255) DEFAULT '',
  `subtotal` INT NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabel Settings / Resto Profile
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` VARCHAR(50) PRIMARY KEY,
  `setting_value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEED DATA DEFAULT
-- Default Admin (password: admin123)
INSERT INTO `users` (`id`, `nama`, `email`, `password`, `no_hp`, `role`) VALUES
(1, 'Administrator Dapur', 'admin@dapurlezat.com', '$2y$12$j.DuubP7KEFQGvrEhvoAXuiNBKbLAT4HYW5AeF/nK4kNL4qNRnFTa', '08123456789', 'admin')
ON DUPLICATE KEY UPDATE `password`='$2y$12$j.DuubP7KEFQGvrEhvoAXuiNBKbLAT4HYW5AeF/nK4kNL4qNRnFTa';

-- Default Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('nama_resto', 'Dapur Lezat Express'),
('slogan', 'Pesan Cepat, Masak Hangat, Antar Cepat Sampai ke Mejamu!'),
('alamat_resto', 'Jl. Merdeka No. 45, Kebayoran Baru, Jakarta Selatan'),
('no_wa_resto', '628123456789'),
('ongkir_default', '10000'),
('jam_operasional', '10:00 - 22:00 WIB')
ON DUPLICATE KEY UPDATE `setting_key`=`setting_key`;

-- Default Categories
INSERT INTO `categories` (`id`, `nama_kategori`, `slug`, `icon`, `urutan`) VALUES
(1, 'Makanan Utama', 'makanan-utama', 'fa-bowl-rice', 1),
(2, 'Ayam & Bebek', 'ayam-bebek', 'fa-drumstick-bite', 2),
(3, 'Camilan & Snack', 'snack', 'fa-cookie-bite', 3),
(4, 'Minuman Segar', 'minuman', 'fa-glass-water', 4),
(5, 'Paket Hemat', 'paket-hemat', 'fa-box-open', 5)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Default Menus with High Quality images
INSERT INTO `menus` (`id`, `category_id`, `nama_menu`, `deskripsi`, `harga`, `stok`, `gambar`, `is_popular`, `is_promo`, `diskon_persen`, `status`) VALUES
(1, 1, 'Nasi Goreng Spesial Dapur Lezat', 'Nasi goreng harum bumbu rempah pilihan, dilengkapi telur mata sapi, sate ayam, acar, dan kerupuk udang.', 28000, 40, 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=600&q=80', 1, 0, 0, 'tersedia'),
(2, 2, 'Ayam Geprek Sambal Korek Super Pedas', 'Ayam goreng krispi juicy digeprek dengan sambal korek cabai rawit pedas nendang, disajikan dengan lalapan segar.', 24000, 35, 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?auto=format&fit=crop&w=600&q=80', 1, 1, 15, 'tersedia'),
(3, 1, 'Mie Goreng Seafood Komplit', 'Mie telur kenyal digoreng dengan cumi, udang segar, bakso ikan, telur orak-arik dan sayuran renyah.', 32000, 25, 'https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&w=600&q=80', 0, 0, 0, 'tersedia'),
(4, 2, 'Bebek Goreng Kremes Madura', 'Bebek ungkep bumbu rempah gurih digoreng garing, bertabur kremesan renyah dan sambal hitam khas Madura.', 38000, 20, 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?auto=format&fit=crop&w=600&q=80', 1, 0, 0, 'tersedia'),
(5, 3, 'Tahu Cabe Garam Crispy', 'Tahu sutra dipotong dadu, digoreng garing lalu ditumis dengan cincangan bawang putih, cabai rawit, dan garam gurih.', 16000, 50, 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80', 0, 0, 0, 'tersedia'),
(6, 3, 'Kentang Goreng Keju Bolognaise', 'French fries renyah disiram saus bolognaise daging sapi cincang dan lelehan keju mozarella panggang.', 22000, 30, 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80', 0, 1, 10, 'tersedia'),
(7, 4, 'Es Kopi Susu Gula Aren Senja', 'Espresso arabika blend creamy berpadu dengan sirup gula aren asli organik dan susu segar murni.', 18000, 60, 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=600&q=80', 1, 0, 0, 'tersedia'),
(8, 4, 'Es Teh Leci Mint Segar', 'Teh melati wangi dingin berpadu dengan sirup buah leci manis, buah leci utuh, dan daun mint.', 15000, 75, 'https://images.unsplash.com/photo-1556881286-fc6915169721?auto=format&fit=crop&w=600&q=80', 0, 0, 0, 'tersedia'),
(9, 5, 'Paket Kenyang 1 (Ayam Geprek + Nasi + Es Teh)', 'Paket lengkap hemat: Ayam Geprek Sambal Bawang + Nasi Putih Pulen + Es Teh Manis Segar.', 30000, 45, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?auto=format&fit=crop&w=600&q=80', 1, 1, 20, 'tersedia'),
(10, 5, 'Paket Bukber Berdua (2 Nasi Goreng + 2 Es Kopi)', 'Paket nikmat untuk berdua: 2 porsi Nasi Goreng Spesial + 2 Es Kopi Susu Aren.', 75000, 20, 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=600&q=80', 1, 0, 0, 'tersedia')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Dummy Sample Active Order for Tracking Demo
INSERT INTO `orders` (`id`, `no_invoice`, `customer_name`, `customer_phone`, `customer_address`, `patokan`, `delivery_notes`, `payment_method`, `payment_status`, `order_status`, `subtotal`, `ongkir`, `total_bayar`, `driver_name`, `driver_phone`, `driver_plate`, `estimasi_waktu`) VALUES
(1, 'INV-20261020-001', 'Ananda Putri', '081298765432', 'Apartemen Sudirman Tower A Lantai 12 No 1205', 'Depan Halte Busway', 'Jangan pakai sendok plastik, sambal dipisah ya mas kurir.', 'QRIS', 'lunas', 'diantar', 58000, 10000, 68000, 'Rian Hidayat', '085712349988', 'B 6890 PQR', 12)
ON DUPLICATE KEY UPDATE `id`=`id`;

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `nama_menu`, `harga_satuan`, `qty`, `catatan`, `subtotal`) VALUES
(1, 1, 1, 'Nasi Goreng Spesial Dapur Lezat', 28000, 1, 'Pedas sedang, telur ceplok setengah matang', 28000),
(2, 1, 9, 'Paket Kenyang 1 (Ayam Geprek + Nasi + Es Teh)', 30000, 1, 'Ayam paha atas, sambal dipisah', 30000)
ON DUPLICATE KEY UPDATE `id`=`id`;


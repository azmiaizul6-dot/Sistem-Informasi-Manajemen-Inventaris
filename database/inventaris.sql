-- ========================================
-- DATABASE SISTEM MANAJEMEN INVENTARIS
-- ========================================

CREATE DATABASE IF NOT EXISTS inventaris_db;
USE inventaris_db;

-- ========================================
-- TABLE USERS (Manajemen User & Login)
-- ========================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    nama_lengkap VARCHAR(150) NOT NULL,
    no_telepon VARCHAR(15),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- TABLE KATEGORI PRODUK
-- ========================================
CREATE TABLE kategori_produk (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- TABLE MASTER PRODUK
-- ========================================
CREATE TABLE produk (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_produk VARCHAR(50) NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT NOT NULL,
    harga DECIMAL(12, 2) NOT NULL,
    stok_total INT DEFAULT 0,
    stok_minimum INT DEFAULT 5,
    deskripsi TEXT,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori_produk(id)
);

-- ========================================
-- TABLE GUDANG
-- ========================================
CREATE TABLE gudang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_gudang VARCHAR(50) NOT NULL UNIQUE,
    nama_gudang VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    kota VARCHAR(50),
    provinsi VARCHAR(50),
    no_telepon VARCHAR(15),
    pic_nama VARCHAR(100),
    pic_telepon VARCHAR(15),
    kapasitas INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- TABLE STOK GUDANG
-- ========================================
CREATE TABLE stok_gudang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produk_id INT NOT NULL,
    gudang_id INT NOT NULL,
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_produk_gudang (produk_id, gudang_id),
    FOREIGN KEY (produk_id) REFERENCES produk(id),
    FOREIGN KEY (gudang_id) REFERENCES gudang(id)
);

-- ========================================
-- TABLE STOK MASUK (Barang Masuk)
-- ========================================
CREATE TABLE stok_masuk (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produk_id INT NOT NULL,
    gudang_id INT NOT NULL,
    jumlah INT NOT NULL,
    keterangan TEXT,
    no_referensi VARCHAR(100),
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES produk(id),
    FOREIGN KEY (gudang_id) REFERENCES gudang(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================================
-- TABLE STOK KELUAR (Barang Keluar)
-- ========================================
CREATE TABLE stok_keluar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produk_id INT NOT NULL,
    gudang_id INT NOT NULL,
    jumlah INT NOT NULL,
    tujuan VARCHAR(255),
    keterangan TEXT,
    no_referensi VARCHAR(100),
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES produk(id),
    FOREIGN KEY (gudang_id) REFERENCES gudang(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================================
-- TABLE TRANSFER STOK
-- ========================================
CREATE TABLE transfer_stok (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produk_id INT NOT NULL,
    gudang_asal_id INT NOT NULL,
    gudang_tujuan_id INT NOT NULL,
    jumlah INT NOT NULL,
    keterangan TEXT,
    status ENUM('pending', 'diproses', 'selesai', 'dibatalkan') DEFAULT 'pending',
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES produk(id),
    FOREIGN KEY (gudang_asal_id) REFERENCES gudang(id),
    FOREIGN KEY (gudang_tujuan_id) REFERENCES gudang(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================================
-- TABLE PENGAJUAN PEMINJAMAN
-- ========================================
CREATE TABLE pengajuan_peminjaman (
    id INT PRIMARY KEY AUTO_INCREMENT,
    no_pengajuan VARCHAR(100) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    produk_id INT NOT NULL,
    gudang_id INT NOT NULL,
    jumlah INT NOT NULL,
    tujuan_peminjaman TEXT,
    status ENUM('pending', 'disetujui', 'ditolak', 'dikembalikan') DEFAULT 'pending',
    tanggal_persetujuan DATETIME,
    disetujui_oleh INT,
    keterangan_penolakan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (produk_id) REFERENCES produk(id),
    FOREIGN KEY (gudang_id) REFERENCES gudang(id),
    FOREIGN KEY (disetujui_oleh) REFERENCES users(id)
);

-- ========================================
-- TABLE AUDIT TRAIL (Pencatatan Aktivitas)
-- ========================================
CREATE TABLE audit_trail (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    aksi VARCHAR(100) NOT NULL,
    modul VARCHAR(50),
    data_lama TEXT,
    data_baru TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ========================================
-- TABLE NOTIFIKASI STOK MINIMUM
-- ========================================
CREATE TABLE notifikasi_stok_minimum (
    id INT PRIMARY KEY AUTO_INCREMENT,
    produk_id INT NOT NULL,
    gudang_id INT,
    stok_saat_ini INT,
    stok_minimum INT,
    status ENUM('aktif', 'diatasi') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produk_id) REFERENCES produk(id),
    FOREIGN KEY (gudang_id) REFERENCES gudang(id)
);

-- ========================================
-- TABLE SETTINGS
-- ========================================
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_sistem VARCHAR(100) DEFAULT 'Sistem Manajemen Inventaris',
    deskripsi TEXT,
    logo VARCHAR(255),
    favicon VARCHAR(255),
    primary_color VARCHAR(7) DEFAULT '#3B82F6',
    secondary_color VARCHAR(7) DEFAULT '#10B981',
    dark_mode BOOLEAN DEFAULT FALSE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ========================================
-- DATA AWAL / SEED DATA
-- ========================================

-- Insert Users (Admin & User)
INSERT INTO users (username, password, email, role, nama_lengkap, no_telepon, status) VALUES
('admin', 'admin123', 'admin@inventaris.com', 'admin', 'Administrator', '081234567890', 'aktif'),
('user1', 'user123', 'user1@inventaris.com', 'user', 'Ahmad Rifki', '082345678901', 'aktif'),
('user2', 'user123', 'user2@inventaris.com', 'user', 'Siti Nurhaliza', '083456789012', 'aktif');

-- Insert Kategori Produk
INSERT INTO kategori_produk (nama_kategori, deskripsi) VALUES
('Elektronik', 'Produk elektronik dan gadget'),
('Furniture', 'Peralatan dan mebel kantor'),
('Alat Tulis', 'Alat tulis dan perlengkapan kantor'),
('Bahan Baku', 'Bahan baku produksi'),
('Spare Part', 'Suku cadang dan komponen');

-- Insert Gudang
INSERT INTO gudang (kode_gudang, nama_gudang, alamat, kota, provinsi, no_telepon, pic_nama, pic_telepon, kapasitas) VALUES
('GDG001', 'Gudang Pusat Jakarta', 'Jl. Merdeka No. 123, Jakarta Pusat', 'Jakarta', 'DKI Jakarta', '021-1234567', 'Budi Santoso', '081234567890', 10000),
('GDG002', 'Gudang Surabaya', 'Jl. Ahmad Yani No. 45, Surabaya', 'Surabaya', 'Jawa Timur', '031-9876543', 'Heri Wijaya', '082345678901', 5000),
('GDG003', 'Gudang Bandung', 'Jl. Diponegoro No. 78, Bandung', 'Bandung', 'Jawa Barat', '022-5555555', 'Rina Setia', '083456789012', 7000);

-- Insert Master Produk
INSERT INTO produk (kode_produk, nama_produk, kategori_id, harga, stok_total, stok_minimum, deskripsi) VALUES
('PRD001', 'Laptop Dell Inspiron 15', 1, 8500000, 45, 5, 'Laptop dengan prosesor Intel i5, RAM 8GB'),
('PRD002', 'Monitor LG 24 inch', 1, 2000000, 30, 3, 'Monitor Full HD dengan resolusi 1920x1080'),
('PRD003', 'Kursi Kantor Ergonomis', 2, 1500000, 50, 10, 'Kursi kantor dengan dukungan lumbar'),
('PRD004', 'Meja Kerja Minimalis', 2, 2000000, 25, 5, 'Meja kerja dengan desain minimalis modern'),
('PRD005', 'Kertas HVS 80gsm (500 lembar)', 3, 45000, 200, 50, 'Kertas putih standar A4'),
('PRD006', 'Ballpoint Pilot (1 box = 50 pcs)', 3, 125000, 100, 20, 'Ballpoint hitam berkualitas tinggi'),
('PRD007', 'Besi Plat (per lembar)', 4, 350000, 60, 10, 'Besi plat tebal 5mm'),
('PRD008', 'Bearing SKF 6205', 5, 75000, 150, 30, 'Bearing presisi untuk mesin industri');

-- Insert Stok Gudang
INSERT INTO stok_gudang (produk_id, gudang_id, stok) VALUES
(1, 1, 25),
(1, 2, 15),
(1, 3, 5),
(2, 1, 20),
(2, 2, 10),
(3, 1, 35),
(3, 3, 15),
(4, 1, 15),
(4, 2, 10),
(5, 1, 100),
(5, 2, 50),
(5, 3, 50),
(6, 1, 60),
(6, 2, 40),
(7, 2, 40),
(7, 3, 20),
(8, 1, 80),
(8, 2, 50),
(8, 3, 20);

-- Insert Settings
INSERT INTO settings (nama_sistem, deskripsi, primary_color, secondary_color, dark_mode) VALUES
('Sistem Manajemen Inventaris PT. Maju Jaya', 'Aplikasi manajemen stok dan inventaris profesional', '#3B82F6', '#10B981', FALSE);

-- ========================================
-- INDEXES UNTUK PERFORMA
-- ========================================
CREATE INDEX idx_produk_kategori ON produk(kategori_id);
CREATE INDEX idx_stok_gudang_produk ON stok_gudang(produk_id);
CREATE INDEX idx_stok_gudang_gudang ON stok_gudang(gudang_id);
CREATE INDEX idx_stok_masuk_produk ON stok_masuk(produk_id);
CREATE INDEX idx_stok_masuk_created ON stok_masuk(created_at);
CREATE INDEX idx_stok_keluar_produk ON stok_keluar(produk_id);
CREATE INDEX idx_stok_keluar_created ON stok_keluar(created_at);
CREATE INDEX idx_transfer_stok_status ON transfer_stok(status);
CREATE INDEX idx_pengajuan_peminjaman_status ON pengajuan_peminjaman(status);
CREATE INDEX idx_pengajuan_peminjaman_user ON pengajuan_peminjaman(user_id);
CREATE INDEX idx_audit_trail_user ON audit_trail(user_id);
CREATE INDEX idx_audit_trail_created ON audit_trail(created_at);
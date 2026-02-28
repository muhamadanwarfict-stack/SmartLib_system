-- Database: perpustakaan

CREATE DATABASE IF NOT EXISTS perpustakaan;
USE perpustakaan;

-- Drop tables if exists (untuk menghindari error foreign key)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS peminjaman;
DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS anggota;
DROP TABLE IF EXISTS kategori_buku;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    user_role ENUM('admin', 'petugas') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Kategori Buku
CREATE TABLE kategori_buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_kategori VARCHAR(20) UNIQUE NOT NULL,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Buku
CREATE TABLE buku (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_buku VARCHAR(50) UNIQUE NOT NULL,
    judul VARCHAR(200) NOT NULL,
    pengarang VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100),
    tahun_terbit YEAR,
    kategori_id INT,
    isbn VARCHAR(20),
    jumlah_total INT DEFAULT 1,
    jumlah_tersedia INT DEFAULT 1,
    lokasi_rak VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_kategori (kategori_id),
    FOREIGN KEY (kategori_id) REFERENCES kategori_buku(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Anggota
CREATE TABLE anggota (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_anggota VARCHAR(50) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    tanggal_daftar DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Peminjaman
CREATE TABLE peminjaman (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_peminjaman VARCHAR(50) UNIQUE NOT NULL,
    anggota_id INT NOT NULL,
    buku_id INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    tanggal_dikembalikan DATE,
    denda DECIMAL(10,2) DEFAULT 0,
    status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
    petugas_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_anggota (anggota_id),
    INDEX idx_buku (buku_id),
    INDEX idx_petugas (petugas_id),
    FOREIGN KEY (anggota_id) REFERENCES anggota(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (buku_id) REFERENCES buku(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (petugas_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, nama_lengkap, email, user_role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@perpustakaan.com', 'admin'),
('petugas', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Petugas Perpustakaan', 'petugas@perpustakaan.com', 'petugas');

-- Insert sample kategori
INSERT INTO kategori_buku (kode_kategori, nama_kategori, deskripsi) VALUES
('FIK', 'Fiksi', 'Buku-buku fiksi dan novel'),
('NON', 'Non-Fiksi', 'Buku-buku non-fiksi'),
('REF', 'Referensi', 'Buku referensi dan ensiklopedia'),
('TEK', 'Teknologi', 'Buku-buku teknologi dan komputer'),
('SEJ', 'Sejarah', 'Buku-buku sejarah');

-- Insert sample buku
INSERT INTO buku (kode_buku, judul, pengarang, penerbit, tahun_terbit, kategori_id, isbn, jumlah_total, jumlah_tersedia, lokasi_rak) VALUES
('BK001', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 1, '9789793062792', 3, 3, 'A1'),
('BK002', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 1, '9789799731234', 2, 2, 'A2'),
('BK003', 'Algoritma dan Pemrograman', 'Rinaldi Munir', 'Informatika', 2020, 4, '9786025847123', 5, 5, 'B1');

-- Insert sample anggota
INSERT INTO anggota (no_anggota, nama_lengkap, alamat, telepon, email, tanggal_daftar) VALUES
('AGT001', 'Budi Santoso', 'Jl. Merdeka No. 10', '081234567890', 'budi@email.com', '2024-01-15'),
('AGT002', 'Siti Nurhaliza', 'Jl. Sudirman No. 25', '081234567891', 'siti@email.com', '2024-02-20');

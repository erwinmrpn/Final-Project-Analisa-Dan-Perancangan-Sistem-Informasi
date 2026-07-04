-- =============================================
-- DATABASE: laundry_kiloan
-- =============================================
CREATE DATABASE IF NOT EXISTS laundry_kiloan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE laundry_kiloan;

-- Tabel users (Karyawan dan Owner)
CREATE TABLE IF NOT EXISTS users (
  id VARCHAR(50) PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('Karyawan', 'Owner') NOT NULL,
  aktif BOOLEAN DEFAULT TRUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pelanggan
CREATE TABLE IF NOT EXISTS pelanggan (
  id VARCHAR(50) PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  no_hp VARCHAR(20) NOT NULL UNIQUE,
  alamat TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabel pesanan
CREATE TABLE IF NOT EXISTS pesanan (
  id VARCHAR(50) PRIMARY KEY,
  pelanggan_id VARCHAR(50) NOT NULL,
  user_id VARCHAR(50) NOT NULL,
  layanan VARCHAR(50) NOT NULL,          -- 'Reguler', 'Express', 'Cuci+Setrika'
  berat_kg DECIMAL(10,2) NOT NULL,
  total_biaya DECIMAL(10,2) NOT NULL,    -- berat_kg * harga_per_kg
  status ENUM('Masuk','Proses','Selesai') DEFAULT 'Masuk',
  tanggal_masuk DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel nota (bukti pesanan, dikirim WA setelah input pesanan)
CREATE TABLE IF NOT EXISTS nota (
  id VARCHAR(50) PRIMARY KEY,
  pesanan_id VARCHAR(50) NOT NULL UNIQUE,
  isi_pesan TEXT NOT NULL,               -- Teks pesan WA yang dikirim
  status_kirim BOOLEAN DEFAULT FALSE,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id)
);

-- Tabel transaksi (pembayaran)
CREATE TABLE IF NOT EXISTS transaksi (
  id VARCHAR(50) PRIMARY KEY,
  pesanan_id VARCHAR(50) NOT NULL UNIQUE,
  user_id VARCHAR(50) NOT NULL,
  jumlah_bayar DECIMAL(10,2) NOT NULL,
  metode_pembayaran VARCHAR(50) NOT NULL, -- 'Cash', 'Transfer', 'E-wallet'
  status ENUM('Lunas','Pending') DEFAULT 'Lunas',
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel struk (bukti pembayaran, dikirim WA setelah bayar)
CREATE TABLE IF NOT EXISTS struk (
  id VARCHAR(50) PRIMARY KEY,
  transaksi_id VARCHAR(50) NOT NULL UNIQUE,
  isi_pesan TEXT NOT NULL,               -- Teks pesan WA yang dikirim
  status_kirim BOOLEAN DEFAULT FALSE,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (transaksi_id) REFERENCES transaksi(id)
);

-- Tabel wa_notifikasi_status (notifikasi status cucian selesai)
CREATE TABLE IF NOT EXISTS wa_notifikasi_status (
  id VARCHAR(50) PRIMARY KEY,
  pesanan_id VARCHAR(50) NOT NULL,
  no_hp VARCHAR(20) NOT NULL,
  isi_pesan TEXT NOT NULL,
  tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (pesanan_id) REFERENCES pesanan(id)
);

-- Tabel laporan_pendapatan
CREATE TABLE IF NOT EXISTS laporan_pendapatan (
  id VARCHAR(50) PRIMARY KEY,
  user_id VARCHAR(50) NOT NULL,
  periode_awal DATE NOT NULL,
  periode_akhir DATE NOT NULL,
  total_pendapatan DECIMAL(10,2) NOT NULL,
  total_pesanan INT NOT NULL,
  rata_rata DECIMAL(10,2) NOT NULL,
  tgl_generate DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tabel laporan_kinerja
CREATE TABLE IF NOT EXISTS laporan_kinerja (
  id VARCHAR(50) PRIMARY KEY,
  user_id VARCHAR(50) NOT NULL,          -- Owner yang generate
  karyawan_id VARCHAR(50) NOT NULL,      -- Karyawan yang dievaluasi
  periode_awal DATE NOT NULL,
  periode_akhir DATE NOT NULL,
  jml_pesanan INT DEFAULT 0,
  jml_transaksi INT DEFAULT 0,
  score_kinerja DECIMAL(5,2) DEFAULT 0,
  tgl_generate DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (karyawan_id) REFERENCES users(id)
);

-- =============================================
-- SEED DATA
-- =============================================

-- Users (password = "password123" di-hash bcrypt via PHP password_hash())
INSERT INTO users (id, nama, username, password, role) VALUES
  ('USR-001', 'Owner Laundry', 'owner', '$2y$12$gIydodVMjQfTiJwGRXlbSu6CyUluCJBUithBtyu2zHOHWconPxCXu', 'Owner'),
  ('USR-002', 'Budi Santoso', 'budi', '$2y$12$gIydodVMjQfTiJwGRXlbSu6CyUluCJBUithBtyu2zHOHWconPxCXu', 'Karyawan'),
  ('USR-003', 'Siti Rahayu', 'siti', '$2y$12$gIydodVMjQfTiJwGRXlbSu6CyUluCJBUithBtyu2zHOHWconPxCXu', 'Karyawan');

-- Pelanggan contoh
INSERT INTO pelanggan (id, nama, no_hp, alamat) VALUES
  ('PLG-001', 'Ahmad Fauzi', '08112345678', 'Jl. Merpati No. 5, Semarang'),
  ('PLG-002', 'Dewi Lestari', '08223456789', 'Jl. Kenanga No. 12, Semarang'),
  ('PLG-003', 'Rizki Pratama', '08334567890', 'Jl. Anggrek No. 3, Semarang');

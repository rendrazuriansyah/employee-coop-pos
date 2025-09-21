-- Database: pos_koperasi
-- Household Goods and Equipment Procurement System for Employee Cooperatives

CREATE DATABASE IF NOT EXISTS pos_koperasi;
USE pos_koperasi;

-- Table: level (User access categories)
CREATE TABLE level (
    id_level INT PRIMARY KEY AUTO_INCREMENT,
    level VARCHAR(50) NOT NULL UNIQUE
);

-- Insert default levels
INSERT INTO level (level) VALUES 
('admin'), 
('kasir'), 
('manager');

-- Table: petugas (User data for admin, cashier, etc.)
CREATE TABLE petugas (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nama_user VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    level INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (level) REFERENCES level(id_level)
);

-- Table: manager (Manager user data)
CREATE TABLE manager (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nama_user VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    level INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (level) REFERENCES level(id_level)
);

-- Table: customer (Customer data)
CREATE TABLE customer (
    id_customer INT PRIMARY KEY AUTO_INCREMENT,
    nama_customer VARCHAR(100) NOT NULL,
    alamat TEXT,
    telp VARCHAR(20),
    fax VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: item (Product data)
CREATE TABLE item (
    id_item INT PRIMARY KEY AUTO_INCREMENT,
    nama_item VARCHAR(100) NOT NULL,
    uom VARCHAR(20) NOT NULL, -- unit of measurement
    harga_beli DECIMAL(15,2) NOT NULL,
    harga_jual DECIMAL(15,2) NOT NULL,
    stok INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: sales (Sales information)
CREATE TABLE sales (
    id_sales INT PRIMARY KEY AUTO_INCREMENT,
    tgl_sales DATE NOT NULL,
    id_customer INT NOT NULL,
    do_number VARCHAR(50) UNIQUE,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_customer) REFERENCES customer(id_customer)
);

-- Table: transaction (Sales details per item)
CREATE TABLE transaction (
    id_transaction INT PRIMARY KEY AUTO_INCREMENT,
    id_sales INT NOT NULL,
    id_item INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sales) REFERENCES sales(id_sales) ON DELETE CASCADE,
    FOREIGN KEY (id_item) REFERENCES item(id_item)
);

-- Table: transactio_temp (Temporary transaction data)
CREATE TABLE transactio_temp (
    id_transaction INT PRIMARY KEY AUTO_INCREMENT,
    id_item INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    session_id VARCHAR(100) NOT NULL,
    remark TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_item) REFERENCES item(id_item)
);

-- Table: identitas (Company profile information)
CREATE TABLE identitas (
    id_identitas INT PRIMARY KEY AUTO_INCREMENT,
    nama_identitas VARCHAR(100) NOT NULL,
    badan_hukum VARCHAR(100),
    npwp VARCHAR(50),
    email VARCHAR(100),
    url VARCHAR(100),
    alamat TEXT,
    telp VARCHAR(20),
    fax VARCHAR(20),
    rekening VARCHAR(50),
    foto VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO petugas (nama_user, username, password, level) VALUES 
('Administrator', 'admin', '$2y$10$z1e9Wvai0xXnaaEH6hR0iOAOlRUkI.ixQ.Q0E9T3TrQX.dTiX616C', 1);

-- Insert default manager user (password: manager123)
INSERT INTO manager (nama_user, username, password, level) VALUES
('Manager System', 'manager', '$2y$10$B6q8lzd9vF7xBOAc0vORju0Wd2LI4ApKb6N3nAhcGzO3vz2Eyeq4i', 3);

-- Insert sample company identity
INSERT INTO identitas (nama_identitas, badan_hukum, npwp, email, alamat, telp) VALUES 
('Koperasi Pegawai RSUD Tarakan', 'Koperasi', '12.345.678.9-012.000', 'koperasi@rsudtarakan.com', 'Jl. Pulau Kalimantan No. 1, Tarakan', '0551-21234');

-- Insert sample customers
INSERT INTO customer (nama_customer, alamat, telp, email) VALUES 
('RSUD TARAKAN JAKARTA', 'Jl. Letjen S. Parman No. 1, Jakarta', '021-5681234', 'rsud@jakarta.go.id'),
('Rumah Sakit Umum Daerah', 'Jl. Ahmad Yani No. 15, Surabaya', '031-8291234', 'rsud@surabaya.go.id');

-- =====================================================
-- COMPREHENSIVE SAMPLE DATA FOR POS SYSTEM
-- =====================================================

-- Additional Staff Members
INSERT INTO customer (nama_customer, alamat, telp, fax, email, created_at) VALUES
('PT. Mitra Sejahtera', 'Jl. Sudirman No. 123, Jakarta Pusat', '021-5551234', '021-5551235', 'info@mitrasejahtera.com', '2025-01-15 08:30:00'),
('CV. Berkah Mandiri', 'Jl. Gatot Subroto No. 45, Jakarta Selatan', '021-7891234', '021-7891235', 'admin@berkahmandiri.co.id', '2025-01-20 09:15:00'),
('Toko Sumber Rejeki', 'Jl. Pasar Baru No. 67, Jakarta Pusat', '021-3456789', NULL, 'sumberrejeki@gmail.com', '2025-02-01 10:00:00'),
('PT. Cahaya Utama', 'Jl. HR Rasuna Said No. 88, Jakarta Selatan', '021-5267890', '021-5267891', 'procurement@cahayautama.com', '2025-02-05 11:20:00'),
('UD. Karya Bersama', 'Jl. Cikini Raya No. 34, Jakarta Pusat', '021-3145678', NULL, 'karyabersama@yahoo.com', '2025-02-10 14:30:00'),
('CV. Harapan Jaya', 'Jl. Kemang Raya No. 56, Jakarta Selatan', '021-7194567', '021-7194568', 'harapanjaya@gmail.com', '2025-02-15 08:45:00'),
('Toko Makmur Sentosa', 'Jl. Tanah Abang No. 78, Jakarta Pusat', '021-2345678', NULL, 'makmursentosa@hotmail.com', '2025-02-20 13:15:00'),
('PT. Sukses Mandiri', 'Jl. Kuningan No. 90, Jakarta Selatan', '021-8901234', '021-8901235', 'sukses@suksesmandiri.co.id', '2025-02-25 16:00:00'),
('CV. Maju Bersama', 'Jl. Menteng No. 12, Jakarta Pusat', '021-4567890', NULL, 'majubersama@gmail.com', '2025-03-01 09:30:00'),
('Toko Sejahtera Abadi', 'Jl. Blok M No. 23, Jakarta Selatan', '021-6789012', NULL, 'sejahteraabadi@yahoo.co.id', '2025-03-05 11:45:00'),
('PT. Global Prima', 'Jl. Thamrin No. 45, Jakarta Pusat', '021-1234567', '021-1234568', 'info@globalprima.com', '2025-03-10 08:20:00'),
('CV. Anugerah Teknik', 'Jl. Senayan No. 67, Jakarta Selatan', '021-9876543', '021-9876544', 'anugerahteknik@gmail.com', '2025-03-15 15:10:00'),
('UD. Sari Rasa', 'Jl. Sabang No. 89, Jakarta Pusat', '021-5432109', NULL, 'sarirasa@hotmail.com', '2025-03-20 12:25:00'),
('Toko Bintang Terang', 'Jl. Pondok Indah No. 34, Jakarta Selatan', '021-8765432', NULL, 'bintangterang@gmail.com', '2025-03-25 14:40:00'),
('PT. Nusantara Jaya', 'Jl. Monas No. 56, Jakarta Pusat', '021-2109876', '021-2109877', 'nusantarajaya@company.com', '2025-04-01 10:15:00'),
('CV. Bahagia Selalu', 'Jl. Fatmawati No. 78, Jakarta Selatan', '021-6543210', NULL, 'bahagiaselalu@yahoo.com', '2025-04-05 13:50:00'),
('Toko Murah Meriah', 'Jl. Senen No. 90, Jakarta Pusat', '021-0987654', NULL, 'murahmeriah@gmail.com', '2025-04-10 09:05:00'),
('PT. Indah Permata', 'Jl. Cipete No. 12, Jakarta Selatan', '021-4321098', '021-4321099', 'indahpermata@company.id', '2025-04-15 16:30:00'),
('CV. Jaya Abadi', 'Jl. Gambir No. 34, Jakarta Pusat', '021-8765109', NULL, 'jayaabadi@hotmail.com', '2025-04-20 11:20:00'),
('UD. Rezeki Nomplok', 'Jl. Lebak Bulus No. 56, Jakarta Selatan', '021-1098765', NULL, 'rezekinomplok@gmail.com', '2025-04-25 08:55:00');

-- Comprehensive Customer Data (20 customers)
INSERT INTO petugas (nama_user, username, password, level, created_at) VALUES
                                                                           ('Siti Nurhaliza', 'kasir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, '2025-01-10 08:00:00'),
                                                                           ('Budi Santoso', 'kasir2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, '2025-01-15 09:00:00'),
                                                                           ('Rendra Zuriansyah', 'supervisor1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3, '2025-01-25 11:00:00');

-- Comprehensive Item Data (35 items across categories)
INSERT INTO item (nama_item, uom, harga_beli, harga_jual, stok, created_at) VALUES
-- Office Supplies
('Kertas A4 80gsm', 'Rim', 45000, 55000, 150, '2025-01-10 08:00:00'),
('Pulpen Pilot', 'Pcs', 3500, 5000, 200, '2025-01-10 08:15:00'),
('Pensil 2B', 'Pcs', 2000, 3000, 180, '2025-01-10 08:30:00'),
('Penghapus Karet', 'Pcs', 1500, 2500, 120, '2025-01-10 08:45:00'),
('Stapler Kenko', 'Pcs', 25000, 35000, 50, '2025-01-10 09:00:00'),
('Isi Stapler', 'Box', 8000, 12000, 80, '2025-01-10 09:15:00'),
('Map Plastik', 'Pcs', 3000, 4500, 100, '2025-01-10 09:30:00'),
('Amplop Putih', 'Pack', 15000, 22000, 60, '2025-01-10 09:45:00'),
('Spidol Whiteboard', 'Pcs', 8000, 12000, 90, '2025-01-10 10:00:00'),
('Correction Pen', 'Pcs', 5000, 7500, 70, '2025-01-10 10:15:00'),

-- Cleaning Supplies
('Sabun Cuci Piring', 'Botol', 8000, 12000, 100, '2025-01-15 08:00:00'),
('Deterjen Bubuk', 'Kg', 12000, 18000, 80, '2025-01-15 08:15:00'),
('Pembersih Lantai', 'Botol', 15000, 22000, 60, '2025-01-15 08:30:00'),
('Tisu Toilet', 'Roll', 8000, 12000, 120, '2025-01-15 08:45:00'),
('Tisu Wajah', 'Pack', 12000, 18000, 90, '2025-01-15 09:00:00'),
('Sapu Lidi', 'Pcs', 15000, 22000, 40, '2025-01-15 09:15:00'),
('Pel Lantai', 'Pcs', 25000, 35000, 30, '2025-01-15 09:30:00'),
('Ember Plastik', 'Pcs', 20000, 30000, 25, '2025-01-15 09:45:00'),
('Sikat WC', 'Pcs', 10000, 15000, 35, '2025-01-15 10:00:00'),
('Pengharum Ruangan', 'Botol', 18000, 25000, 50, '2025-01-15 10:15:00'),

-- Kitchen Supplies
('Piring Melamin', 'Pcs', 15000, 22000, 80, '2025-01-20 08:00:00'),
('Gelas Plastik', 'Pcs', 5000, 8000, 150, '2025-01-20 08:15:00'),
('Sendok Stainless', 'Pcs', 8000, 12000, 100, '2025-01-20 08:30:00'),
('Garpu Stainless', 'Pcs', 8000, 12000, 100, '2025-01-20 08:45:00'),
('Pisau Dapur', 'Pcs', 25000, 35000, 40, '2025-01-20 09:00:00'),

-- Electronic Items
('Kalkulator Casio', 'Pcs', 45000, 65000, 30, '2025-01-25 08:00:00'),
('Lampu LED 12W', 'Pcs', 25000, 35000, 80, '2025-01-25 08:15:00'),
('Stop Kontak', 'Pcs', 15000, 22000, 60, '2025-01-25 08:30:00'),
('Senter LED', 'Pcs', 35000, 50000, 40, '2025-01-25 09:15:00'),
('Setrika Listrik', 'Pcs', 150000, 220000, 10, '2025-01-25 09:45:00'),

-- Furniture
('Kursi Plastik', 'Pcs', 45000, 65000, 50, '2025-02-01 08:00:00'),
('Meja Lipat', 'Pcs', 120000, 180000, 20, '2025-02-01 08:15:00'),
('Lemari Plastik', 'Pcs', 200000, 300000, 15, '2025-02-01 08:30:00'),
('Tempat Sampah', 'Pcs', 25000, 35000, 40, '2025-02-01 09:00:00'),
('Jam Dinding', 'Pcs', 35000, 50000, 30, '2025-02-01 09:15:00'),

-- Low Stock Items (for testing alerts)
('Tinta Printer', 'Botol', 50000, 75000, 3, '2025-02-05 08:00:00'),
('Kertas Foto', 'Pack', 40000, 60000, 2, '2025-02-05 08:15:00');

-- Sales Transaction Data (25 transactions)
INSERT INTO sales (tgl_sales, id_customer, do_number, status, total_amount, created_at) VALUES
('2025-01-15', 3, 'DO-2025-001', 'completed', 275000, '2025-01-15 10:30:00'),
('2025-01-20', 4, 'DO-2025-002', 'completed', 180000, '2025-01-20 11:15:00'),
('2025-02-01', 5, 'DO-2025-003', 'completed', 95000, '2025-02-01 14:20:00'),
('2025-02-05', 6, 'DO-2025-004', 'completed', 420000, '2025-02-05 09:45:00'),
('2025-02-10', 7, 'DO-2025-005', 'completed', 160000, '2025-02-10 13:30:00'),
('2025-02-15', 8, 'DO-2025-006', 'completed', 320000, '2025-02-15 15:10:00'),
('2025-03-01', 9, 'DO-2025-007', 'completed', 145000, '2025-03-01 16:45:00'),
('2025-03-05', 10, 'DO-2025-008', 'completed', 380000, '2025-03-05 08:20:00'),
('2025-03-10', 11, 'DO-2025-009', 'completed', 225000, '2025-03-10 10:15:00'),
('2025-03-15', 12, 'DO-2025-010', 'completed', 190000, '2025-03-15 12:30:00'),
('2025-04-01', 13, 'DO-2025-011', 'completed', 480000, '2025-04-01 14:45:00'),
('2025-04-05', 14, 'DO-2025-012', 'completed', 165000, '2025-04-05 09:20:00'),
('2025-04-10', 15, 'DO-2025-013', 'completed', 290000, '2025-04-10 11:35:00'),
('2025-04-15', 16, 'DO-2025-014', 'completed', 135000, '2025-04-15 13:50:00'),
('2025-05-01', 17, 'DO-2025-015', 'completed', 375000, '2025-05-01 15:25:00'),
('2025-05-05', 18, 'DO-2025-016', 'completed', 210000, '2025-05-05 16:40:00'),
('2025-05-10', 19, 'DO-2025-017', 'completed', 155000, '2025-05-10 08:55:00'),
('2025-05-15', 20, 'DO-2025-018', 'completed', 340000, '2025-05-15 10:10:00'),
('2025-09-01', 21, 'DO-2025-019', 'completed', 185000, '2025-09-01 12:25:00'),
('2025-09-05', 22, 'DO-2025-020', 'completed', 425000, '2025-09-05 14:40:00'),
('2025-09-10', 3, 'DO-2025-021', 'completed', 195000, '2025-09-10 09:15:00'),
('2025-09-15', 4, 'DO-2025-022', 'completed', 310000, '2025-09-15 11:30:00'),
('2025-09-20', 5, 'DO-2025-023', 'pending', 175000, '2025-09-20 13:45:00'),
('2025-09-22', 6, 'DO-2025-024', 'pending', 265000, '2025-09-22 15:20:00'),
('2025-09-25', 7, 'DO-2025-025', 'completed', 390000, '2025-09-25 16:35:00');

-- Transaction Details (Items in each sale)
INSERT INTO transaction (id_sales, id_item, quantity, price, amount) VALUES
-- Sales 1 (DO-2025-001) - Total: 275000
(1, 3, 5, 55000, 275000),
-- Sales 2 (DO-2025-002) - Total: 180000  
(2, 34, 1, 180000, 180000),
-- Sales 3 (DO-2025-003) - Total: 95000
(3, 13, 5, 12000, 60000),
(3, 14, 2, 18000, 35000),
-- Sales 4 (DO-2025-004) - Total: 420000
(4, 33, 2, 65000, 130000),
(4, 26, 4, 65000, 260000),
(4, 7, 1, 35000, 30000),
-- Sales 5 (DO-2025-005) - Total: 160000
(5, 23, 4, 22000, 88000),
(5, 24, 9, 8000, 72000),
-- Sales 6 (DO-2025-006) - Total: 320000
(6, 35, 2, 300000, 300000),
(6, 5, 7, 3000, 20000),
-- Sales 7 (DO-2025-007) - Total: 145000
(7, 15, 3, 22000, 66000),
(7, 16, 4, 12000, 48000),
(7, 17, 2, 18000, 31000),
-- Sales 8 (DO-2025-008) - Total: 380000
(8, 32, 2, 180000, 360000),
(8, 6, 8, 2500, 20000),
-- Sales 9 (DO-2025-009) - Total: 225000
(9, 28, 3, 35000, 105000),
(9, 29, 4, 30000, 120000),
-- Sales 10 (DO-2025-010) - Total: 190000
(10, 4, 10, 5000, 50000),
(10, 31, 2, 65000, 130000),
(10, 8, 1, 12000, 10000),

-- Sales 11 (DO-2025-011) - Total: 480000
(11, 32, 2, 180000, 360000),
(11, 27, 2, 65000, 120000),

-- Sales 12 (DO-2025-012) - Total: 165000
(12, 1, 3, 55000, 165000),

-- Sales 13 (DO-2025-013) - Total: 290000
(13, 33, 1, 300000, 290000),

-- Sales 14 (DO-2025-014) - Total: 135000
(14, 11, 5, 12000, 60000),
(14, 12, 4, 18000, 75000),

-- Sales 15 (DO-2025-015) - Total: 375000
(15, 32, 2, 180000, 360000),
(15, 6, 2, 12000, 15000),

-- Sales 16 (DO-2025-016) - Total: 210000
(16, 26, 3, 65000, 195000),
(16, 9, 2, 12000, 15000),

-- Sales 17 (DO-2025-017) - Total: 155000
(17, 21, 5, 22000, 110000),
(17, 22, 6, 8000, 45000),

-- Sales 18 (DO-2025-018) - Total: 340000
(18, 33, 1, 300000, 300000),
(18, 5, 1, 35000, 40000),

-- Sales 19 (DO-2025-019) - Total: 185000
(19, 28, 2, 35000, 70000),
(19, 29, 4, 30000, 115000),

-- Sales 20 (DO-2025-020) - Total: 425000
(20, 32, 2, 180000, 360000),
(20, 31, 1, 65000, 65000),

-- Sales 21 (DO-2025-021) - Total: 195000
(21, 1, 3, 55000, 165000),
(21, 2, 6, 5000, 30000),

-- Sales 22 (DO-2025-022) - Total: 310000
(22, 33, 1, 300000, 300000),
(22, 3, 4, 3000, 10000),

-- Sales 23 (DO-2025-023) - Total: 175000
(23, 27, 2, 65000, 130000),
(23, 21, 2, 22000, 45000),

-- Sales 24 (DO-2025-024) - Total: 265000
(24, 32, 1, 180000, 180000),
(24, 26, 1, 65000, 65000),
(24, 11, 2, 12000, 20000),

-- Sales 25 (DO-2025-025) - Total: 390000
(25, 33, 1, 300000, 300000),
(25, 31, 1, 65000, 65000),
(25, 27, 1, 65000, 25000);

-- =====================================================
-- LOGIN CREDENTIALS SUMMARY:
-- =====================================================
-- Admin: admin / admin123
-- Manager: manager / manager123  
-- Kasir 1: kasir1 / kasir123
-- Kasir 2: kasir2 / kasir123
-- Supervisor: supervisor1 / kasir123
-- =====================================================

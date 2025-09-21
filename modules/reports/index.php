<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../config/Database.php';

$auth = new Auth();
$auth->requireLogin();

// Only admin and manager can access reports
if (!$auth->hasPermission(1) && !$auth->hasPermission(2)) {
    header('Location: ../../dashboard.php');
    exit;
}

$db = Database::getInstance();
$user = $auth->getCurrentUser();

// Get date range from request
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$endDate = $_GET['end_date'] ?? date('Y-m-d'); // Today

// Sales Summary
$salesSummary = $db->fetchOne("
    SELECT 
        COUNT(*) as total_sales,
        SUM(total_amount) as total_revenue,
        AVG(total_amount) as avg_sale,
        COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_sales,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_sales,
        COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_sales
    FROM sales 
    WHERE DATE(tgl_sales) BETWEEN ? AND ?
", [$startDate, $endDate]);

// Top Selling Items
$topItems = $db->fetchAll("
    SELECT 
        i.nama_item,
        SUM(t.quantity) as total_sold,
        SUM(t.amount) as total_revenue,
        COUNT(DISTINCT t.id_sales) as transaction_count
    FROM transaction t
    JOIN item i ON t.id_item = i.id_item
    JOIN sales s ON t.id_sales = s.id_sales
    WHERE DATE(s.tgl_sales) BETWEEN ? AND ?
    GROUP BY i.id_item, i.nama_item
    ORDER BY total_sold DESC
    LIMIT 10
", [$startDate, $endDate]);

// Customer Analysis
$customerStats = $db->fetchAll("
    SELECT 
        c.nama_customer as nama,
        COUNT(s.id_sales) as total_purchases,
        SUM(s.total_amount) as total_spent,
        AVG(s.total_amount) as avg_purchase,
        MAX(s.tgl_sales) as last_purchase
    FROM customer c
    JOIN sales s ON c.id_customer = s.id_customer
    WHERE DATE(s.tgl_sales) BETWEEN ? AND ?
    GROUP BY c.id_customer, c.nama_customer
    ORDER BY total_spent DESC
    LIMIT 10
", [$startDate, $endDate]);

// Low Stock Items
$lowStockItems = $db->fetchAll("
    SELECT 
        id_item,
        nama_item,
        stok as stock,
        harga_beli,
        harga_jual,
        (stok * harga_beli) as stock_value
    FROM item 
    WHERE stok <= 10
    ORDER BY stok ASC
    LIMIT 20
");

// Monthly Sales Trend (last 6 months)
$monthlySales = $db->fetchAll("
    SELECT 
        DATE_FORMAT(tgl_sales, '%Y-%m') as month,
        COUNT(*) as total_sales,
        SUM(total_amount) as total_revenue
    FROM sales 
    WHERE tgl_sales >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    AND status = 'completed'
    GROUP BY DATE_FORMAT(tgl_sales, '%Y-%m')
    ORDER BY month DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Koperasi Pegawai RSUD Tarakan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #4e73df 0%, #224abe 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .main-content {
            background-color: #f8f9fc;
            min-height: 100vh;
        }
        .navbar-custom {
            background: white;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .card {
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .table th {
            background-color: #f8f9fc;
            border-top: none;
            font-weight: 600;
            color: #5a5c69;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #224abe 0%, #4e73df 100%);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        .stat-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
        }
        .stat-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 15px;
        }
        .stat-card-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <i class="fas fa-store fa-2x mb-2"></i>
                        <h5>KOPERASI PEGAWAI</h5>
                        <small>RSUD TARAKAN</small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../../dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-light">
                                <span>MASTER DATA</span>
                            </h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../customer/index.php">
                                <i class="fas fa-users me-2"></i>
                                Customer
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../item/index.php">
                                <i class="fas fa-boxes me-2"></i>
                                Item
                            </a>
                        </li>
                        
                        <?php if ($auth->hasPermission(1)): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../petugas/index.php">
                                <i class="fas fa-user-tie me-2"></i>
                                Petugas
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-light">
                                <span>TRANSAKSI</span>
                            </h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../sales/index.php">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Sales
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../transaction/new.php">
                                <i class="fas fa-cash-register me-2"></i>
                                New Transaction
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-light">
                                <span>LAPORAN</span>
                            </h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-chart-bar me-2"></i>
                                Reports
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-light">
                                <span>AKUN</span>
                            </h6>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="../profile/index.php">
                                <i class="fas fa-user me-2"></i>
                                Profile
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link text-light" href="../../logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <!-- Top navbar -->
                <nav class="navbar navbar-expand-lg navbar-custom mb-4">
                    <div class="container-fluid">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../../dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Reports</li>
                            </ol>
                        </nav>
                        
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle me-2"></i>
                                        <?= htmlspecialchars($user['nama_user']) ?>
                                        <span class="badge bg-primary ms-2"><?= htmlspecialchars($user['level_name']) ?></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="../../logout.php">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                
                <!-- Page content -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-chart-bar me-2"></i>LAPORAN SISTEM</h2>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>
                        Print Report
                    </button>
                </div>
                
                <!-- Date Filter -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="start_date" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       value="<?= htmlspecialchars($startDate) ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="end_date" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       value="<?= htmlspecialchars($endDate) ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>
                                    Filter Data
                                </button>
                                <a href="index.php" class="btn btn-secondary ms-2">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                <h4><?= number_format($salesSummary['total_sales'] ?? 0) ?></h4>
                                <p class="mb-0">Total Penjualan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card-2">
                            <div class="card-body text-center">
                                <i class="fas fa-money-bill-wave fa-2x mb-2"></i>
                                <h4>Rp <?= number_format($salesSummary['total_revenue'] ?? 0) ?></h4>
                                <p class="mb-0">Total Pendapatan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card-3">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <h4>Rp <?= number_format($salesSummary['avg_sale'] ?? 0) ?></h4>
                                <p class="mb-0">Rata-rata Penjualan</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card-4">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h4><?= number_format($salesSummary['completed_sales'] ?? 0) ?></h4>
                                <p class="mb-0">Transaksi Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Top Selling Items -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-trophy me-2"></i>
                                    TOP 10 BARANG TERLARIS
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nama Barang</th>
                                                <th>Terjual</th>
                                                <th>Pendapatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($topItems)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center">Tidak ada data</td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach ($topItems as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['nama_item']) ?></td>
                                                <td><?= number_format($item['total_sold']) ?></td>
                                                <td>Rp <?= number_format($item['total_revenue']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Top Customers -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-users me-2"></i>
                                    TOP 10 CUSTOMER
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Nama Customer</th>
                                                <th>Transaksi</th>
                                                <th>Total Belanja</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($customerStats)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center">Tidak ada data</td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach ($customerStats as $customer): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($customer['nama']) ?></td>
                                                <td><?= number_format($customer['total_purchases']) ?></td>
                                                <td>Rp <?= number_format($customer['total_spent']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Low Stock Alert -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    STOK MENIPIS (≤10)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Kode</th>
                                                <th>Nama Barang</th>
                                                <th>Stok</th>
                                                <th>Nilai Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($lowStockItems)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Semua stok aman
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach ($lowStockItems as $item): ?>
                                            <tr class="<?= $item['stock'] <= 5 ? 'table-danger' : 'table-warning' ?>">
                                                <td><?= htmlspecialchars($item['id_item']) ?></td>
                                                <td><?= htmlspecialchars($item['nama_item']) ?></td>
                                                <td><?= number_format($item['stock']) ?></td>
                                                <td>Rp <?= number_format($item['stock_value']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Monthly Trend -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-line me-2"></i>
                                    TREN PENJUALAN 6 BULAN TERAKHIR
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Bulan</th>
                                                <th>Transaksi</th>
                                                <th>Pendapatan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($monthlySales)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center">Tidak ada data</td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach ($monthlySales as $month): ?>
                                            <tr>
                                                <td><?= date('M Y', strtotime($month['month'] . '-01')) ?></td>
                                                <td><?= number_format($month['total_sales']) ?></td>
                                                <td>Rp <?= number_format($month['total_revenue']) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Report Footer -->
                <div class="card">
                    <div class="card-body text-center">
                        <p class="mb-1"><strong>Koperasi Pegawai RSUD Tarakan</strong></p>
                        <p class="mb-1">Laporan dibuat pada: <?= date('d/m/Y H:i:s') ?></p>
                        <p class="mb-0">Periode: <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?></p>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

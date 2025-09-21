<?php
require_once 'classes/Auth.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Koperasi Pegawai RSUD Tarakan</title>
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
        .card-stats {
            border-left: 4px solid;
            border-radius: 8px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .card-primary { border-left-color: #4e73df; }
        .card-success { border-left-color: #1cc88a; }
        .card-info { border-left-color: #36b9cc; }
        .card-warning { border-left-color: #f6c23e; }
        .text-primary-custom { color: #4e73df !important; }
        .text-success-custom { color: #1cc88a !important; }
        .text-info-custom { color: #36b9cc !important; }
        .text-warning-custom { color: #f6c23e !important; }
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
                            <a class="nav-link active" href="dashboard.php">
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
                            <a class="nav-link" href="modules/customer/index.php">
                                <i class="fas fa-users me-2"></i>
                                Customer
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="modules/item/index.php">
                                <i class="fas fa-boxes me-2"></i>
                                Item
                            </a>
                        </li>
                        
                        <?php if ($auth->hasPermission(1)): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="modules/petugas/index.php">
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
                            <a class="nav-link" href="modules/sales/index.php">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Sales
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="modules/transaction/new.php">
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
                            <a class="nav-link" href="modules/reports/index.php">
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
                            <a class="nav-link" href="modules/profile/index.php">
                                <i class="fas fa-user me-2"></i>
                                Profile
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link text-light" href="logout.php">
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
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user-circle me-2"></i>
                                        <?= htmlspecialchars($user['nama_user']) ?>
                                        <span class="badge bg-primary ms-2"><?= htmlspecialchars($user['level_name']) ?></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="modules/profile/index.php">
                                            <i class="fas fa-user me-2"></i>Profile
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="logout.php">
                                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                                        </a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                
                <!-- Dashboard content -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-calendar me-1"></i>
                                <?= date('d F Y') ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats card-primary h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary-custom text-uppercase mb-1">
                                            Total Customer
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-customers">
                                            Loading...
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-primary-custom"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats card-success h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success-custom text-uppercase mb-1">
                                            Total Penjualan (Bulan Ini)
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthly-sales">
                                            Loading...
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-dollar-sign fa-2x text-success-custom"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats card-info h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info-custom text-uppercase mb-1">
                                            Total Item
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-items">
                                            Loading...
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-boxes fa-2x text-info-custom"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-stats card-warning h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning-custom text-uppercase mb-1">
                                            Transaksi Hari Ini
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="daily-transactions">
                                            Loading...
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-warning-custom"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Transactions -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">Transaksi Terbaru</h6>
                                <a href="modules/sales/index.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i>Lihat Semua
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="recent-transactions">
                                        <thead>
                                            <tr>
                                                <th>No. Sales</th>
                                                <th>Tanggal</th>
                                                <th>Customer</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="5" class="text-center">Loading...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Stok Menipis</h6>
                            </div>
                            <div class="card-body">
                                <div id="low-stock-items">
                                    <div class="text-center">Loading...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load dashboard statistics
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            loadRecentTransactions();
            loadLowStockItems();
        });
        
        function loadDashboardStats() {
            fetch('api/dashboard_stats.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-customers').textContent = data.total_customers || '0';
                    document.getElementById('monthly-sales').textContent = 'Rp ' + (data.monthly_sales || '0').toLocaleString('id-ID');
                    document.getElementById('total-items').textContent = data.total_items || '0';
                    document.getElementById('daily-transactions').textContent = data.daily_transactions || '0';
                })
                .catch(error => {
                    console.error('Error loading dashboard stats:', error);
                });
        }
        
        function loadRecentTransactions() {
            fetch('api/recent_transactions.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#recent-transactions tbody');
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada transaksi</td></tr>';
                        return;
                    }
                    
                    tbody.innerHTML = data.map(transaction => `
                        <tr>
                            <td>${transaction.id_sales}</td>
                            <td>${transaction.tgl_sales}</td>
                            <td>${transaction.nama_customer}</td>
                            <td>Rp ${parseFloat(transaction.total_amount).toLocaleString('id-ID')}</td>
                            <td>
                                <span class="badge bg-${transaction.status === 'completed' ? 'success' : transaction.status === 'pending' ? 'warning' : 'danger'}">
                                    ${transaction.status}
                                </span>
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading recent transactions:', error);
                });
        }
        
        function loadLowStockItems() {
            fetch('api/low_stock.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('low-stock-items');
                    if (data.length === 0) {
                        container.innerHTML = '<div class="text-center text-muted">Semua stok aman</div>';
                        return;
                    }
                    
                    container.innerHTML = data.map(item => `
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-grow-1">
                                <div class="fw-bold">${item.nama_item}</div>
                                <small class="text-muted">Stok: ${item.stok} ${item.uom}</small>
                            </div>
                            <span class="badge bg-warning">${item.stok}</span>
                        </div>
                    `).join('');
                })
                .catch(error => {
                    console.error('Error loading low stock items:', error);
                });
        }
    </script>
</body>
</html>

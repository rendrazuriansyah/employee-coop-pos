<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Customer.php';

$auth = new Auth();
$auth->requireLogin();

$customer = new Customer();
$user = $auth->getCurrentUser();

$id = $_GET['id'] ?? 0;

// Get customer data
$customerData = $customer->getById($id);
if (!$customerData) {
    header('Location: index.php');
    exit;
}

// Get customer sales history
$salesHistory = $customer->getCustomerSales($id);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Customer - Koperasi Pegawai RSUD Tarakan</title>
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
        .info-label {
            font-weight: 600;
            color: #5a5c69;
        }
        .info-value {
            color: #3a3b45;
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
                            <a class="nav-link active" href="index.php">
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
                                <li class="breadcrumb-item"><a href="index.php">Customer</a></li>
                                <li class="breadcrumb-item active">Detail Customer</li>
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
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-user me-2"></i>
                                    DETAIL CUSTOMER - ID: <?= htmlspecialchars($customerData['id_customer']) ?>
                                </h5>
                            </div>
                            
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="info-label" width="40%">ID Customer:</td>
                                                <td class="info-value"><?= htmlspecialchars($customerData['id_customer']) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Nama Customer:</td>
                                                <td class="info-value"><?= htmlspecialchars($customerData['nama_customer']) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Alamat:</td>
                                                <td class="info-value"><?= htmlspecialchars($customerData['alamat'] ?: '-') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Telepon:</td>
                                                <td class="info-value"><?= htmlspecialchars($customerData['telp'] ?: '-') ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td class="info-label" width="40%">Fax:</td>
                                                <td class="info-value"><?= htmlspecialchars($customerData['fax'] ?: '-') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Email:</td>
                                                <td class="info-value"><?= htmlspecialchars($customerData['email'] ?: '-') ?></td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Dibuat pada:</td>
                                                <td class="info-value"><?= date('d/m/Y H:i', strtotime($customerData['created_at'])) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="info-label">Status:</td>
                                                <td class="info-value">
                                                    <span class="badge bg-success">Active</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Kembali
                                    </a>
                                    <div>
                                        <a href="edit.php?id=<?= $customerData['id_customer'] ?>" class="btn btn-warning me-2">
                                            <i class="fas fa-edit me-2"></i>
                                            Edit
                                        </a>
                                        <button type="button" class="btn btn-danger" 
                                                onclick="deleteCustomer(<?= $customerData['id_customer'] ?>)">
                                            <i class="fas fa-trash me-2"></i>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Statistik Customer
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <h3 class="text-primary"><?= count($salesHistory) ?></h3>
                                    <p class="text-muted mb-0">Total Transaksi</p>
                                </div>
                                
                                <?php 
                                $totalAmount = array_sum(array_column($salesHistory, 'total_amount'));
                                ?>
                                <div class="text-center">
                                    <h4 class="text-success">Rp <?= number_format($totalAmount, 0, ',', '.') ?></h4>
                                    <p class="text-muted mb-0">Total Pembelian</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sales History -->
                <?php if (!empty($salesHistory)): ?>
                <div class="card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2"></i>
                            Riwayat Transaksi
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No. Sales</th>
                                        <th>Tanggal</th>
                                        <th>DO Number</th>
                                        <th>Total Items</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($salesHistory as $sale): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sale['id_sales']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($sale['tgl_sales'])) ?></td>
                                        <td><?= htmlspecialchars($sale['do_number'] ?: '-') ?></td>
                                        <td><?= htmlspecialchars($sale['total_items']) ?></td>
                                        <td>Rp <?= number_format($sale['total_amount'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge bg-<?= $sale['status'] === 'completed' ? 'success' : ($sale['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($sale['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="../sales/view.php?id=<?= $sale['id_sales'] ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteCustomer(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data customer akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>

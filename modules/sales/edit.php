<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Sales.php';
require_once __DIR__ . '/../../classes/Customer.php';

$auth = new Auth();
$auth->requireLogin();

$sales = new Sales();
$customer = new Customer();
$user = $auth->getCurrentUser();

$id = $_GET['id'] ?? 0;

// Get sales detail
$salesDetail = $sales->getSalesDetail($id);
if (!$salesDetail) {
    header('Location: index.php?error=' . urlencode('Sales tidak ditemukan'));
    exit;
}

$salesData = $salesDetail['sales'];
$transactions = $salesDetail['transactions'];

// Get all customers for dropdown
$customers = $customer->getAll();

// Handle form submission
if ($_POST) {
    try {
        $updateData = [
            'tgl_sales' => $_POST['tgl_sales'],
            'id_customer' => $_POST['id_customer'],
            'do_number' => $_POST['do_number'],
            'status' => $_POST['status'],
            'current_id' => $id
        ];
        
        $errors = $sales->validate($updateData);
        
        if (empty($errors)) {
            unset($updateData['current_id']);
            $sales->update($id, $updateData);
            header('Location: index.php?success=' . urlencode('Sales berhasil diupdate'));
            exit;
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sales - Koperasi Pegawai RSUD Tarakan</title>
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
        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #224abe 0%, #4e73df 100%);
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
                            <a class="nav-link active" href="index.php">
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
                            <a class="nav-link" href="../reports/index.php">
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
                                <li class="breadcrumb-item"><a href="index.php">Sales</a></li>
                                <li class="breadcrumb-item active">Edit Sales</li>
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
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit me-2"></i>
                                    EDIT SALES
                                </h5>
                            </div>
                            
                            <div class="card-body">
                                <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>
                                
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="tgl_sales" class="form-label">Tanggal Sales <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="tgl_sales" name="tgl_sales" 
                                                       value="<?= htmlspecialchars($salesData['tgl_sales']) ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="do_number" class="form-label">DO Number</label>
                                                <input type="text" class="form-control" id="do_number" name="do_number" 
                                                       value="<?= htmlspecialchars($salesData['do_number']) ?>" 
                                                       placeholder="Opsional - akan digenerate otomatis jika kosong">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="id_customer" class="form-label">Customer <span class="text-danger">*</span></label>
                                        <select class="form-select" id="id_customer" name="id_customer" required>
                                            <option value="">Pilih Customer</option>
                                            <?php foreach ($customers as $cust): ?>
                                            <option value="<?= $cust['id_customer'] ?>" 
                                                    <?= $cust['id_customer'] == $salesData['id_customer'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cust['nama_customer']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="pending" <?= $salesData['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="completed" <?= $salesData['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                            <option value="cancelled" <?= $salesData['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between">
                                        <a href="index.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Update Sales
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Sales Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless table-sm">
                                    <tr>
                                        <td><strong>Sales ID:</strong></td>
                                        <td><?= htmlspecialchars($salesData['id_sales']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Current DO:</strong></td>
                                        <td><?= htmlspecialchars($salesData['do_number'] ?: '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Amount:</strong></td>
                                        <td>Rp <?= number_format($salesData['total_amount'], 0, ',', '.') ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Items:</strong></td>
                                        <td><?= count($transactions) ?> items</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td><?= date('d/m/Y H:i', strtotime($salesData['created_at'])) ?></td>
                                    </tr>
                                </table>
                                
                                <hr>
                                
                                <div class="d-grid gap-2">
                                    <a href="view.php?id=<?= $salesData['id_sales'] ?>" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye me-2"></i>
                                        View Details
                                    </a>
                                    <a href="print.php?id=<?= $salesData['id_sales'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-print me-2"></i>
                                        Print Invoice
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Catatan
                                </h6>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">
                                    <ul class="mb-0">
                                        <li>Perubahan customer akan mempengaruhi invoice</li>
                                        <li>DO Number harus unik jika diisi</li>
                                        <li>Status "Cancelled" tidak dapat diubah kembali</li>
                                        <li>Item transaksi tidak dapat diubah di halaman ini</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

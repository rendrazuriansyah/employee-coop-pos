<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Petugas.php';

$auth = new Auth();
$auth->requireLogin();

// Only admin can access petugas management
if (!$auth->hasPermission(1)) {
    header('Location: ../../dashboard.php');
    exit;
}

$petugas = new Petugas();
$user = $auth->getCurrentUser();

$id = $_GET['id'] ?? 0;
$petugasData = $petugas->getUserWithLevel($id);

if (!$petugasData) {
    header('Location: index.php?error=Data petugas tidak ditemukan');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Petugas - Koperasi Pegawai RSUD Tarakan</title>
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
        .info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
                        
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-user-tie me-2"></i>
                                Petugas
                            </a>
                        </li>
                        
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
                                <li class="breadcrumb-item"><a href="index.php">Petugas</a></li>
                                <li class="breadcrumb-item active">Detail</li>
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
                <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($_GET['success']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <!-- User Info Card -->
                <div class="card info-card mb-4">
                    <div class="card-body text-center">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <i class="fas fa-user-circle fa-5x mb-3"></i>
                            </div>
                            <div class="col-md-6">
                                <h3 class="mb-2"><?= htmlspecialchars($petugasData['nama_user']) ?></h3>
                                <p class="mb-1">
                                    <i class="fas fa-at me-2"></i>
                                    <?= htmlspecialchars($petugasData['username']) ?>
                                </p>
                                <span class="badge bg-light text-dark fs-6">
                                    <i class="fas fa-user-tag me-1"></i>
                                    <?= ucfirst($petugasData['level_name']) ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <div class="d-grid gap-2">
                                    <a href="edit.php?id=<?= $petugasData['id_user'] ?>" class="btn btn-light">
                                        <i class="fas fa-edit me-1"></i>
                                        Edit Data
                                    </a>
                                    <?php if ($petugasData['username'] !== 'admin'): ?>
                                    <button type="button" class="btn btn-outline-light" 
                                            onclick="deleteUser(<?= $petugasData['id_user'] ?>)">
                                        <i class="fas fa-trash me-1"></i>
                                        Hapus
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- User Details -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    INFORMASI PETUGAS
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>ID User:</strong></td>
                                        <td><?= htmlspecialchars($petugasData['id_user']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nama Lengkap:</strong></td>
                                        <td><?= htmlspecialchars($petugasData['nama_user']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Username:</strong></td>
                                        <td><?= htmlspecialchars($petugasData['username']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Level Akses:</strong></td>
                                        <td>
                                            <span class="badge bg-<?= $petugasData['level'] == 1 ? 'danger' : ($petugasData['level'] == 2 ? 'info' : 'warning') ?>">
                                                <?= ucfirst($petugasData['level_name']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Aktif
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Access Permissions -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    HAK AKSES SISTEM
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php
                                $permissions = [];
                                switch($petugasData['level']) {
                                    case 1: // Admin
                                        $permissions = [
                                            'Akses penuh ke semua fitur',
                                            'Kelola data petugas',
                                            'Kelola data customer dan item',
                                            'Proses transaksi penjualan',
                                            'Lihat dan cetak laporan',
                                            'Kelola pengaturan sistem'
                                        ];
                                        break;
                                    case 2: // Manager
                                        $permissions = [
                                            'Lihat dashboard dan statistik',
                                            'Kelola data customer dan item',
                                            'Lihat transaksi penjualan',
                                            'Lihat dan cetak laporan',
                                            'Monitor aktivitas sistem'
                                        ];
                                        break;
                                    case 3: // Kasir
                                        $permissions = [
                                            'Lihat dashboard terbatas',
                                            'Kelola data customer',
                                            'Kelola data item',
                                            'Proses transaksi penjualan',
                                            'Lihat riwayat transaksi'
                                        ];
                                        break;
                                }
                                ?>
                                
                                <ul class="list-unstyled">
                                    <?php foreach ($permissions as $permission): ?>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <?= $permission ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                
                                <?php if ($petugasData['username'] === 'admin'): ?>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-crown me-2"></i>
                                    <strong>Administrator Utama</strong><br>
                                    Akun ini memiliki akses penuh dan tidak dapat dihapus.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Kembali ke Daftar
                            </a>
                            <div>
                                <a href="edit.php?id=<?= $petugasData['id_user'] ?>" class="btn btn-warning me-2">
                                    <i class="fas fa-edit me-1"></i>
                                    Edit Data
                                </a>
                                <a href="create.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>
                                    Tambah Petugas Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deleteUser(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data petugas akan dihapus permanen!",
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

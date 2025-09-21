<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Petugas.php';

$auth = new Auth();
$auth->requireLogin();

$petugas = new Petugas();
$user = $auth->getCurrentUser();

$errors = [];
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_user = trim($_POST['nama_user'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($nama_user)) {
        $errors[] = 'Nama user harus diisi';
    }
    
    if (empty($username)) {
        $errors[] = 'Username harus diisi';
    }
    
    // Check if username is already taken by another user
    $existingUser = $petugas->getByUsername($username);
    if ($existingUser && $existingUser['id_user'] != $user['id_user']) {
        $errors[] = 'Username sudah digunakan oleh user lain';
    }
    
    // Password validation (only if user wants to change password)
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = 'Password lama harus diisi untuk mengubah password';
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Password lama tidak sesuai';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'Password baru minimal 6 karakter';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'Konfirmasi password tidak sesuai';
        }
    }
    
    if (empty($errors)) {
        try {
            $updateData = [
                'nama_user' => $nama_user,
                'username' => $username
            ];
            
            // Add password to update if user wants to change it
            if (!empty($new_password)) {
                $updateData['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }
            
            $petugas->update($user['id_user'], $updateData);
            $success = 'Profile berhasil diperbarui';
            
            // Refresh user data
            $user = $auth->getCurrentUser();
            
        } catch (Exception $e) {
            $errors[] = 'Gagal memperbarui profile: ' . $e->getMessage();
        }
    }
}

// Get user level name
$levelName = '';
switch($user['level']) {
    case 1: $levelName = 'Admin'; break;
    case 2: $levelName = 'Kasir'; break;
    case 3: $levelName = 'Manager'; break;
    default: $levelName = 'Unknown'; break;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Koperasi Pegawai RSUD Tarakan</title>
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
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 20px;
        }
        .level-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        .level-admin { background-color: #e74c3c; color: white; }
        .level-manager { background-color: #f39c12; color: white; }
        .level-kasir { background-color: #1cc88a; color: white; }
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
                            <a class="nav-link active" href="index.php">
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
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto">
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-user me-1"></i>
                                        <?= htmlspecialchars($user['nama_user']) ?>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="index.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="../../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Profile Saya</h1>
                </div>
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0">
                                <i class="fas fa-user me-2 text-primary"></i>
                                Profile Saya
                            </h2>
                            <p class="text-muted mb-0">Kelola informasi profile Anda</p>
                        </div>
                    </div>

                    <!-- Alerts -->
                    <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <!-- Profile Info Card -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <div class="profile-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <h5 class="card-title"><?= htmlspecialchars($user['nama_user']) ?></h5>
                                    <p class="text-muted">@<?= htmlspecialchars($user['username']) ?></p>
                                    <span class="level-badge level-<?= strtolower($levelName) ?>">
                                        <?= $levelName ?>
                                    </span>
                                    <hr>
                                    <div class="row text-center">
                                        <div class="col">
                                            <small class="text-muted">Member Since</small>
                                            <div class="fw-bold">
                                                <?= date('M Y', strtotime($user['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Profile Form -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-edit me-2"></i>
                                        Edit Profile
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="nama_user" class="form-label">Nama Lengkap</label>
                                                    <input type="text" class="form-control" id="nama_user" name="nama_user" 
                                                           value="<?= htmlspecialchars($user['nama_user']) ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Username</label>
                                                    <input type="text" class="form-control" id="username" name="username" 
                                                           value="<?= htmlspecialchars($user['username']) ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="level" class="form-label">Level Akses</label>
                                            <input type="text" class="form-control" value="<?= $levelName ?>" readonly>
                                            <small class="text-muted">Level akses tidak dapat diubah sendiri</small>
                                        </div>

                                        <hr>
                                        <h6 class="text-primary">
                                            <i class="fas fa-lock me-2"></i>
                                            Ubah Password (Opsional)
                                        </h6>
                                        <p class="text-muted small">Kosongkan jika tidak ingin mengubah password</p>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="current_password" class="form-label">Password Lama</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="new_password" class="form-label">Password Baru</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                Simpan Perubahan
                                            </button>
                                            <a href="../../dashboard.php" class="btn btn-secondary">
                                                <i class="fas fa-times me-2"></i>
                                                Batal
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const button = field.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>

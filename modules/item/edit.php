<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Item.php';

$auth = new Auth();
$auth->requireLogin();

$item = new Item();
$user = $auth->getCurrentUser();

$id = $_GET['id'] ?? 0;
$errors = [];
$success = '';

// Get item data
$itemData = $item->getById($id);
if (!$itemData) {
    header('Location: index.php');
    exit;
}

if ($_POST) {
    $data = [
        'nama_item' => $_POST['nama_item'] ?? '',
        'uom' => $_POST['uom'] ?? '',
        'harga_beli' => $_POST['harga_beli'] ?? 0,
        'harga_jual' => $_POST['harga_jual'] ?? 0,
        'stok' => $_POST['stok'] ?? 0
    ];
    
    $errors = $item->validate($data);
    
    if (empty($errors)) {
        try {
            $item->update($id, $data);
            $success = 'Item berhasil diupdate!';
            $itemData = array_merge($itemData, $data);
        } catch (Exception $e) {
            $errors[] = 'Gagal mengupdate item: ' . $e->getMessage();
        }
    }
} else {
    $data = $itemData;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item - Koperasi Pegawai RSUD Tarakan</title>
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
        .profit-preview {
            background-color: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
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
                            <a class="nav-link active" href="index.php">
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
                                <li class="breadcrumb-item"><a href="index.php">Item</a></li>
                                <li class="breadcrumb-item active">Edit Item</li>
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
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>
                            EDIT ITEM - ID: <?= htmlspecialchars($itemData['id_item']) ?>
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
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?= htmlspecialchars($success) ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="itemForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_item" class="form-label">Nama Item <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_item" name="nama_item" 
                                               value="<?= htmlspecialchars($data['nama_item']) ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="uom" class="form-label">Unit of Measurement (UOM) <span class="text-danger">*</span></label>
                                        <select class="form-select" id="uom" name="uom" required>
                                            <option value="">Pilih UOM</option>
                                            <option value="Unit" <?= $data['uom'] === 'Unit' ? 'selected' : '' ?>>Unit</option>
                                            <option value="Pcs" <?= $data['uom'] === 'Pcs' ? 'selected' : '' ?>>Pcs</option>
                                            <option value="Set" <?= $data['uom'] === 'Set' ? 'selected' : '' ?>>Set</option>
                                            <option value="Box" <?= $data['uom'] === 'Box' ? 'selected' : '' ?>>Box</option>
                                            <option value="Pack" <?= $data['uom'] === 'Pack' ? 'selected' : '' ?>>Pack</option>
                                            <option value="Kg" <?= $data['uom'] === 'Kg' ? 'selected' : '' ?>>Kg</option>
                                            <option value="Liter" <?= $data['uom'] === 'Liter' ? 'selected' : '' ?>>Liter</option>
                                            <option value="Meter" <?= $data['uom'] === 'Meter' ? 'selected' : '' ?>>Meter</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="stok" class="form-label">Stok</label>
                                        <input type="number" class="form-control" id="stok" name="stok" 
                                               value="<?= htmlspecialchars($data['stok']) ?>" min="0">
                                        <div class="form-text">Stok saat ini: <?= $itemData['stok'] ?> <?= $itemData['uom'] ?></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="harga_beli" class="form-label">Harga Beli <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="harga_beli" name="harga_beli" 
                                                   value="<?= htmlspecialchars($data['harga_beli']) ?>" min="0" step="0.01" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="harga_jual" class="form-label">Harga Jual <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="harga_jual" name="harga_jual" 
                                                   value="<?= htmlspecialchars($data['harga_jual']) ?>" min="0" step="0.01" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Dibuat pada</label>
                                        <input type="text" class="form-control" 
                                               value="<?= date('d/m/Y H:i', strtotime($itemData['created_at'])) ?>" readonly>
                                    </div>
                                    
                                    <div class="profit-preview" id="profitPreview">
                                        <h6 class="text-primary">Preview Keuntungan:</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <small class="text-muted">Keuntungan per unit:</small><br>
                                                <strong id="profitPerUnit">Rp <?= number_format($data['harga_jual'] - $data['harga_beli'], 0, ',', '.') ?></strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Margin (%):</small><br>
                                                <strong id="profitMargin"><?= $data['harga_beli'] > 0 ? number_format((($data['harga_jual'] - $data['harga_beli']) / $data['harga_beli']) * 100, 2) : 0 ?>%</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <a href="index.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Kembali
                                        </a>
                                        <div>
                                            <a href="view.php?id=<?= $itemData['id_item'] ?>" class="btn btn-info me-2">
                                                <i class="fas fa-eye me-2"></i>
                                                View Detail
                                            </a>
                                            <button type="submit" class="btn btn-warning">
                                                <i class="fas fa-save me-2"></i>
                                                Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calculate profit preview
        function calculateProfit() {
            const hargaBeli = parseFloat(document.getElementById('harga_beli').value) || 0;
            const hargaJual = parseFloat(document.getElementById('harga_jual').value) || 0;
            
            if (hargaBeli > 0 && hargaJual > 0) {
                const profit = hargaJual - hargaBeli;
                const margin = ((profit / hargaBeli) * 100).toFixed(2);
                
                document.getElementById('profitPerUnit').textContent = 'Rp ' + profit.toLocaleString('id-ID');
                document.getElementById('profitMargin').textContent = margin + '%';
                
                // Warning if selling price is lower than buying price
                if (hargaJual < hargaBeli) {
                    document.getElementById('profitPreview').className = 'profit-preview bg-danger text-white';
                    document.getElementById('profitPerUnit').parentElement.innerHTML = 
                        '<small class="text-white">Kerugian per unit:</small><br><strong id="profitPerUnit">Rp ' + 
                        Math.abs(profit).toLocaleString('id-ID') + '</strong>';
                } else {
                    document.getElementById('profitPreview').className = 'profit-preview';
                }
            }
        }
        
        // Add event listeners
        document.getElementById('harga_beli').addEventListener('input', calculateProfit);
        document.getElementById('harga_jual').addEventListener('input', calculateProfit);
        
        // Initial calculation
        calculateProfit();
    </script>
</body>
</html>

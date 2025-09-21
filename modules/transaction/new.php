<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Sales.php';
require_once __DIR__ . '/../../classes/Customer.php';
require_once __DIR__ . '/../../classes/Item.php';

$auth = new Auth();
$auth->requireLogin();

$sales = new Sales();
$customer = new Customer();
$item = new Item();
$user = $auth->getCurrentUser();

$errors = [];
$success = '';

// Get all customers and items for dropdowns
$customers = $customer->getAll('nama_customer ASC');
$items = $item->getAll('nama_item ASC');

if ($_POST) {
    $salesData = [
        'tgl_sales' => $_POST['tgl_sales'] ?? date('Y-m-d'),
        'id_customer' => $_POST['id_customer'] ?? '',
        'do_number' => $_POST['do_number'] ?? '',
        'status' => 'pending'
    ];
    
    $transactionItems = [];
    if (isset($_POST['items']) && is_array($_POST['items'])) {
        foreach ($_POST['items'] as $itemData) {
            if (!empty($itemData['id_item']) && !empty($itemData['quantity']) && !empty($itemData['price'])) {
                $transactionItems[] = [
                    'id_item' => $itemData['id_item'],
                    'quantity' => intval($itemData['quantity']),
                    'price' => floatval($itemData['price'])
                ];
            }
        }
    }
    
    $errors = $sales->validate($salesData);
    
    if (empty($transactionItems)) {
        $errors[] = 'Minimal harus ada satu item dalam transaksi';
    }
    
    // Validate stock availability
    foreach ($transactionItems as $transItem) {
        $itemData = $item->getById($transItem['id_item']);
        if ($itemData && $itemData['stok'] < $transItem['quantity']) {
            $errors[] = "Stok {$itemData['nama_item']} tidak mencukupi (tersedia: {$itemData['stok']})";
        }
    }
    
    if (empty($errors)) {
        try {
            // Generate DO number if not provided
            if (empty($salesData['do_number'])) {
                $salesData['do_number'] = $sales->generateDoNumber();
            }
            
            $salesId = $sales->createSalesWithTransactions($salesData, $transactionItems);
            $success = "Transaksi berhasil dibuat dengan ID: {$salesId}";
            
            // Clear form data
            $salesData = ['tgl_sales' => date('Y-m-d'), 'id_customer' => '', 'do_number' => ''];
            $transactionItems = [];
            
        } catch (Exception $e) {
            $errors[] = 'Gagal membuat transaksi: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Baru - Koperasi Pegawai RSUD Tarakan</title>
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
        .item-row {
            background-color: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
        }
        .total-summary {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
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
                            <a class="nav-link active" href="new.php">
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
                                <li class="breadcrumb-item">Transaction</li>
                                <li class="breadcrumb-item active">New Transaction</li>
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
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cash-register me-2"></i>
                            TRANSAKSI BARU
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
                            <a href="../sales/index.php" class="btn btn-sm btn-outline-success ms-3">
                                <i class="fas fa-list me-1"></i>Lihat Daftar Sales
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="transactionForm">
                            <!-- Sales Header -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="tgl_sales" class="form-label">Tanggal Sales <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="tgl_sales" name="tgl_sales" 
                                               value="<?= $salesData['tgl_sales'] ?? date('Y-m-d') ?>" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="id_customer" class="form-label">Customer <span class="text-danger">*</span></label>
                                        <select class="form-select" id="id_customer" name="id_customer" required>
                                            <option value="">Pilih Customer</option>
                                            <?php foreach ($customers as $cust): ?>
                                            <option value="<?= $cust['id_customer'] ?>" 
                                                    <?= (isset($salesData['id_customer']) && $salesData['id_customer'] == $cust['id_customer']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cust['nama_customer']) ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="do_number" class="form-label">DO Number</label>
                                        <input type="text" class="form-control" id="do_number" name="do_number" 
                                               value="<?= $salesData['do_number'] ?? '' ?>" 
                                               placeholder="Kosongkan untuk auto generate">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Items Section -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="mb-0">
                                                <i class="fas fa-shopping-basket me-2"></i>
                                                ITEM TRANSAKSI
                                            </h6>
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-primary btn-sm" onclick="addItemRow()">
                                                <i class="fas fa-plus me-1"></i>
                                                Tambah Item
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-body">
                                    <div id="itemsContainer">
                                        <!-- Item rows will be added here -->
                                    </div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4">
                                            <div class="total-summary">
                                                <div class="d-flex justify-content-between">
                                                    <span>Total Items:</span>
                                                    <strong id="totalItems">0</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>Total Quantity:</span>
                                                    <strong id="totalQuantity">0</strong>
                                                </div>
                                                <hr class="my-2">
                                                <div class="d-flex justify-content-between">
                                                    <span><strong>TOTAL AMOUNT:</strong></span>
                                                    <strong id="totalAmount">Rp 0</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <a href="../sales/index.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Kembali
                                        </a>
                                        <div>
                                            <button type="reset" class="btn btn-warning me-2" onclick="resetForm()">
                                                <i class="fas fa-undo me-2"></i>
                                                Reset
                                            </button>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-save me-2"></i>
                                                Simpan Transaksi
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
        let itemRowIndex = 0;
        const itemsData = <?= json_encode($items) ?>;
        
        function addItemRow() {
            const container = document.getElementById('itemsContainer');
            const rowHtml = `
                <div class="item-row" id="itemRow${itemRowIndex}">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Item</label>
                            <select class="form-select" name="items[${itemRowIndex}][id_item]" onchange="updateItemPrice(${itemRowIndex})" required>
                                <option value="">Pilih Item</option>
                                ${itemsData.map(item => `<option value="${item.id_item}" data-price="${item.harga_jual}" data-stock="${item.stok}" data-uom="${item.uom}">${item.nama_item} (Stok: ${item.stok} ${item.uom})</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="items[${itemRowIndex}][quantity]" min="1" onchange="calculateRowTotal(${itemRowIndex})" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" name="items[${itemRowIndex}][price]" min="0" step="0.01" onchange="calculateRowTotal(${itemRowIndex})" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control" id="rowTotal${itemRowIndex}" readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm d-block" onclick="removeItemRow(${itemRowIndex})">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', rowHtml);
            itemRowIndex++;
            calculateGrandTotal();
        }
        
        function removeItemRow(index) {
            const row = document.getElementById(`itemRow${index}`);
            if (row) {
                row.remove();
                calculateGrandTotal();
            }
        }
        
        function updateItemPrice(index) {
            const select = document.querySelector(`select[name="items[${index}][id_item]"]`);
            const priceInput = document.querySelector(`input[name="items[${index}][price]"]`);
            const quantityInput = document.querySelector(`input[name="items[${index}][quantity]"]`);
            
            if (select.selectedIndex > 0) {
                const option = select.options[select.selectedIndex];
                const price = option.getAttribute('data-price');
                const stock = option.getAttribute('data-stock');
                
                priceInput.value = price;
                quantityInput.max = stock;
                quantityInput.placeholder = `Max: ${stock}`;
                
                calculateRowTotal(index);
            }
        }
        
        function calculateRowTotal(index) {
            const quantity = parseFloat(document.querySelector(`input[name="items[${index}][quantity]"]`).value) || 0;
            const price = parseFloat(document.querySelector(`input[name="items[${index}][price]"]`).value) || 0;
            const total = quantity * price;
            
            document.getElementById(`rowTotal${index}`).value = 'Rp ' + total.toLocaleString('id-ID');
            calculateGrandTotal();
        }
        
        function calculateGrandTotal() {
            let totalItems = 0;
            let totalQuantity = 0;
            let totalAmount = 0;
            
            document.querySelectorAll('.item-row').forEach((row, index) => {
                const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
                const price = parseFloat(row.querySelector('input[name*="[price]"]').value) || 0;
                
                if (quantity > 0 && price > 0) {
                    totalItems++;
                    totalQuantity += quantity;
                    totalAmount += quantity * price;
                }
            });
            
            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalQuantity').textContent = totalQuantity;
            document.getElementById('totalAmount').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
        }
        
        function resetForm() {
            document.getElementById('itemsContainer').innerHTML = '';
            itemRowIndex = 0;
            calculateGrandTotal();
        }
        
        // Add initial item row
        document.addEventListener('DOMContentLoaded', function() {
            addItemRow();
        });
        
        // Form validation
        document.getElementById('transactionForm').addEventListener('submit', function(e) {
            const itemRows = document.querySelectorAll('.item-row');
            if (itemRows.length === 0) {
                e.preventDefault();
                alert('Minimal harus ada satu item dalam transaksi!');
                return false;
            }
            
            let hasValidItem = false;
            itemRows.forEach(row => {
                const itemSelect = row.querySelector('select[name*="[id_item]"]');
                const quantity = row.querySelector('input[name*="[quantity]"]');
                const price = row.querySelector('input[name*="[price]"]');
                
                if (itemSelect.value && quantity.value && price.value) {
                    hasValidItem = true;
                }
            });
            
            if (!hasValidItem) {
                e.preventDefault();
                alert('Minimal harus ada satu item yang lengkap datanya!');
                return false;
            }
        });
    </script>
</body>
</html>

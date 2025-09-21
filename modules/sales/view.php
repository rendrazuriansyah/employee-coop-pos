<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Sales.php';

$auth = new Auth();
$auth->requireLogin();

$sales = new Sales();
$user = $auth->getCurrentUser();

$id = $_GET['id'] ?? 0;

// Get sales detail with transactions
$salesDetail = $sales->getSalesDetail($id);
if (!$salesDetail) {
    header('Location: index.php');
    exit;
}

$salesData = $salesDetail['sales'];
$transactions = $salesDetail['transactions'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Sales - Koperasi Pegawai RSUD Tarakan</title>
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
        .invoice-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px 10px 0 0;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
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
                                <li class="breadcrumb-item active">Detail Sales</li>
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
                
                <!-- Invoice Header -->
                <div class="card">
                    <div class="invoice-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h3><i class="fas fa-receipt me-2"></i>INVOICE</h3>
                                <p class="mb-0">Koperasi Pegawai RSUD Tarakan</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <h4>Sales #<?= htmlspecialchars($salesData['id_sales']) ?></h4>
                                <p class="mb-0">DO: <?= htmlspecialchars($salesData['do_number'] ?: '-') ?></p>
                                <p class="mb-0"><?= date('d F Y', strtotime($salesData['tgl_sales'])) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Customer & Sales Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-primary">CUSTOMER INFORMATION</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="info-label" width="30%">Customer:</td>
                                        <td class="info-value"><?= htmlspecialchars($salesData['nama_customer']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Alamat:</td>
                                        <td class="info-value"><?= htmlspecialchars($salesData['alamat'] ?: '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Telepon:</td>
                                        <td class="info-value"><?= htmlspecialchars($salesData['telp'] ?: '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Email:</td>
                                        <td class="info-value"><?= htmlspecialchars($salesData['email'] ?: '-') ?></td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-primary">SALES INFORMATION</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="info-label" width="30%">Sales ID:</td>
                                        <td class="info-value"><?= htmlspecialchars($salesData['id_sales']) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">DO Number:</td>
                                        <td class="info-value"><?= htmlspecialchars($salesData['do_number'] ?: '-') ?></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Tanggal:</td>
                                        <td class="info-value"><?= date('d/m/Y', strtotime($salesData['tgl_sales'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Status:</td>
                                        <td class="info-value">
                                            <?php
                                            $statusClass = 'status-pending';
                                            $statusText = 'Pending';
                                            
                                            switch($salesData['status']) {
                                                case 'completed':
                                                    $statusClass = 'status-completed';
                                                    $statusText = 'Selesai';
                                                    break;
                                                case 'cancelled':
                                                    $statusClass = 'status-cancelled';
                                                    $statusText = 'Dibatalkan';
                                                    break;
                                            }
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Transaction Items -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="35%">Item</th>
                                        <th width="10%">UOM</th>
                                        <th width="12%">Quantity</th>
                                        <th width="15%">Harga</th>
                                        <th width="18%">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $grandTotal = 0;
                                    $totalQuantity = 0;
                                    ?>
                                    <?php foreach ($transactions as $index => $trans): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($trans['nama_item']) ?></td>
                                        <td><?= htmlspecialchars($trans['uom']) ?></td>
                                        <td class="text-center"><?= $trans['quantity'] ?></td>
                                        <td class="text-end">Rp <?= number_format($trans['price'], 0, ',', '.') ?></td>
                                        <td class="text-end">Rp <?= number_format($trans['amount'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php 
                                    $grandTotal += $trans['amount'];
                                    $totalQuantity += $trans['quantity'];
                                    ?>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">TOTAL:</th>
                                        <th class="text-center"><?= $totalQuantity ?></th>
                                        <th></th>
                                        <th class="text-end">Rp <?= number_format($grandTotal, 0, ',', '.') ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>
                                        Kembali
                                    </a>
                                    <div>
                                        <a href="print.php?id=<?= $salesData['id_sales'] ?>" target="_blank" class="btn btn-info me-2">
                                            <i class="fas fa-print me-2"></i>
                                            Print Invoice
                                        </a>
                                        
                                        <?php if ($salesData['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-success me-2" onclick="updateStatus(<?= $salesData['id_sales'] ?>, 'completed')">
                                            <i class="fas fa-check me-2"></i>
                                            Mark Completed
                                        </button>
                                        <button type="button" class="btn btn-warning me-2" onclick="updateStatus(<?= $salesData['id_sales'] ?>, 'cancelled')">
                                            <i class="fas fa-times me-2"></i>
                                            Cancel
                                        </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($auth->hasPermission(1)): ?>
                                        <button type="button" class="btn btn-danger" onclick="deleteSales(<?= $salesData['id_sales'] ?>)">
                                            <i class="fas fa-trash me-2"></i>
                                            Delete
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
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
        function updateStatus(id, status) {
            const statusText = status === 'completed' ? 'selesai' : 'dibatalkan';
            
            Swal.fire({
                title: 'Konfirmasi',
                text: `Apakah Anda yakin ingin menandai transaksi ini sebagai ${statusText}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: status === 'completed' ? '#28a745' : '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Update!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `update_status.php?id=${id}&status=${status}`;
                }
            });
        }
        
        function deleteSales(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data sales dan semua transaksi terkait akan dihapus permanen!",
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
        
        // Print styles
        window.addEventListener('beforeprint', function() {
            document.querySelector('.sidebar').style.display = 'none';
            document.querySelector('.navbar-custom').style.display = 'none';
            document.querySelector('.main-content').style.margin = '0';
            document.querySelector('.main-content').style.padding = '20px';
        });
        
        window.addEventListener('afterprint', function() {
            location.reload();
        });
    </script>
</body>
</html>

<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Sales.php';
require_once __DIR__ . '/../../classes/Customer.php';
require_once __DIR__ . '/../../config/Database.php';

$auth = new Auth();
$auth->requireLogin();

$sales = new Sales();
$customer = new Customer();

// Get sales ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Get sales detail with transactions
$salesDetail = $sales->getSalesDetail($id);
if (!$salesDetail) {
    header('Location: index.php');
    exit;
}

$salesData = $salesDetail['sales'];
$transactions = $salesDetail['transactions'];


// Customer data is already included in salesDetail
$customerData = [
    'id_customer' => $salesData['id_customer'],
    'nama_customer' => $salesData['nama_customer'],
    'alamat' => $salesData['alamat'],
    'telp' => $salesData['telp'],
    'email' => $salesData['email']
];

// Get company identity
$db = Database::getInstance();
$company = $db->fetchOne("SELECT * FROM identitas LIMIT 1");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?= htmlspecialchars($salesData['do_number']) ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.3;
        }
        
        .invoice-title {
            text-align: right;
            flex: 1;
        }
        
        .invoice-title h1 {
            font-size: 24px;
            color: #e74c3c;
            margin: 0;
            font-weight: bold;
        }
        
        .invoice-number {
            font-size: 14px;
            color: #333;
            margin-top: 5px;
        }
        
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .bill-to, .invoice-info {
            flex: 1;
        }
        
        .bill-to {
            margin-right: 40px;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 13px;
            color: #2c3e50;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #bdc3c7;
            padding-bottom: 5px;
        }
        
        .customer-info, .invoice-meta {
            font-size: 12px;
            line-height: 1.5;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        
        .items-table th {
            background-color: #34495e;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .total-section {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }
        
        .total-table {
            width: 300px;
            font-size: 12px;
        }
        
        .total-table td {
            padding: 8px 12px;
            border: none;
        }
        
        .total-table .total-label {
            text-align: right;
            font-weight: bold;
            background-color: #ecf0f1;
        }
        
        .total-table .total-amount {
            text-align: right;
            background-color: #ecf0f1;
        }
        
        .grand-total {
            background-color: #2c3e50 !important;
            color: white !important;
            font-weight: bold;
            font-size: 14px;
        }
        
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #bdc3c7;
            font-size: 11px;
            color: #666;
            text-align: center;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .print-button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .print-button:hover {
            background-color: #2980b9;
        }
        
        .back-button {
            background-color: #95a5a6;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            margin-left: 10px;
        }
        
        .back-button:hover {
            background-color: #7f8c8d;
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="print-button">🖨️ Print Invoice</button>
        <a href="index.php" class="back-button">← Kembali ke Sales</a>
    </div>

    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="company-info">
                <div class="company-name"><?= htmlspecialchars($company['nama_identitas'] ?? 'Koperasi Pegawai RSUD Tarakan') ?></div>
                <div class="company-details">
                    <?= htmlspecialchars($company['alamat'] ?? 'Jl. Pulau Kalimantan No. 1, Tarakan') ?><br>
                    Telp: <?= htmlspecialchars($company['telp'] ?? '0551-21234') ?><br>
                    <?php if (!empty($company['email'])): ?>
                    Email: <?= htmlspecialchars($company['email']) ?><br>
                    <?php endif; ?>
                    <?php if (!empty($company['npwp'])): ?>
                    NPWP: <?= htmlspecialchars($company['npwp']) ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <div class="invoice-number"><?= htmlspecialchars($salesData['do_number']) ?></div>
                <div class="status-badge status-<?= $salesData['status'] ?>">
                    <?= ucfirst($salesData['status']) ?>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="bill-to">
                <div class="section-title">Bill To:</div>
                <div class="customer-info">
                    <strong><?= htmlspecialchars($customerData['nama_customer']) ?></strong><br>
                    <?= nl2br(htmlspecialchars($customerData['alamat'])) ?><br>
                    <?php if (!empty($customerData['telp'])): ?>
                    Telp: <?= htmlspecialchars($customerData['telp']) ?><br>
                    <?php endif; ?>
                    <?php if (!empty($customerData['email'])): ?>
                    Email: <?= htmlspecialchars($customerData['email']) ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="invoice-info">
                <div class="section-title">Invoice Info:</div>
                <div class="invoice-meta">
                    <strong>Invoice Date:</strong> <?= date('d F Y', strtotime($salesData['tgl_sales'])) ?><br>
                    <strong>Due Date:</strong> <?= date('d F Y', strtotime($salesData['tgl_sales'] . ' +30 days')) ?><br>
                    <strong>Created:</strong> <?= date('d F Y H:i', strtotime($salesData['created_at'])) ?><br>
                    <strong>Customer ID:</strong> #<?= str_pad($customerData['id_customer'], 4, '0', STR_PAD_LEFT) ?>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 45%">Item Description</th>
                    <th style="width: 10%" class="text-center">UOM</th>
                    <th style="width: 10%" class="text-center">Qty</th>
                    <th style="width: 15%" class="text-right">Unit Price</th>
                    <th style="width: 15%" class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $subtotal = 0;
                foreach ($transactions as $item): 
                    $subtotal += $item['amount'];
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td>
                        <strong><?= htmlspecialchars($item['nama_item']) ?></strong>
                        <?php if (!empty($item['description'])): ?>
                        <br><small style="color: #666;"><?= htmlspecialchars($item['description']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= htmlspecialchars($item['uom']) ?></td>
                    <td class="text-center"><?= number_format($item['quantity']) ?></td>
                    <td class="text-right">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($item['amount'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #666;">
                        No items found for this invoice.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Total Section -->
        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td class="total-label">Subtotal:</td>
                    <td class="total-amount">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td class="total-label">Tax (0%):</td>
                    <td class="total-amount">Rp 0</td>
                </tr>
                <tr>
                    <td class="total-label">Discount:</td>
                    <td class="total-amount">Rp 0</td>
                </tr>
                <tr class="grand-total">
                    <td class="total-label">TOTAL:</td>
                    <td class="total-amount">Rp <?= number_format($salesData['total_amount'], 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>

        <!-- Invoice Footer -->
        <div class="invoice-footer">
            <p><strong>Terms & Conditions:</strong></p>
            <p>Payment is due within 30 days from invoice date. Late payments may incur additional charges.<br>
            Please include invoice number when making payment. Thank you for your business!</p>
            
            <p style="margin-top: 20px; font-size: 10px; color: #999;">
                This is a computer-generated invoice. No signature required.<br>
                Generated on <?= date('d F Y H:i:s') ?> | Invoice ID: <?= $salesData['id_sales'] ?>
            </p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
        
        // Print function
        function printInvoice() {
            window.print();
        }
        
        // Keyboard shortcut for printing
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>

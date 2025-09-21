<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Sales.php';

$auth = new Auth();
$auth->requireLogin();

// Only admin can delete sales
if (!$auth->hasPermission(1)) {
    header('Location: index.php?error=Anda tidak memiliki akses untuk menghapus data sales');
    exit;
}

$sales = new Sales();
$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        $sales->deleteSalesWithTransactions($id);
        header('Location: index.php?success=Sales berhasil dihapus');
        exit;
        
    } catch (Exception $e) {
        header('Location: index.php?error=Gagal menghapus sales: ' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>

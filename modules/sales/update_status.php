<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Sales.php';

$auth = new Auth();
$auth->requireLogin();

$sales = new Sales();
$id = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? '';

if ($id && $status) {
    try {
        $sales->updateSalesStatus($id, $status);
        
        $statusText = '';
        switch($status) {
            case 'completed':
                $statusText = 'selesai';
                break;
            case 'cancelled':
                $statusText = 'dibatalkan';
                break;
            case 'pending':
                $statusText = 'pending';
                break;
        }
        
        header("Location: view.php?id={$id}&success=Status sales berhasil diubah menjadi {$statusText}");
        exit;
        
    } catch (Exception $e) {
        header("Location: view.php?id={$id}&error=Gagal mengubah status: " . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>

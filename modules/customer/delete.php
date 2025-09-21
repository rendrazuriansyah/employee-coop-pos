<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Customer.php';

$auth = new Auth();
$auth->requireLogin();

$customer = new Customer();
$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Check if customer has any sales records
        $db = Database::getInstance();
        $salesCount = $db->fetchOne("SELECT COUNT(*) as count FROM sales WHERE id_customer = ?", [$id])['count'];
        
        if ($salesCount > 0) {
            // Customer has sales records, cannot delete
            header('Location: index.php?error=Customer memiliki riwayat transaksi dan tidak dapat dihapus');
            exit;
        }
        
        // Safe to delete
        $customer->delete($id);
        header('Location: index.php?success=Customer berhasil dihapus');
        exit;
        
    } catch (Exception $e) {
        header('Location: index.php?error=Gagal menghapus customer: ' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>

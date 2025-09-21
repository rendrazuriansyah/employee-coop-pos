<?php
require_once __DIR__ . '/../../classes/Auth.php';
require_once __DIR__ . '/../../classes/Item.php';

$auth = new Auth();
$auth->requireLogin();

$item = new Item();
$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Check if item has any transaction records
        $db = Database::getInstance();
        $transactionCount = $db->fetchOne("SELECT COUNT(*) as count FROM transaction WHERE id_item = ?", [$id])['count'];
        
        if ($transactionCount > 0) {
            // Item has transaction records, cannot delete
            header('Location: index.php?error=Item memiliki riwayat transaksi dan tidak dapat dihapus');
            exit;
        }
        
        // Safe to delete
        $item->delete($id);
        header('Location: index.php?success=Item berhasil dihapus');
        exit;
        
    } catch (Exception $e) {
        header('Location: index.php?error=Gagal menghapus item: ' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>

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
$id = $_GET['id'] ?? 0;

if ($id) {
    try {
        // Check if user can be deleted
        if (!$petugas->canDeleteUser($id)) {
            header('Location: index.php?error=Tidak dapat menghapus administrator utama');
            exit;
        }
        
        $petugas->delete($id);
        header('Location: index.php?success=Petugas berhasil dihapus');
        exit;
        
    } catch (Exception $e) {
        header('Location: index.php?error=Gagal menghapus petugas: ' . urlencode($e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>

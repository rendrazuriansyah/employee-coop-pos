<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../classes/Auth.php';
require_once __DIR__ . '/../config/Database.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance();
    
    $recentTransactions = $db->fetchAll("
        SELECT s.id_sales, s.tgl_sales, s.status, s.total_amount, c.nama_customer
        FROM sales s
        JOIN customer c ON s.id_customer = c.id_customer
        ORDER BY s.created_at DESC
        LIMIT 5
    ");
    
    echo json_encode($recentTransactions);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>

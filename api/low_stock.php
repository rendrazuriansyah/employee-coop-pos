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
    
    $lowStockItems = $db->fetchAll("
        SELECT nama_item, stok, uom
        FROM item
        WHERE stok <= 5
        ORDER BY stok ASC
        LIMIT 10
    ");
    
    echo json_encode($lowStockItems);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>

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
    
    // Get total customers
    $totalCustomers = $db->fetchOne("SELECT COUNT(*) as count FROM customer")['count'];
    
    // Get monthly sales (current month)
    $monthlySales = $db->fetchOne("
        SELECT COALESCE(SUM(total_amount), 0) as total 
        FROM sales 
        WHERE MONTH(tgl_sales) = MONTH(CURRENT_DATE()) 
        AND YEAR(tgl_sales) = YEAR(CURRENT_DATE())
        AND status = 'completed'
    ")['total'];
    
    // Get total items
    $totalItems = $db->fetchOne("SELECT COUNT(*) as count FROM item")['count'];
    
    // Get daily transactions (today)
    $dailyTransactions = $db->fetchOne("
        SELECT COUNT(*) as count 
        FROM sales 
        WHERE DATE(tgl_sales) = CURRENT_DATE()
    ")['count'];
    
    echo json_encode([
        'total_customers' => $totalCustomers,
        'monthly_sales' => number_format($monthlySales, 0, ',', '.'),
        'total_items' => $totalItems,
        'daily_transactions' => $dailyTransactions
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?>

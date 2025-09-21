<?php
require_once 'BaseCRUD.php';

/**
 * Customer Class
 * Handles customer data operations
 */
class Customer extends BaseCRUD {
    
    public function __construct() {
        parent::__construct('customer', 'id_customer');
    }
    
    public function validate($data) {
        $errors = [];
        
        if (empty($data['nama_customer'])) {
            $errors[] = 'Nama customer harus diisi';
        }
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }
        
        return $errors;
    }
    
    public function searchCustomers($keyword) {
        $sql = "SELECT * FROM customer 
                WHERE nama_customer LIKE ? 
                OR alamat LIKE ? 
                OR email LIKE ?
                ORDER BY nama_customer ASC";
        
        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function getCustomerWithSalesCount() {
        $sql = "SELECT c.*, 
                COUNT(s.id_sales) as total_sales,
                COALESCE(SUM(s.total_amount), 0) as total_amount
                FROM customer c
                LEFT JOIN sales s ON c.id_customer = s.id_customer
                GROUP BY c.id_customer
                ORDER BY c.nama_customer ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    public function getCustomerSales($customerId) {
        $sql = "SELECT s.*, 
                COUNT(t.id_transaction) as total_items
                FROM sales s
                LEFT JOIN transaction t ON s.id_sales = t.id_sales
                WHERE s.id_customer = ?
                GROUP BY s.id_sales
                ORDER BY s.tgl_sales DESC";
        
        return $this->db->fetchAll($sql, [$customerId]);
    }
}
?>

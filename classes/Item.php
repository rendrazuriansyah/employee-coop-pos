<?php
require_once 'BaseCRUD.php';

/**
 * Item Class
 * Handles item/product data operations with inventory management
 */
class Item extends BaseCRUD {
    
    public function __construct() {
        parent::__construct('item', 'id_item');
    }
    
    public function validate($data) {
        $errors = [];
        
        if (empty($data['nama_item'])) {
            $errors[] = 'Nama item harus diisi';
        }
        
        if (empty($data['uom'])) {
            $errors[] = 'Unit of measurement (UOM) harus diisi';
        }
        
        if (empty($data['harga_beli']) || !is_numeric($data['harga_beli']) || $data['harga_beli'] < 0) {
            $errors[] = 'Harga beli harus berupa angka positif';
        }
        
        if (empty($data['harga_jual']) || !is_numeric($data['harga_jual']) || $data['harga_jual'] < 0) {
            $errors[] = 'Harga jual harus berupa angka positif';
        }
        
        if (isset($data['harga_beli']) && isset($data['harga_jual']) && 
            is_numeric($data['harga_beli']) && is_numeric($data['harga_jual']) &&
            $data['harga_jual'] < $data['harga_beli']) {
            $errors[] = 'Harga jual tidak boleh lebih kecil dari harga beli';
        }
        
        if (isset($data['stok']) && (!is_numeric($data['stok']) || $data['stok'] < 0)) {
            $errors[] = 'Stok harus berupa angka positif';
        }
        
        return $errors;
    }
    
    public function searchItems($keyword) {
        $sql = "SELECT * FROM item 
                WHERE nama_item LIKE ? 
                OR uom LIKE ?
                ORDER BY nama_item ASC";
        
        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }
    
    public function getLowStockItems($threshold = 5) {
        $sql = "SELECT * FROM item 
                WHERE stok <= ? 
                ORDER BY stok ASC, nama_item ASC";
        
        return $this->db->fetchAll($sql, [$threshold]);
    }
    
    public function updateStock($itemId, $quantity, $operation = 'add') {
        $item = $this->getById($itemId);
        if (!$item) {
            throw new Exception('Item tidak ditemukan');
        }
        
        $newStock = $item['stok'];
        if ($operation === 'add') {
            $newStock += $quantity;
        } elseif ($operation === 'subtract') {
            $newStock -= $quantity;
            if ($newStock < 0) {
                throw new Exception('Stok tidak mencukupi');
            }
        }
        
        return $this->update($itemId, ['stok' => $newStock]);
    }
    
    public function getItemsWithTransactionCount() {
        $sql = "SELECT i.*, 
                COUNT(t.id_transaction) as total_transactions,
                COALESCE(SUM(t.quantity), 0) as total_sold
                FROM item i
                LEFT JOIN transaction t ON i.id_item = t.id_item
                GROUP BY i.id_item
                ORDER BY i.nama_item ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    public function getItemTransactions($itemId) {
        $sql = "SELECT t.*, s.tgl_sales, s.do_number, c.nama_customer
                FROM transaction t
                JOIN sales s ON t.id_sales = s.id_sales
                JOIN customer c ON s.id_customer = c.id_customer
                WHERE t.id_item = ?
                ORDER BY s.tgl_sales DESC";
        
        return $this->db->fetchAll($sql, [$itemId]);
    }
    
    public function calculateProfit($itemId) {
        $sql = "SELECT 
                    SUM(t.quantity * (t.price - i.harga_beli)) as total_profit,
                    SUM(t.quantity) as total_sold
                FROM transaction t
                JOIN item i ON t.id_item = i.id_item
                WHERE t.id_item = ?";
        
        return $this->db->fetchOne($sql, [$itemId]);
    }
}
?>

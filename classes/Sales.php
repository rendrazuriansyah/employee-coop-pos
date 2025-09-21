<?php
require_once 'BaseCRUD.php';

/**
 * Sales Class
 * Handles sales data operations and transaction management
 */
class Sales extends BaseCRUD {
    
    public function __construct() {
        parent::__construct('sales', 'id_sales');
    }
    
    public function validate($data) {
        $errors = [];
        
        if (empty($data['tgl_sales'])) {
            $errors[] = 'Tanggal sales harus diisi';
        }
        
        if (empty($data['id_customer'])) {
            $errors[] = 'Customer harus dipilih';
        }
        
        if (!empty($data['do_number'])) {
            // Check if DO number already exists (for updates, exclude current record)
            $existingId = isset($data['current_id']) ? $data['current_id'] : 0;
            $existing = $this->db->fetchOne(
                "SELECT id_sales FROM sales WHERE do_number = ? AND id_sales != ?", 
                [$data['do_number'], $existingId]
            );
            if ($existing) {
                $errors[] = 'DO Number sudah digunakan';
            }
        }
        
        return $errors;
    }
    
    public function createSalesWithTransactions($salesData, $transactionItems) {
        try {
            $this->db->beginTransaction();
            
            // Create sales record
            $salesId = $this->create($salesData);
            
            // Create transaction records
            $totalAmount = 0;
            foreach ($transactionItems as $item) {
                $transactionData = [
                    'id_sales' => $salesId,
                    'id_item' => $item['id_item'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'amount' => $item['quantity'] * $item['price']
                ];
                
                $this->db->insert('transaction', $transactionData);
                $totalAmount += $transactionData['amount'];
                
                // Update item stock
                $this->db->query(
                    "UPDATE item SET stok = stok - ? WHERE id_item = ?",
                    [$item['quantity'], $item['id_item']]
                );
            }
            
            // Update sales total amount
            $this->update($salesId, ['total_amount' => $totalAmount]);
            
            $this->db->commit();
            return $salesId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function getSalesWithCustomer($limit = null) {
        $sql = "SELECT s.*, c.nama_customer, 
                COUNT(t.id_transaction) as total_items
                FROM sales s
                JOIN customer c ON s.id_customer = c.id_customer
                LEFT JOIN transaction t ON s.id_sales = t.id_sales
                GROUP BY s.id_sales
                ORDER BY s.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        
        return $this->db->fetchAll($sql);
    }
    
    public function getSalesDetail($salesId) {
        $sales = $this->db->fetchOne(
            "SELECT s.*, c.nama_customer, c.alamat, c.telp, c.email
             FROM sales s
             JOIN customer c ON s.id_customer = c.id_customer
             WHERE s.id_sales = ?",
            [$salesId]
        );
        
        if (!$sales) {
            return null;
        }
        
        $transactions = $this->db->fetchAll(
            "SELECT t.*, i.nama_item, i.uom
             FROM transaction t
             JOIN item i ON t.id_item = i.id_item
             WHERE t.id_sales = ?
             ORDER BY t.id_transaction",
            [$salesId]
        );
        
        return [
            'sales' => $sales,
            'transactions' => $transactions
        ];
    }
    
    public function searchSales($keyword) {
        $sql = "SELECT s.*, c.nama_customer
                FROM sales s
                JOIN customer c ON s.id_customer = c.id_customer
                WHERE s.do_number LIKE ? 
                OR c.nama_customer LIKE ?
                OR s.id_sales LIKE ?
                ORDER BY s.created_at DESC";
        
        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
    
    public function updateSalesStatus($salesId, $status) {
        $validStatuses = ['pending', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Status tidak valid');
        }
        
        return $this->update($salesId, ['status' => $status]);
    }
    
    public function getSalesReport($startDate = null, $endDate = null, $customerId = null) {
        $where = [];
        $params = [];
        
        if ($startDate) {
            $where[] = "s.tgl_sales >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $where[] = "s.tgl_sales <= ?";
            $params[] = $endDate;
        }
        
        if ($customerId) {
            $where[] = "s.id_customer = ?";
            $params[] = $customerId;
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $sql = "SELECT s.*, c.nama_customer,
                COUNT(t.id_transaction) as total_items,
                SUM(t.quantity) as total_quantity
                FROM sales s
                JOIN customer c ON s.id_customer = c.id_customer
                LEFT JOIN transaction t ON s.id_sales = t.id_sales
                {$whereClause}
                GROUP BY s.id_sales
                ORDER BY s.tgl_sales DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function generateDoNumber() {
        $date = date('Ymd');
        $lastDo = $this->db->fetchOne(
            "SELECT do_number FROM sales 
             WHERE do_number LIKE ? 
             ORDER BY do_number DESC LIMIT 1",
            ["DO{$date}%"]
        );
        
        if ($lastDo) {
            $lastNumber = intval(substr($lastDo['do_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return "DO{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
    
    public function deleteSalesWithTransactions($salesId) {
        try {
            $this->db->beginTransaction();
            
            // Get transaction items to restore stock
            $transactions = $this->db->fetchAll(
                "SELECT id_item, quantity FROM transaction WHERE id_sales = ?",
                [$salesId]
            );
            
            // Restore item stock
            foreach ($transactions as $trans) {
                $this->db->query(
                    "UPDATE item SET stok = stok + ? WHERE id_item = ?",
                    [$trans['quantity'], $trans['id_item']]
                );
            }
            
            // Delete transactions first (foreign key constraint)
            $this->db->delete('transaction', 'id_sales = ?', [$salesId]);
            
            // Delete sales record
            $this->delete($salesId);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}
?>

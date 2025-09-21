<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Base CRUD Class
 * Provides common CRUD operations for all modules
 */
abstract class BaseCRUD {
    protected $db;
    protected $table;
    protected $primaryKey;
    
    public function __construct($table, $primaryKey = 'id') {
        $this->db = Database::getInstance();
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }
    
    public function getAll($orderBy = null, $limit = null) {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->db->fetchAll($sql);
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function create($data) {
        return $this->db->insert($this->table, $data);
    }
    
    public function update($id, $data) {
        $where = "{$this->primaryKey} = :id";
        $whereParams = ['id' => $id];
        return $this->db->update($this->table, $data, $where, $whereParams);
    }
    
    public function delete($id) {
        $where = "{$this->primaryKey} = ?";
        return $this->db->delete($this->table, $where, [$id]);
    }
    
    public function search($column, $value) {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} LIKE ?";
        return $this->db->fetchAll($sql, ["%{$value}%"]);
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        return $this->db->fetchOne($sql)['total'];
    }
    
    public function paginate($page = 1, $perPage = 10, $search = null, $searchColumn = null) {
        $offset = ($page - 1) * $perPage;
        
        $whereClause = '';
        $params = [];
        
        if ($search && $searchColumn) {
            $whereClause = "WHERE {$searchColumn} LIKE ?";
            $params[] = "%{$search}%";
        }
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $total = $this->db->fetchOne($countSql, $params)['total'];
        
        // Get data
        $dataSql = "SELECT * FROM {$this->table} {$whereClause} LIMIT {$perPage} OFFSET {$offset}";
        $data = $this->db->fetchAll($dataSql, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }
}
?>

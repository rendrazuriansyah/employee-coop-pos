<?php
require_once 'BaseCRUD.php';

/**
 * Petugas Class
 * Handles user management operations (admin only)
 */
class Petugas extends BaseCRUD {
    
    public function __construct() {
        parent::__construct('petugas', 'id_user');
    }
    
    public function validate($data) {
        $errors = [];
        
        if (empty($data['nama_user'])) {
            $errors[] = 'Nama user harus diisi';
        }
        
        if (empty($data['username'])) {
            $errors[] = 'Username harus diisi';
        } else {
            // Check if username already exists (for updates, exclude current record)
            $existingId = isset($data['current_id']) ? $data['current_id'] : 0;
            $existing = $this->db->fetchOne(
                "SELECT id_user FROM petugas WHERE username = ? AND id_user != ?", 
                [$data['username'], $existingId]
            );
            if ($existing) {
                $errors[] = 'Username sudah digunakan';
            }
        }
        
        if (empty($data['password']) && !isset($data['current_id'])) {
            $errors[] = 'Password harus diisi';
        }
        
        if (empty($data['level'])) {
            $errors[] = 'Level harus dipilih';
        }
        
        return $errors;
    }
    
    public function createUser($data) {
        // Hash password before storing
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return $this->create($data);
    }
    
    public function updateUser($id, $data) {
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Remove password from data if empty (don't update password)
            unset($data['password']);
        }
        
        return $this->update($id, $data);
    }
    
    public function getUsersWithLevel() {
        $sql = "SELECT p.*, l.level as level_name
                FROM petugas p
                JOIN level l ON p.level = l.id_level
                ORDER BY p.nama_user ASC";
        
        return $this->db->fetchAll($sql);
    }
    
    public function getAllLevels() {
        return $this->db->fetchAll("SELECT * FROM level ORDER BY id_level ASC");
    }
    
    public function searchUsers($keyword) {
        $sql = "SELECT p.*, l.level as level_name
                FROM petugas p
                JOIN level l ON p.level = l.id_level
                WHERE p.nama_user LIKE ? 
                OR p.username LIKE ?
                ORDER BY p.nama_user ASC";
        
        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }
    
    public function getUserWithLevel($userId) {
        $sql = "SELECT p.*, l.level as level_name
                FROM petugas p
                JOIN level l ON p.level = l.id_level
                WHERE p.id_user = ?";
        
        return $this->db->fetchOne($sql, [$userId]);
    }
    
    public function canDeleteUser($userId) {
        // Check if user has any related records that would prevent deletion
        // For now, we'll allow deletion of any user except the main admin
        $user = $this->getById($userId);
        
        if ($user && $user['username'] === 'admin') {
            return false; // Cannot delete main admin
        }
        
        return true;
    }
}
?>

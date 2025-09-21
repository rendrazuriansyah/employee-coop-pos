<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Authentication Class
 * Handles user login, logout, and session management
 */
class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function login($username, $password) {
        try {
            // Check in petugas table first
            $user = $this->db->fetchOne(
                "SELECT p.*, l.level as level_name 
                 FROM petugas p 
                 JOIN level l ON p.level = l.id_level 
                 WHERE p.username = ?", 
                [$username]
            );
            
            // If not found in petugas, check manager table
            if (!$user) {
                $user = $this->db->fetchOne(
                    "SELECT m.*, l.level as level_name 
                     FROM manager m 
                     JOIN level l ON m.level = l.id_level 
                     WHERE m.username = ?", 
                    [$username]
                );
            }
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_user'] = $user['nama_user'];
                $_SESSION['level'] = $user['level'];
                $_SESSION['level_name'] = $user['level_name'];
                $_SESSION['logged_in'] = true;
                
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    public function logout() {
        session_destroy();
        return true;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ../login.php');
            exit;
        }
    }
    
    public function hasPermission($requiredLevel) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $userLevel = $_SESSION['level'];
        
        // Admin (1) has access to everything
        if ($userLevel == 1) {
            return true;
        }
        
        // Manager (3) has access to manager and kasir functions
        if ($userLevel == 3 && in_array($requiredLevel, [2, 3])) {
            return true;
        }
        
        // Kasir (2) has access only to kasir functions
        if ($userLevel == 2 && $requiredLevel == 2) {
            return true;
        }
        
        return false;
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            // Get fresh user data from database including created_at
            $user = $this->db->fetchOne(
                "SELECT p.*, l.level as level_name 
                 FROM petugas p 
                 JOIN level l ON p.level = l.id_level 
                 WHERE p.id_user = ?", 
                [$_SESSION['user_id']]
            );
            
            // If not found in petugas, check manager table
            if (!$user) {
                $user = $this->db->fetchOne(
                    "SELECT m.*, l.level as level_name 
                     FROM manager m 
                     JOIN level l ON m.level = l.id_level 
                     WHERE m.id_user = ?", 
                    [$_SESSION['user_id']]
                );
            }
            
            if ($user) {
                return [
                    'id_user' => $user['id_user'],
                    'username' => $user['username'],
                    'nama_user' => $user['nama_user'],
                    'level' => $user['level'],
                    'level_name' => $user['level_name'],
                    'password' => $user['password'],
                    'created_at' => $user['created_at']
                ];
            }
        }
        return null;
    }
    
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
?>

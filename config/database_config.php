<?php
/**
 * Database Configuration
 * Ted Rubin Project Management System
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ted_rubin_pm');
define('DB_USER', 'your_db_username');  // Change this
define('DB_PASS', 'your_db_password');  // Change this
define('DB_CHARSET', 'utf8mb4');

// Site configuration
define('SITE_URL', 'http://localhost/ted-rubin-pm'); // Change this to your site URL
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_DIR', 'uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query failed: " . $e->getMessage() . " | SQL: " . $sql);
            throw new Exception("Database query failed");
        }
    }
    
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function fetchOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return $this->connection->lastInsertId();
    }
    
    public function update($table, $data, $where, $whereParams = []) {
        $setClause = [];
        foreach (array_keys($data) as $column) {
            $setClause[] = "{$column} = :{$column}";
        }
        $setClause = implode(', ', $setClause);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        return $this->query($sql, $params);
    }
    
    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params);
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollback();
    }
}

/**
 * Portfolio Management Class
 */
class PortfolioManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAllItems($status = 'active', $limit = null) {
        $sql = "SELECT * FROM portfolio_items WHERE status = :status ORDER BY sort_order ASC, created_at DESC";
        $params = ['status' => $status];
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getItemById($id) {
        $sql = "SELECT * FROM portfolio_items WHERE id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function getItemsByCategory($category, $status = 'active') {
        $sql = "SELECT * FROM portfolio_items WHERE category = :category AND status = :status ORDER BY sort_order ASC";
        return $this->db->fetchAll($sql, ['category' => $category, 'status' => $status]);
    }
    
    public function createItem($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('portfolio_items', $data);
    }
    
    public function updateItem($id, $data) {
        return $this->db->update('portfolio_items', $data, 'id = :id', ['id' => $id]);
    }
    
    public function deleteItem($id) {
        return $this->db->delete('portfolio_items', 'id = :id', ['id' => $id]);
    }
    
    public function incrementViews($id) {
        $sql = "UPDATE portfolio_items SET views = views + 1 WHERE id = :id";
        return $this->db->query($sql, ['id' => $id]);
    }
    
    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM portfolio_items WHERE status = 'active' ORDER BY category";
        $result = $this->db->fetchAll($sql);
        return array_column($result, 'category');
    }
}

/**
 * Contact Management Class
 */
class ContactManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function saveSubmission($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        return $this->db->insert('contact_submissions', $data);
    }
    
    public function getAllSubmissions($status = null, $limit = 50) {
        $sql = "SELECT * FROM contact_submissions";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = :status";
            $params['status'] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit";
        $params['limit'] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getSubmissionById($id) {
        $sql = "SELECT * FROM contact_submissions WHERE id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    public function updateSubmissionStatus($id, $status, $notes = null) {
        $data = ['status' => $status];
        if ($notes !== null) {
            $data['notes'] = $notes;
        }
        
        return $this->db->update('contact_submissions', $data, 'id = :id', ['id' => $id]);
    }
}

/**
 * Settings Management Class
 */
class SettingsManager {
    private $db;
    private static $cache = [];
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getSetting($key, $default = null) {
        if (!isset(self::$cache[$key])) {
            $sql = "SELECT setting_value FROM site_settings WHERE setting_key = :key";
            $result = $this->db->fetchOne($sql, ['key' => $key]);
            self::$cache[$key] = $result ? $result['setting_value'] : $default;
        }
        
        return self::$cache[$key];
    }
    
    public function setSetting($key, $value, $type = 'text', $description = null) {
        $data = [
            'setting_key' => $key,
            'setting_value' => $value,
            'setting_type' => $type,
            'description' => $description
        ];
        
        // Try to update first
        $existing = $this->getSetting($key);
        if ($existing !== null) {
            unset($data['setting_key']);
            $this->db->update('site_settings', $data, 'setting_key = :key', ['key' => $key]);
        } else {
            $this->db->insert('site_settings', $data);
        }
        
        // Update cache
        self::$cache[$key] = $value;
    }
    
    public function getAllSettings() {
        $sql = "SELECT * FROM site_settings ORDER BY setting_key";
        return $this->db->fetchAll($sql);
    }
}

/**
 * Authentication Class
 */
class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function login($username, $password) {
        $sql = "SELECT * FROM admin_users WHERE username = :username";
        $user = $this->db->fetchOne($sql, ['username' => $username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_user'] = $user;
            $_SESSION['login_time'] = time();
            
            // Update last login
            $this->db->update('admin_users', 
                ['last_login' => date('Y-m-d H:i:s')], 
                'id = :id', 
                ['id' => $user['id']]
            );
            
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function isLoggedIn() {
        if (!isset($_SESSION['admin_user']) || !isset($_SESSION['login_time'])) {
            return false;
        }
        
        // Check session timeout
        if (time() - $_SESSION['login_time'] > SESSION_TIMEOUT) {
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    public function getUser() {
        return $_SESSION['admin_user'] ?? null;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ' . ADMIN_URL . '/login.php');
            exit;
        }
    }
}

// Helper functions
function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    
    return date('M j, Y', strtotime($datetime));
}

function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return $filename;
}
?>
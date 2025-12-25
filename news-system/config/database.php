<?php
// config/database.php

class Database {
    private $host = "localhost";
    private $db_name = "news_system";
    private $username = "root";
    private $password = "";
    public $conn;
    private static $instance = null;

    public function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $exception) {
            die("Ошибка подключения к базе данных: " . $exception->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function executeQuery($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->logError($e->getMessage(), $sql, $params);
            return false;
        }
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    private function logError($error, $sql = '', $params = []) {
        $logMessage = date('[Y-m-d H:i:s]') . " Error: $error\n";
        if ($sql) {
            $logMessage .= "SQL: $sql\n";
        }
        if (!empty($params)) {
            $logMessage .= "Params: " . print_r($params, true) . "\n";
        }
        // Создаем директорию logs если её нет
        if (!file_exists(__DIR__ . '/../logs')) {
            mkdir(__DIR__ . '/../logs', 0777, true);
        }
        error_log($logMessage, 3, __DIR__ . '/../logs/database_errors.log');
    }
}

// Глобальные функции
function redirect($url) {
    header("Location: $url");
    exit();
}

function setMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        setMessage('Требуется авторизация', 'error');
        redirect('login.php');
    }
}

function requireRole($role) {
    requireAuth();
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
        setMessage('Недостаточно прав', 'error');
        redirect('index.php');
    }
}
?>
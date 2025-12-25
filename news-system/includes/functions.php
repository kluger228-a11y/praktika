<?php
// includes/functions.php
require_once __DIR__ . '/../config/database.php';

class NewsSystem {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // ========== ПОЛЬЗОВАТЕЛИ ==========
    
    public function registerUser($username, $email, $password) {
        // Проверка существования
        $sql = "SELECT COUNT(*) FROM users WHERE username = ? OR email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Пользователь с таким именем или email уже существует'];
        }
        
        // Валидация
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Пароль должен быть не менее 6 символов'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Некорректный email'];
        }
        
        // Хеширование пароля
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Создание пользователя
        $sql = "INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        
        if ($stmt->execute([$username, $email, $hashedPassword])) {
            $userId = $this->db->lastInsertId();
            $this->logActivity($userId, 'user.register', 'Регистрация нового пользователя');
            
            // Автоматический вход
            $this->loginUser($username, $password);
            
            return ['success' => true, 'message' => 'Регистрация успешна'];
        }
        
        return ['success' => false, 'message' => 'Ошибка при регистрации'];
    }
    
    public function loginUser($username, $password) {
        $sql = "SELECT id, username, email, password, role, is_active FROM users 
                WHERE (username = ? OR email = ?) AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            $this->logActivity($user['id'], 'user.login', 'Вход в систему');
            
            return true;
        }
        
        return false;
    }
    
    public function getUserById($id) {
        $sql = "SELECT id, username, email, role, avatar, bio, created_at 
                FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAllUsers($limit = 50, $offset = 0) {
        $sql = "SELECT id, username, email, role, is_active, created_at 
                FROM users 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }
    
    public function countUsers() {
        $sql = "SELECT COUNT(*) FROM users";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    // ========== НОВОСТИ ==========
    
    public function getNews($limit = 10, $offset = 0, $category = null) {
        $sql = "SELECT n.*, u.username as author_name, c.name as category_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                LEFT JOIN categories c ON n.category_id = c.id 
                WHERE n.is_published = 1";
        
        $params = [];
        
        if ($category) {
            $sql .= " AND (c.slug = ? OR c.name = ?)";
            $params[] = $category;
            $params[] = $category;
        }
        
        $sql .= " ORDER BY n.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getNewsById($id) {
        $sql = "SELECT n.*, u.username as author_name, c.name as category_name 
                FROM news n 
                LEFT JOIN users u ON n.author_id = u.id 
                LEFT JOIN categories c ON n.category_id = c.id 
                WHERE n.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $news = $stmt->fetch();
        
        if ($news) {
            // Увеличиваем счетчик просмотров
            $this->incrementViews($id);
        }
        
        return $news;
    }
    
    private function incrementViews($id) {
        $sql = "UPDATE news SET views = views + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
    }
    
    public function createNews($data) {
        // Генерация slug
        $slug = $this->generateSlug($data['title']);
        
        $sql = "INSERT INTO news (title, slug, content, excerpt, author_id, category_id, 
                meta_title, meta_description, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $excerpt = substr(strip_tags($data['content']), 0, 200) . '...';
        $metaTitle = $data['meta_title'] ?? $data['title'];
        $metaDescription = $data['meta_description'] ?? $excerpt;
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['title'],
            $slug,
            $data['content'],
            $excerpt,
            $_SESSION['user_id'],
            $data['category_id'] ?? null,
            $metaTitle,
            $metaDescription
        ]);
        
        if ($result) {
            $newsId = $this->db->lastInsertId();
            $this->logActivity($_SESSION['user_id'], 'news.create', "Создана новость: {$data['title']}");
            return $newsId;
        }
        
        return false;
    }
    
    public function updateNews($id, $data) {
        $sql = "UPDATE news SET title = ?, content = ?, excerpt = ?, category_id = ?, 
                meta_title = ?, meta_description = ?, updated_at = NOW() 
                WHERE id = ?";
        
        $excerpt = substr(strip_tags($data['content']), 0, 200) . '...';
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['title'],
            $data['content'],
            $excerpt,
            $data['category_id'] ?? null,
            $data['meta_title'] ?? $data['title'],
            $data['meta_description'] ?? $excerpt,
            $id
        ]);
        
        if ($result) {
            $this->logActivity($_SESSION['user_id'], 'news.update', "Обновлена новость ID: $id");
        }
        
        return $result;
    }
    
    public function deleteNews($id) {
        $sql = "DELETE FROM news WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([$id]);
        
        if ($result) {
            $this->logActivity($_SESSION['user_id'], 'news.delete', "Удалена новость ID: $id");
        }
        
        return $result;
    }
    
    public function countNews() {
        $sql = "SELECT COUNT(*) FROM news WHERE is_published = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    // ========== КАТЕГОРИИ ==========
    
    public function getCategories() {
        $sql = "SELECT * FROM categories ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // ========== КОММЕНТАРИИ ==========
    
    public function getComments($newsId, $approvedOnly = true) {
        $sql = "SELECT c.*, u.username, u.avatar 
                FROM comments c 
                JOIN users u ON c.user_id = u.id 
                WHERE c.news_id = ?";
        
        if ($approvedOnly) {
            $sql .= " AND c.is_approved = 1";
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$newsId]);
        return $stmt->fetchAll();
    }
    
    // ========== СТАТИСТИКА ==========
    
    public function getStats() {
        $stats = [];
        
        // Общая статистика
        $stats['total_users'] = $this->countUsers();
        $stats['total_news'] = $this->countNews();
        
        $sql = "SELECT COUNT(*) FROM comments WHERE is_approved = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_comments'] = $stmt->fetchColumn();
        
        $sql = "SELECT SUM(views) FROM news";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['total_views'] = $stmt->fetchColumn() ?? 0;
        
        // Статистика за сегодня
        $sql = "SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['new_users_today'] = $stmt->fetchColumn();
        
        $sql = "SELECT COUNT(*) FROM news WHERE DATE(created_at) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['new_news_today'] = $stmt->fetchColumn();
        
        // Популярные новости
        $sql = "SELECT id, title, views FROM news ORDER BY views DESC LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stats['popular_news'] = $stmt->fetchAll();
        
        return $stats;
    }
    
    public function getRecentActivities($limit = 10) {
        $sql = "SELECT al.*, u.username 
                FROM activity_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                ORDER BY al.created_at DESC 
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    // ========== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ==========
    
    private function generateSlug($title) {
        $slug = mb_strtolower($title, 'UTF-8');
        $slug = preg_replace('/[^\p{L}\p{N}\s]/u', '', $slug);
        $slug = preg_replace('/\s+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Проверка уникальности
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function slugExists($slug) {
        $sql = "SELECT COUNT(*) FROM news WHERE slug = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        return $stmt->fetchColumn() > 0;
    }
    
    private function logActivity($userId, $action, $description) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $sql = "INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $action, $description, $ip, $userAgent]);
    }
}
?>
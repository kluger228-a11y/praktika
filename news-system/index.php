<?php
// index.php
$pageTitle = "Главная - Система управления новостями";
$pageDescription = "Добро пожаловать в систему управления новостями";

// Инициализация сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключение конфигурации и функций
require_once 'config/database.php';
require_once 'includes/functions.php';

// Создание экземпляра системы
$system = new NewsSystem();
?>
<!DOCTYPE html>
<html lang="ru" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <meta name="keywords" content="новости, система, управление, CMS">
    <meta name="author" content="News System">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            padding-top: 0;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
            border-radius: 0 0 20px 20px;
        }
        
        .stat-card {
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-newspaper me-2"></i>Новости
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/"><i class="fas fa-home"></i> Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="news.php"><i class="fas fa-newspaper"></i> Все новости</a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Профиль</a>
                        </li>
                        
                        <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'moderator'): ?>
                            <li class="nav-item">
                                <a class="nav-link text-warning" href="admin/">
                                    <i class="fas fa-cogs"></i> Админка
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                
                <!-- Пользовательские кнопки -->
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                                <span class="badge bg-<?php 
                                    switch($_SESSION['user_role']) {
                                        case 'admin': echo 'danger'; break;
                                        case 'moderator': echo 'warning'; break;
                                        default: echo 'secondary';
                                    }
                                ?> ms-1">
                                    <?php echo $_SESSION['user_role']; ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user me-2"></i>Профиль
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Выход
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">
                                <i class="fas fa-sign-in-alt"></i> Вход
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">
                                <i class="fas fa-user-plus"></i> Регистрация
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Сообщения -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="container mt-3">
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <!-- Основной контент -->
    <main class="container my-4">
        <!-- Приветствие -->
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="display-4 fw-bold mb-3">Добро пожаловать в систему управления новостями</h1>
                <p class="lead mb-4">Платформа для публикации и управления новостным контентом</p>
                
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="mt-4">
                        <a href="register.php" class="btn btn-light btn-lg me-3">
                            <i class="fas fa-user-plus me-1"></i> Зарегистрироваться
                        </a>
                        <a href="login.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-sign-in-alt me-1"></i> Войти
                        </a>
                    </div>
                <?php else: ?>
                    <p class="fs-5">
                        <i class="fas fa-check-circle me-1"></i>
                        Вы вошли как <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                    </p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-md-8">
                <!-- Последние новости -->
                <h2 class="mb-4"><i class="fas fa-newspaper me-2"></i>Последние новости</h2>
                
                <?php
                try {
                    $news = $system->getNews(5, 0);
                    
                    if (empty($news)):
                ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Новостей пока нет. Будьте первым, кто опубликует новость!
                    </div>
                <?php
                    else:
                        foreach ($news as $item):
                ?>
                    <div class="card mb-3 stat-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-10">
                                    <h5 class="card-title">
                                        <a href="news.php?id=<?php echo $item['id']; ?>" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($item['title']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?php echo htmlspecialchars($item['excerpt']); ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo htmlspecialchars($item['author_name']); ?>
                                            &bull;
                                            <i class="far fa-calendar me-1"></i>
                                            <?php echo date('d.m.Y', strtotime($item['created_at'])); ?>
                                            &bull;
                                            <i class="far fa-eye me-1"></i>
                                            <?php echo $item['views']; ?> просмотров
                                        </small>
                                        <a href="news.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">
                                            Читать далее <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                        endforeach;
                    endif;
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">Ошибка при загрузке новостей: ' . $e->getMessage() . '</div>';
                }
                ?>
                
                <div class="text-center mt-4">
                    <a href="news.php" class="btn btn-primary">
                        <i class="fas fa-list me-1"></i> Все новости
                    </a>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Статистика -->
                <div class="card mb-4 stat-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Статистика</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Всего новостей
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $system->countNews(); ?>
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Всего пользователей
                                <span class="badge bg-success rounded-pill">
                                    <?php echo $system->countUsers(); ?>
                                </span>
                            </li>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Ваша роль
                                    <span class="badge bg-<?php 
                                        switch($_SESSION['user_role']) {
                                            case 'admin': echo 'danger'; break;
                                            case 'moderator': echo 'warning'; break;
                                            default: echo 'secondary';
                                        }
                                    ?> rounded-pill">
                                        <?php echo $_SESSION['user_role']; ?>
                                    </span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Категории -->
                <div class="card mb-4 stat-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Категории</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        try {
                            $categories = $system->getCategories();
                            if (!empty($categories)):
                        ?>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($categories as $category): ?>
                                    <a href="category.php?slug=<?php echo $category['slug']; ?>" 
                                       class="badge bg-info text-decoration-none">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Категории пока не добавлены</p>
                        <?php endif;
                        } catch (Exception $e) {
                            echo '<p class="text-danger small">Ошибка загрузки категорий</p>';
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Быстрые действия -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="card mb-4 stat-card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Быстрые действия</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="profile.php" class="btn btn-outline-primary">
                                    <i class="fas fa-user me-1"></i> Мой профиль
                                </a>
                                
                                <?php if ($_SESSION['user_role'] === 'moderator' || $_SESSION['user_role'] === 'admin'): ?>
                                    <a href="admin/news.php?action=create" class="btn btn-outline-success">
    <i class="fas fa-plus me-1"></i> Добавить новость
</a>
                                <?php endif; ?>
                                
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <a href="admin/" class="btn btn-outline-danger">
                                        <i class="fas fa-cogs me-1"></i> Админ панель
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- Футер -->
    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Система управления новостями</h5>
                    <p class="text-muted">Простая и эффективная система для публикации новостей</p>
                </div>
                <div class="col-md-3">
                    <h5>Навигация</h5>
                    <ul class="list-unstyled">
                        <li><a href="/" class="text-white text-decoration-none">Главная</a></li>
                        <li><a href="news.php" class="text-white text-decoration-none">Все новости</a></li>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li><a href="login.php" class="text-white text-decoration-none">Вход</a></li>
                            <li><a href="register.php" class="text-white text-decoration-none">Регистрация</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Контакты</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope me-2"></i> support@news-system.ru</li>
                        <li><i class="fas fa-phone me-2"></i> +7 (999) 123-45-67</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> Москва, Россия</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> Система управления новостями. 
                        Все права защищены.
                    </p>
                    <p class="text-muted small mt-2">
                        <i class="fas fa-code me-1"></i>
                        Разработано на PHP, MySQL, Bootstrap 5
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    
    <script>
        // Глобальные функции JavaScript
        function confirmAction(message, callback) {
            if (confirm(message)) {
                callback();
            }
        }
        
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            const container = document.querySelector('.toast-container') || createToastContainer();
            container.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }
        
        function createToastContainer() {
            const container = document.createElement('div');
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(container);
            return container;
        }
        
        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            // Инициализация tooltips
            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(function(tooltip) {
                return new bootstrap.Tooltip(tooltip);
            });
            
            // Инициализация popovers
            const popovers = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popovers.map(function(popover) {
                return new bootstrap.Popover(popover);
            });
            
            // Автоматическое скрытие алертов
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    setTimeout(() => bsAlert.close(), 5000);
                });
            }, 3000);
        });
    </script>
</body>
</html>
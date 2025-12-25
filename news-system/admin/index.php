<?php
// admin/index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/database.php';
require_once '../includes/functions.php';

// Проверка прав доступа
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    setMessage('Доступ запрещен', 'error');
    redirect('../login.php');
}

$system = new NewsSystem();

// Получение статистики
$stats = $system->getStats();
$recentActivities = $system->getRecentActivities(10);

$pageTitle = "Админ панель";
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
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 250px;
            background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
            color: white;
            padding: 0;
            z-index: 1000;
            box-shadow: 3px 0 10px rgba(0,0,0,0.1);
        }
        
        .admin-main {
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            display: block;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-nav .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #667eea;
        }
        
        .sidebar-nav .nav-link.active {
            background: rgba(102, 126, 234, 0.2);
            color: white;
            border-left-color: #667eea;
        }
        
        .admin-header {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 1.5rem;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 70px;
            }
            
            .admin-main {
                margin-left: 70px;
            }
            
            .sidebar-header h3 span,
            .sidebar-nav .nav-link span {
                display: none;
            }
            
            .sidebar-nav .nav-link {
                text-align: center;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Сайдбар -->
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h3 class="mb-0">
                <i class="fas fa-cogs"></i>
                <span class="ms-2">Админка</span>
            </h3>
            <small class="text-muted">News System</small>
        </div>
        
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-link active">
                <i class="fas fa-tachometer-alt"></i>
                <span class="ms-2">Дашборд</span>
            </a>
            <a href="users.php" class="nav-link">
                <i class="fas fa-users"></i>
                <span class="ms-2">Пользователи</span>
            </a>
            <a href="news.php" class="nav-link">
                <i class="fas fa-newspaper"></i>
                <span class="ms-2">Новости</span>
            </a>
            <a href="categories.php" class="nav-link">
                <i class="fas fa-folder"></i>
                <span class="ms-2">Категории</span>
            </a>
            <a href="comments.php" class="nav-link">
                <i class="fas fa-comments"></i>
                <span class="ms-2">Комментарии</span>
            </a>
            <div class="mt-4 pt-3 border-top border-secondary">
                <a href="../index.php" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span class="ms-2">На сайт</span>
                </a>
                <a href="../logout.php" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="ms-2">Выход</span>
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Основной контент -->
    <div class="admin-main">
        <!-- Хедер -->
        <div class="admin-header">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Дашборд
                </h1>
                <p class="text-muted mb-0">Обзор системы и статистика</p>
            </div>
            <div class="text-end">
                <div class="d-flex align-items-center">
                    <div class="me-3 text-end">
                        <div class="fw-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                        <small class="text-muted"><?php echo $_SESSION['user_role']; ?></small>
                    </div>
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px;">
                        <i class="fas fa-user text-white"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Сообщения -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
        
        <!-- Статистика -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="stat-icon text-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Пользователи</h5>
                                <h2 class="mb-0"><?php echo $stats['total_users']; ?></h2>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +<?php echo $stats['new_users_today']; ?> за сегодня
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="stat-icon text-success">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Новости</h5>
                                <h2 class="mb-0"><?php echo $stats['total_news']; ?></h2>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>
                                    +<?php echo $stats['new_news_today']; ?> за сегодня
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="stat-icon text-info">
                                    <i class="fas fa-comments"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Комментарии</h5>
                                <h2 class="mb-0"><?php echo $stats['total_comments']; ?></h2>
                                <small class="text-muted">Всего комментариев</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="stat-icon text-warning">
                                    <i class="fas fa-eye"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title text-muted mb-1">Просмотры</h5>
                                <h2 class="mb-0"><?php echo number_format($stats['total_views']); ?></h2>
                                <small class="text-muted">Всего просмотров</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Последние действия -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Последние действия
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Пользователь</th>
                                        <th>Действие</th>
                                        <th>Описание</th>
                                        <th>Время</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentActivities as $activity): ?>
                                    <tr>
                                        <td>
                                            <?php if ($activity['username']): ?>
                                                <span class="badge bg-primary">
                                                    <?php echo htmlspecialchars($activity['username']); ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Система</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                switch($activity['action']) {
                                                    case 'user.login': echo 'success'; break;
                                                    case 'user.register': echo 'info'; break;
                                                    case 'news.create': echo 'warning'; break;
                                                    case 'news.delete': echo 'danger'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>">
                                                <?php echo $activity['action']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($activity['description']); ?></small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo date('H:i', strtotime($activity['created_at'])); ?>
                                            </small>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Быстрые действия -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt me-2"></i>Быстрые действия
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="users.php?action=create" class="btn btn-outline-primary text-start">
                                <i class="fas fa-user-plus me-2"></i> Добавить пользователя
                            </a>
                            <a href="news.php?action=create" class="btn btn-outline-success text-start">
                                <i class="fas fa-plus me-2"></i> Добавить новость
                            </a>
                            <a href="categories.php?action=create" class="btn btn-outline-info text-start">
                                <i class="fas fa-folder-plus me-2"></i> Добавить категорию
                            </a>
                            <a href="comments.php" class="btn btn-outline-warning text-start">
                                <i class="fas fa-comment-check me-2"></i> Модерация комментариев
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Популярные новости -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-fire me-2"></i>Популярные новости
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach ($stats['popular_news'] as $news): ?>
                            <a href="../news.php?id=<?php echo $news['id']; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                               target="_blank">
                                <span class="text-truncate" style="max-width: 70%;">
                                    <?php echo htmlspecialchars($news['title']); ?>
                                </span>
                                <span class="badge bg-primary rounded-pill">
                                    <?php echo $news['views']; ?>
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Автоматическое скрытие алертов
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                setTimeout(() => bsAlert.close(), 5000);
            });
        }, 3000);
    </script>
</body>
</html>
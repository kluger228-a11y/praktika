<?php
// admin/news.php
session_start();

// Проверка прав доступа
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'moderator')) {
    header('Location: ../index.php');
    exit();
}

$pageTitle = "Управление новостями";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px 0;
        }
        .admin-main {
            margin-left: 250px;
            padding: 20px;
        }
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        .sidebar-nav {
            padding: 20px 0;
        }
        .sidebar-nav a {
            display: block;
            color: rgba(255,255,255,0.8);
            padding: 10px 20px;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all 0.3s;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #3498db;
        }
        .admin-header {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Сайдбар -->
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-cogs"></i> Админка</h4>
            <small><?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo $_SESSION['user_role']; ?>)</small>
        </div>
        
        <nav class="sidebar-nav">
            <a href="index.php">
                <i class="fas fa-tachometer-alt"></i> Дашборд
            </a>
            <a href="users.php">
                <i class="fas fa-users"></i> Пользователи
            </a>
            <a href="news.php" class="active">
                <i class="fas fa-newspaper"></i> Новости
            </a>
            <a href="categories.php">
                <i class="fas fa-folder"></i> Категории
            </a>
            <a href="comments.php">
                <i class="fas fa-comments"></i> Комментарии
            </a>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                <a href="../index.php">
                    <i class="fas fa-external-link-alt"></i> На сайт
                </a>
                <a href="../logout.php" class="text-danger">
                    <i class="fas fa-sign-out-alt"></i> Выход
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Основной контент -->
    <div class="admin-main">
        <!-- Хедер -->
        <div class="admin-header">
            <h1><i class="fas fa-newspaper me-2"></i>Управление новостями</h1>
            <p class="text-muted mb-0">Создание и редактирование новостей</p>
        </div>
        
        <!-- Сообщения -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>
        
        <!-- Кнопка добавления новости -->
        <div class="mb-4">
            <a href="?action=create" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Добавить новость
            </a>
        </div>
        
        <!-- Форма добавления новости -->
        <?php if (isset($_GET['action']) && $_GET['action'] === 'create'): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Создание новой новости</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="title" class="form-label">Заголовок *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Содержание *</label>
                            <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Категория</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Без категории</option>
                                    <option value="1">Политика</option>
                                    <option value="2">Экономика</option>
                                    <option value="3">Технологии</option>
                                    <option value="4">Спорт</option>
                                    <option value="5">Культура</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="image_url" class="form-label">URL изображения</label>
                                <input type="url" class="form-control" id="image_url" name="image_url" 
                                       placeholder="https://example.com/image.jpg">
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_published" name="is_published" checked>
                            <label class="form-check-label" for="is_published">Опубликовать сразу</label>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Сохранить новость
                            </button>
                            <a href="news.php" class="btn btn-secondary">Отмена</a>
                        </div>
                    </form>
                </div>
            </div>
            
        <!-- Список новостей -->
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Список новостей</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Заголовок</th>
                                    <th>Автор</th>
                                    <th>Дата</th>
                                    <th>Просмотры</th>
                                    <th>Статус</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Пример новости -->
                                <tr>
                                    <td>1</td>
                                    <td>Добро пожаловать в систему новостей</td>
                                    <td>admin</td>
                                    <td>01.01.2024</td>
                                    <td>45</td>
                                    <td><span class="badge bg-success">Опубликована</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="?action=edit&id=1" class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Еще примеры -->
                                <tr>
                                    <td>2</td>
                                    <td>Новые функции платформы</td>
                                    <td>moderator</td>
                                    <td>02.01.2024</td>
                                    <td>32</td>
                                    <td><span class="badge bg-success">Опубликована</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="?action=edit&id=2" class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Это демо-версия. В реальной системе здесь будут загружаться новости из базы данных.
                    </div>
                </div>
            </div>
        <?php endif; ?>
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
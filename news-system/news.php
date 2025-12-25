<?php
// news.php - основной файл для новостей
session_start();

$pageTitle = "Все новости";

// Подключаем функции если нужно
if (file_exists('includes/functions.php')) {
    require_once 'includes/functions.php';
}
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
        .news-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            margin-bottom: 2rem;
            height: 100%;
        }
        .news-card:hover {
            transform: translateY(-5px);
        }
        .news-image {
            height: 200px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
            width: 100%;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
            border-radius: 0 0 20px 20px;
        }
    </style>
</head>
<body>
    <!-- Навигационная панель -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-newspaper me-2"></i>Новости
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-home"></i> Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="news.php"><i class="fas fa-newspaper"></i> Все новости</a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Профиль</a>
                        </li>
                        
                        <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'moderator')): ?>
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
                                    if (isset($_SESSION['user_role'])) {
                                        switch($_SESSION['user_role']) {
                                            case 'admin': echo 'danger'; break;
                                            case 'moderator': echo 'warning'; break;
                                            default: echo 'secondary';
                                        }
                                    } else {
                                        echo 'secondary';
                                    }
                                ?> ms-1">
                                    <?php echo $_SESSION['user_role'] ?? 'user'; ?>
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
        <!-- Заголовок -->
        <div class="hero-section text-center">
            <div class="container">
                <h1 class="display-5 fw-bold mb-3">Все новости</h1>
                <p class="lead mb-4">Читайте самые свежие и интересные новости</p>
            </div>
        </div>
        
        <!-- Список новостей -->
        <div class="row">
            <!-- Новость 1 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card news-card">
                    <img src="https://images.unsplash.com/photo-1588681664899-f142ff2dc9b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" 
                         class="card-img-top news-image" alt="Новость 1">
                    <div class="card-body">
                        <h5 class="card-title">Добро пожаловать в систему новостей</h5>
                        <p class="card-text text-muted">
                            Это первая новость в нашей системе. Здесь вы найдете самые свежие новости...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> admin
                            </small>
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i> 01.01.2024
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="news-single.php?id=1" class="btn btn-primary w-100">
                            <i class="fas fa-book-reader me-1"></i> Читать далее
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Новость 2 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card news-card">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" 
                         class="card-img-top news-image" alt="Новость 2">
                    <div class="card-body">
                        <h5 class="card-title">Новые функции платформы</h5>
                        <p class="card-text text-muted">
                            Мы рады сообщить о добавлении новых функций в нашу платформу...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> moderator
                            </small>
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i> 02.01.2024
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="news-single.php?id=2" class="btn btn-primary w-100">
                            <i class="fas fa-book-reader me-1"></i> Читать далее
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Новость 3 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card news-card">
                    <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" 
                         class="card-img-top news-image" alt="Новость 3">
                    <div class="card-body">
                        <h5 class="card-title">Экономический рост</h5>
                        <p class="card-text text-muted">
                            По данным министерства экономики, наблюдается стабильный рост...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> admin
                            </small>
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i> 03.01.2024
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="news-single.php?id=3" class="btn btn-primary w-100">
                            <i class="fas fa-book-reader me-1"></i> Читать далее
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Новость 4 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card news-card">
                    <img src="https://images.unsplash.com/photo-1517649763962-0c623066013b?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" 
                         class="card-img-top news-image" alt="Новость 4">
                    <div class="card-body">
                        <h5 class="card-title">Спортивные достижения</h5>
                        <p class="card-text text-muted">
                            Наши спортсмены показали отличные результаты на международных соревнованиях...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> moderator
                            </small>
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i> 04.01.2024
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="news-single.php?id=4" class="btn btn-primary w-100">
                            <i class="fas fa-book-reader me-1"></i> Читать далее
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Новость 5 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card news-card">
                    <img src="https://images.unsplash.com/photo-1518709268805-4e9042af2176?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" 
                         class="card-img-top news-image" alt="Новость 5">
                    <div class="card-body">
                        <h5 class="card-title">Технологии в образовании</h5>
                        <p class="card-text text-muted">
                            Современные технологии кардинально меняют подход к образованию...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> admin
                            </small>
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i> 05.01.2024
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="news-single.php?id=5" class="btn btn-primary w-100">
                            <i class="fas fa-book-reader me-1"></i> Читать далее
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Новость 6 -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card news-card">
                    <img src="https://images.unsplash.com/photo-1499364615650-ec38552f4f34?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" 
                         class="card-img-top news-image" alt="Новость 6">
                    <div class="card-body">
                        <h5 class="card-title">Культурные события</h5>
                        <p class="card-text text-muted">
                            В этом месяце нас ждут интересные культурные события...
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i> moderator
                            </small>
                            <small class="text-muted">
                                <i class="far fa-calendar me-1"></i> 06.01.2024
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="news-single.php?id=6" class="btn btn-primary w-100">
                            <i class="fas fa-book-reader me-1"></i> Читать далее
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Пагинация -->
        <nav aria-label="Навигация по страницам">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
                <li class="page-item active"><a class="page-link" href="news.php">1</a></li>
                <li class="page-item"><a class="page-link" href="news.php?page=2">2</a></li>
                <li class="page-item"><a class="page-link" href="news.php?page=3">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="news.php?page=2">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Информация -->
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle me-2"></i>
            Всего новостей: <strong>6</strong>. Показано: <strong>6</strong>.
        </div>
    </main>
    
    <!-- Футер -->
    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> Система управления новостями
                    </p>
                    <p class="text-muted small mt-2">
                        <a href="index.php" class="text-white text-decoration-none me-3">Главная</a>
                        <a href="news.php" class="text-white text-decoration-none me-3">Новости</a>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="login.php" class="text-white text-decoration-none me-3">Вход</a>
                            <a href="register.php" class="text-white text-decoration-none">Регистрация</a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
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
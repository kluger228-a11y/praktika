<?php
// profile.php
session_start();

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Мой профиль";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        .profile-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 10px;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
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
                        <a class="nav-link" href="news.php"><i class="fas fa-newspaper"></i> Новости</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="profile.php"><i class="fas fa-user"></i> Профиль</a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'moderator')): ?>
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="admin/">
                                <i class="fas fa-cogs"></i> Админка
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Пользователь -->
                <ul class="navbar-nav">
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
                            <li><a class="dropdown-item active" href="profile.php">
                                <i class="fas fa-user me-2"></i>Профиль
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Выход
                            </a></li>
                        </ul>
                    </li>
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
    <main class="container">
        <!-- Заголовок профиля -->
        <div class="profile-header text-center">
            <div class="container">
                <div class="profile-avatar mb-3 d-inline-flex align-items-center justify-content-center bg-white text-primary" 
                     style="width: 150px; height: 150px; border-radius: 50%;">
                    <i class="fas fa-user fa-4x"></i>
                </div>
                
                <h1 class="display-5 fw-bold mb-2"><?php echo htmlspecialchars($_SESSION['username']); ?></h1>
                <p class="lead mb-0">
                    <span class="badge bg-<?php 
                        switch($_SESSION['user_role']) {
                            case 'admin': echo 'danger'; break;
                            case 'moderator': echo 'warning'; break;
                            default: echo 'secondary';
                        }
                    ?> fs-6">
                        <?php echo $_SESSION['user_role']; ?>
                    </span>
                    • ID: <?php echo $_SESSION['user_id']; ?>
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <!-- Информация о пользователе -->
                <div class="profile-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Информация</h5>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Имя:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($_SESSION['username']); ?></dd>
                            
                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($_SESSION['user_email'] ?? 'Не указан'); ?></dd>
                            
                            <dt class="col-sm-4">Роль:</dt>
                            <dd class="col-sm-8">
                                <span class="badge bg-<?php 
                                    switch($_SESSION['user_role']) {
                                        case 'admin': echo 'danger'; break;
                                        case 'moderator': echo 'warning'; break;
                                        default: echo 'secondary';
                                    }
                                ?>">
                                    <?php echo $_SESSION['user_role']; ?>
                                </span>
                            </dd>
                            
                            <dt class="col-sm-12 mt-3">ID сессии:</dt>
                            <dd class="col-sm-12">
                                <code><?php echo session_id(); ?></code>
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <!-- Статистика -->
                <div class="profile-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Статистика</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon text-primary">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                    <div class="stat-number">0</div>
                                    <div class="stat-label">Новости</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="stat-card">
                                    <div class="stat-icon text-warning">
                                        <i class="fas fa-comment"></i>
                                    </div>
                                    <div class="stat-number">0</div>
                                    <div class="stat-label">Комментарии</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <!-- Форма редактирования профиля -->
                <div class="profile-card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Редактировать профиль</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="row g-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Имя пользователя</label>
                                <input type="text" class="form-control" id="username" 
                                       value="<?php echo htmlspecialchars($_SESSION['username']); ?>" disabled>
                                <div class="form-text">Имя пользователя нельзя изменить</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-12">
                                <label for="bio" class="form-label">О себе</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4"
                                          placeholder="Расскажите о себе..."><?php echo htmlspecialchars($_SESSION['bio'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Сохранить изменения
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Смена пароля -->
                <div class="profile-card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-key me-2"></i>Смена пароля</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="row g-3">
                            <div class="col-md-12">
                                <label for="current_password" class="form-label">Текущий пароль</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" 
                                           name="current_password" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePasswordVisibility('current_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">Новый пароль</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" 
                                           name="new_password" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePasswordVisibility('new_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Подтверждение пароля</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePasswordVisibility('confirm_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <div class="form-text">
                                    Пароль должен быть не менее 6 символов
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-key me-1"></i> Сменить пароль
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Сессия -->
                <div class="profile-card mt-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Сессия</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>ID сессии:</strong> <?php echo session_id(); ?></p>
                        <p><strong>Время начала:</strong> <?php echo date('H:i:s'); ?></p>
                        <div class="d-grid gap-2">
                            <a href="logout.php" class="btn btn-danger">
                                <i class="fas fa-sign-out-alt me-1"></i> Выйти из системы
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Футер -->
    <footer class="footer mt-auto py-3 bg-dark text-white">
        <div class="container text-center">
            <p class="mb-0">
                &copy; <?php echo date('Y'); ?> Система управления новостями
            </p>
        </div>
    </footer>
    
    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
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
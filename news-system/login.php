<?php
// login.php - простой файл для теста
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Простая проверка (тестовые пользователи)
    $users = [
        'admin' => ['password' => '123456', 'role' => 'admin'],
        'moderator' => ['password' => '123456', 'role' => 'moderator'],
        'user1' => ['password' => '123456', 'role' => 'user']
    ];
    
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = $username;
        $_SESSION['user_role'] = $users[$username]['role'];
        $_SESSION['message'] = 'Вы успешно вошли!';
        $_SESSION['message_type'] = 'success';
        header('Location: index.php');
        exit();
    } else {
        $error = 'Неверные данные';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card p-4">
            <h2 class="text-center mb-4">Вход в систему</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['message']; ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Имя пользователя</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
            
            <div class="mt-3 text-center">
                <a href="index.php">На главную</a> | 
                <a href="register.php">Регистрация</a>
            </div>
            
            <div class="mt-4 p-3 border rounded">
                <h6 class="mb-2">Тестовые пользователи:</h6>
                <p class="mb-1"><strong>admin</strong> / 123456</p>
                <p class="mb-1"><strong>moderator</strong> / 123456</p>
                <p class="mb-0"><strong>user1</strong> / 123456</p>
            </div>
        </div>
    </div>
</body>
</html>
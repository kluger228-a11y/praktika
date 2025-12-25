<?php
// logout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Очищаем все данные сессии
$_SESSION = array();

// Удаляем сессионную cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Уничтожаем сессию
session_destroy();

// Перенаправляем на главную с сообщением
session_start();
$_SESSION['message'] = 'Вы успешно вышли из системы';
$_SESSION['message_type'] = 'success';
header('Location: index.php');
exit();
?>
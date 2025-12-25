<?php
// admin/users.php
session_start();

// Проверка прав доступа
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

echo "Страница управления пользователями";
?>
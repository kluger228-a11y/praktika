<?php
// admin/categories.php
session_start();

// Проверка прав доступа
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'moderator')) {
    header('Location: ../index.php');
    exit();
}

echo "Страница управления категориями";
?>
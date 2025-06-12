<?php
session_start();

if (!file_exists('config.php')) {
    header('Location: init.php');
    exit;
}

require 'config.php';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
    if ($stmt->rowCount() === 0) {
        header('Location: init.php');
        exit;
    }
} catch (Exception $e) {
    header('Location: init.php');
    exit;
}

// 跳转到登录
header('Location: login.php');
exit;

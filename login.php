<?php
session_start();

if (!file_exists('config.php')) {
    header('Location: init.php');
    exit;
}

require 'config.php';

$error = '';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $pdo = new PDO($dsn, $user, $pass);
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "用户名或密码错误";
        }
    } catch (Exception $e) {
        $error = "数据库连接失败：" . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>管理员登录 - Backup Panel</title>
    <style>
        body {
            background: #0b0c10;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: #1f2833;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px #45a29e;
            width: 350px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #66fcf1;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            background: #c5c6c7;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            text-align: center;
        }
        button {
            margin-top: 20px;
            width: 60%;
            padding: 10px;
            background: #45a29e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
        }
        .error {
            color: #ff5c5c;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>管理员登录</h2>
    <form method="post">
        <input type="text" name="username" placeholder="用户名" required>
        <input type="password" name="password" placeholder="密码" required>
        <button type="submit">登录</button>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>
</div>
</body>
</html>

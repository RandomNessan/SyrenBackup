<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: /login.php");
    exit;
}

require '../config.php';
$pdo = new PDO($dsn, $user, $pass);

$current_user = $_SESSION['admin_username'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_user = $_POST['username'];
    $new_pass = $_POST['password'];

    try {
        $stmt = $pdo->prepare("UPDATE admins SET username = ?, password_hash = ? WHERE username = ?");
        $stmt->execute([
            $new_user,
            password_hash($new_pass, PASSWORD_DEFAULT),
            $current_user
        ]);

        session_unset();
        session_destroy();
        header("Location: /login.php?msg=修改成功，请重新登录");
        exit;
    } catch (Exception $e) {
        $error = "修改失败：" . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>修改管理员信息</title>
    <style>
        body {
            font-family: sans-serif;
            background: #1e1e1e;
            color: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: #2c2f33;
            padding: 30px;
            border-radius: 10px;
            width: 360px;
            box-shadow: 0 0 10px #000;
        }
        input {
            width: 90%;
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            border: none;
            font-size: 15px;
        }
        button {
            margin-top: 20px;
            width: 45%;
            padding: 10px;
            background: #45a29e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .cancel {
            background: #c0392b;
            float: right;
        }
        h2 {
            text-align: center;
        }
        .msg {
            margin-top: 10px;
            text-align: center;
        }
        .error { color: #ff4c4c; }
    </style>
</head>
<body>
<form method="post">
    <h2>修改管理员信息</h2>
    <input type="text" name="username" placeholder="新用户名" required>
    <input type="password" name="password" placeholder="新密码" required>
    <div style="display: flex; justify-content: space-between;">
        <button type="submit">保存修改</button>
        <button type="button" class="cancel" onclick="window.location.href='/dashboard.php'">放弃修改</button>
    </div>
    <?php if ($error): ?><div class="msg error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
</form>
</body>
</html>

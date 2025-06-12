<?php
session_start();

// 已初始化则跳转
if (file_exists('config.php')) {
    require 'config.php';
    try {
        $pdo = new PDO($dsn, $user, $pass);
        $stmt = $pdo->query("SHOW TABLES LIKE 'admins'");
        if ($stmt->rowCount()) {
            header('Location: login.php');
            exit;
        }
    } catch (Exception $e) {
        // 继续初始化
    }
}

// 处理表单提交
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbName = $_POST['dbname'];
    $dbUser = $_POST['dbuser'];
    $dbPass = $_POST['dbpass'];
    $adminUser = $_POST['admin_user'];
    $adminPass = $_POST['admin_pass'];

    // 写入 config.php
    $configContent = "<?php\n\$dsn = 'mysql:host=localhost;dbname={$dbName};charset=utf8mb4';\n\$user = '{$dbUser}';\n\$pass = '{$dbPass}';\n";

    if (file_put_contents('config.php', $configContent)) {
        try {
            require 'config.php';
            $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

            $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )");

            $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
            $stmt->execute([$adminUser, password_hash($adminPass, PASSWORD_DEFAULT)]);

            header('Location: login.php');
            exit;
        } catch (Exception $e) {
            $error = "数据库连接失败或用户无权限：" . $e->getMessage();
        }
    } else {
        $error = "无法写入 config.php，请检查文件权限";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>系统初始化</title>
    <style>
        body { font-family: sans-serif; background: #1e1e1e; color: #eee; display: flex; justify-content: center; align-items: center; height: 100vh; }
        form { background: #2c2f33; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px #000; width: 350px; }
        input { width: 100%; margin-top: 10px; padding: 10px; border-radius: 5px; border: none; }
        button { margin-top: 20px; width: 100%; padding: 10px; background: #3cb371; color: white; border: none; border-radius: 5px; }
        h2 { text-align: center; }
        .error { color: #ff4c4c; margin-top: 10px; }
    </style>
</head>
<body>
    <form method="post">
        <h2>初始化配置</h2>
        <input name="dbname" placeholder="数据库名" required>
        <input name="dbuser" placeholder="数据库用户名" required>
        <input name="dbpass" placeholder="数据库密码" type="password" required>
        <hr>
        <input name="admin_user" placeholder="管理员用户名" required>
        <input name="admin_pass" placeholder="管理员密码" type="password" required>
        <button type="submit">完成初始化</button>
        <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    </form>
</body>
</html>

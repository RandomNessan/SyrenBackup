<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require 'config.php';

$pdo = new PDO($dsn, $user, $pass);
$backups = $pdo->query("SELECT * FROM backups ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>管理后台 - 备份查看</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            display: flex;
            background: #0f0f0f;
            color: #eee;
        }
        aside {
            width: 220px;
            background: #1e1e1e;
            padding: 20px;
            height: 100vh;
        }
        aside h2 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #66fcf1;
        }
        aside a {
            display: block;
            margin: 10px 0;
            color: #eee;
            text-decoration: none;
        }
        main {
            flex: 1;
            padding: 30px;
            background: #1b1b1b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #222;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #444;
            text-align: left;
        }
        th {
            background: #111;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-btn {
            cursor: pointer;
            padding: 4px 10px;
            font-size: 18px;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: #2f2f2f;
            box-shadow: 0 0 10px rgba(0,0,0,0.6);
            z-index: 100;
            min-width: 140px;
            border-radius: 4px;
            overflow: hidden;
        }
        .dropdown-content a {
            color: #66fcf1;
            padding: 10px;
            display: block;
            text-decoration: none;
        }
        .dropdown-content a:hover {
            background-color: #444;
        }
        .show {
            display: block;
        }
    </style>
</head>
<body>
<aside>
    <h2>管理后台</h2>
    <a href="method/change_password.php">修改管理员信息</a>
    <a href="dashboard.php">查看备份</a>
    <a href="dashboard.php?page=upload_config">上传配置</a>
    <a href="method/logout.php">退出登录</a>
</aside>

<main>
    <?php if (isset($_GET['page']) && $_GET['page'] === 'upload_config'): ?>
        <h1>自动部署备份脚本</h1>
        <p>请在需要被备份的 VPS 上执行以下命令：</p>
        <pre style="background:#222;padding:10px;border-radius:5px;word-break:break-all;white-space:pre-wrap;">
mkdir -p /tmp/bak_script && cd /tmp/bak_script && \
curl -O https://<?= $_SERVER['HTTP_HOST'] ?>/script/bakinit.sh && \
curl -O https://<?= $_SERVER['HTTP_HOST'] ?>/script/db_auto_backup_push.sh.template && \
bash bakinit.sh
        </pre>
    <?php else: ?>
        <h1 style="display: flex; justify-content: space-between; align-items: center;">
            <span>数据库备份记录</span>
            <form method="post" action="method/delete_batch.php" onsubmit="return confirm('确认删除选中的备份？')">
                <button type="submit" style="background:#e74c3c;color:#fff;border:none;padding:8px 12px;border-radius:4px;cursor:pointer;">批量删除</button>
        </h1>

        <?php if (count($backups) === 0): ?>
            <p>暂无备份记录。</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggleAll(this)"></th>
                        <th>ID</th>
                        <th>文件名</th>
                        <th>备份时间</th>
                        <th>上传时间</th>
                        <th>文件大小</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $b): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_ids[]" value="<?= $b['id'] ?>"></td>
                            <td><?= $b['id'] ?></td>
                            <td><?= htmlspecialchars($b['filename']) ?></td>
                            <td><?= $b['backup_time'] ?></td>
                            <td><?= $b['upload_time'] ?></td>
                            <td><?= round($b['file_size'] / 1024, 2) ?> KB</td>
                            <td>
                                <div class="dropdown">
                                    <span class="dropdown-btn" onclick="toggleDropdown(this)">⋮</span>
                                    <div class="dropdown-content">
                                        <a href="uploads/<?= urlencode($b['filename']) ?>" download>下载备份</a>
                                        <a href="method/delete_backup.php?id=<?= $b['id'] ?>" onclick="return confirm('确认删除该备份？')">删除备份</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</main>

<script>
function toggleAll(source) {
    const checkboxes = document.querySelectorAll('input[name="selected_ids[]"]');
    checkboxes.forEach(cb => cb.checked = source.checked);
}

function toggleDropdown(button) {
    // 先关闭所有其他的
    document.querySelectorAll('.dropdown-content').forEach(menu => {
        if (menu !== button.nextElementSibling) {
            menu.classList.remove('show');
        }
    });

    // 切换当前的
    const dropdown = button.nextElementSibling;
    dropdown.classList.toggle('show');
}

// 点击其他区域关闭所有下拉菜单
document.addEventListener('click', function(event) {
    const isClickInsideDropdown = event.target.closest('.dropdown');
    if (!isClickInsideDropdown) {
        document.querySelectorAll('.dropdown-content').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});
</script>

</body>
</html>

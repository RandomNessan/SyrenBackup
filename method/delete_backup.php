<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    exit('未登录');
}

require '../config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    exit('缺少参数');
}

$pdo = new PDO($dsn, $user, $pass);
$stmt = $pdo->prepare("SELECT * FROM backups WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch();

if (!$record) {
    exit('备份记录不存在');
}

$filename = $record['filename'];
$filePath = __DIR__ . '/../uploads/' . $filename;

// 删除文件
if (file_exists($filePath)) {
    unlink($filePath);
}

// 删除记录
$del = $pdo->prepare("DELETE FROM backups WHERE id = ?");
$del->execute([$id]);

header('Location: ../dashboard.php');
exit;

<?php
require '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['filename']) || !isset($_POST['backup_time'])) {
    http_response_code(400);
    exit('非法请求');
}

$filename = $_POST['filename'];
$backupTime = $_POST['backup_time'];
$filePath = __DIR__ . '/../uploads/' . $filename;

if (!file_exists($filePath)) {
    exit('文件不存在');
}

$fileSize = filesize($filePath);
$uploaderIp = $_SERVER['REMOTE_ADDR'];

$pdo = new PDO($dsn, $user, $pass);
$stmt = $pdo->prepare("INSERT IGNORE INTO backups (filename, backup_time, file_size, uploader_ip) VALUES (?, ?, ?, ?)");
$stmt->execute([$filename, $backupTime, $fileSize, $uploaderIp]);

echo "记录成功";

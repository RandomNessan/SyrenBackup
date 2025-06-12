<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    http_response_code(400);
    exit('非法请求');
}

$uploadDir = __DIR__ . '/../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filename = basename($_FILES['file']['name']);
$targetPath = $uploadDir . $filename;

// 处理文件上传
if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
    if (preg_match('/\d{4}-\d{2}-\d{2}_\d{2}-\d{2}/', $filename)) {
        $backupTime = extractBackupTime($filename);
        archiveBackup($filename, $backupTime);
        echo "上传成功，已归档记录";
    } else {
        echo "上传成功，但文件名格式不符，未归档";
    }
} else {
    echo "上传失败";
}

// 提取时间
function extractBackupTime($filename) {
    if (preg_match('/(\d{4}-\d{2}-\d{2}_\d{2}-\d{2})/', $filename, $match)) {
        return str_replace('_', ' ', $match[1]) . ":00";
    }
    return date('Y-m-d H:i:s');
}

// 写入数据库
function archiveBackup($filename, $backupTime) {
    require '../config.php';
    $filePath = __DIR__ . '/../uploads/' . $filename;
    $fileSize = filesize($filePath);
    $ip = $_SERVER['REMOTE_ADDR'];

    try {
        $pdo = new PDO($dsn, $user, $pass);
        $stmt = $pdo->prepare("INSERT IGNORE INTO backups (filename, backup_time, file_size, uploader_ip) VALUES (?, ?, ?, ?)");
        $stmt->execute([$filename, $backupTime, $fileSize, $ip]);
    } catch (Exception $e) {
        // 若数据库失败，不影响上传
    }
}

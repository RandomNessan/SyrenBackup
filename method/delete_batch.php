<?php
require '../config.php';
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}

if (!empty($_POST['selected_ids'])) {
    $pdo = new PDO($dsn, $user, $pass);
    foreach ($_POST['selected_ids'] as $id) {
        $stmt = $pdo->prepare("SELECT filename FROM backups WHERE id = ?");
        $stmt->execute([$id]);
        $file = $stmt->fetchColumn();
        if ($file && file_exists("../uploads/$file")) {
            unlink("../uploads/$file");
        }
        $pdo->prepare("DELETE FROM backups WHERE id = ?")->execute([$id]);
    }
}

header("Location: ../dashboard.php");
exit;

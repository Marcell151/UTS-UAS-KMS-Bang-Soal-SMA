<?php
// download.php
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT file_path, original_name FROM questions WHERE id = ?");
    $stmt->execute([$id]);
    $fileInfo = $stmt->fetch();

    if ($fileInfo && !empty($fileInfo['file_path'])) {
        $filePath = 'upload/' . $fileInfo['file_path'];
        
        if (file_exists($filePath)) {
            // Update download count
            $update = $pdo->prepare("UPDATE questions SET download_count = download_count + 1 WHERE id = ?");
            $update->execute([$id]);

            // Force Download
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($fileInfo['original_name']).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            flush(); // Flush system output buffer
            readfile($filePath);
            exit;
        } else {
            echo "<script>alert('Maaf, file fisik tidak ditemukan di server.'); window.location.href='view-soal.php?id=$id';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Informasi file tidak ditemukan di database.'); window.location.href='bank-soal.php';</script>";
        exit();
    }
} else {
    header('Location: bank-soal.php');
    exit();
}

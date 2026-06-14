<?php
// archive_soal.php
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'] ?? null;
$identityId = getIdentityId();

if ($id) {
    // Cek apakah soal ini ada dan siapa uploadernya
    $stmt = $pdo->prepare("SELECT uploader_id, title FROM questions WHERE id = ? AND is_archived = 0");
    $stmt->execute([$id]);
    $q = $stmt->fetch();

    if ($q) {
        // Hanya Uploader asli atau Admin Akademik / Superadmin yang bisa arsipkan
        if ($q['uploader_id'] == $identityId || hasRoleId([ROLE_ADMIN_AKADEMIK, ROLE_ADMIN_SISTEM])) {
            $update = $pdo->prepare("UPDATE questions SET is_archived = 1 WHERE id = ?");
            if ($update->execute([$id])) {
                // Log action
                $log = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
                $log->execute([$identityId, "Mengarsipkan soal: " . $q['title'], $_SERVER['REMOTE_ADDR']]);
                
                header("Location: bank-soal.php?archived=1");
                exit();
            }
        } else {
            echo "<script>alert('Anda tidak memiliki akses untuk mengarsipkan soal ini.'); window.location.href='view-soal.php?id=$id';</script>";
            exit();
        }
    }
}

header("Location: bank-soal.php");
exit();

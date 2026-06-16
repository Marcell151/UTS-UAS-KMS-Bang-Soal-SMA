<?php
// actions/unarchive_explicit.php
require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

// Hanya Admin Akademik dan Admin Sistem yang boleh memulihkan arsip
checkRoleId([ROLE_ADMIN_AKADEMIK, ROLE_ADMIN_SISTEM]);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Periksa apakah dokumen eksis
    $stmt = $pdo->prepare("SELECT id FROM kms_explicit WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch();
    
    if ($doc) {
        // Set is_archived = 0 (Restore)
        $update = $pdo->prepare("UPDATE kms_explicit SET is_archived = 0 WHERE id = ?");
        $update->execute([$id]);
        
        // Redirect kembali ke halaman arsip
        header('Location: ../templates.php?view=archive&msg=restored');
        exit();
    } else {
        header('Location: ../templates.php?view=archive&msg=error');
        exit();
    }
} else {
    header('Location: ../templates.php?view=archive');
    exit();
}

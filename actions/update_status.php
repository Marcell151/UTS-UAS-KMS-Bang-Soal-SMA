<?php
// actions/update_status.php
require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

// Only Admin Akademik and Kepala Sekolah can update status
checkRoleId([ROLE_ADMIN_AKADEMIK, ROLE_KEPSEK]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = $_POST['question_id'];
    $new_status = $_POST['status'];
    $notes = $_POST['notes'] ?? '';
    $identityId = getIdentityId();
    $role_id = $_SESSION['role_id'];

    // Basic Validation
    if (!in_array($new_status, [STATUS_DRAFT, STATUS_REVIEW, STATUS_VERIFIED])) {
        die("Status tidak valid.");
    }

    // Role-based status restrictions
    if ($role_id == ROLE_ADMIN_AKADEMIK && !in_array($new_status, [STATUS_DRAFT, STATUS_REVIEW])) {
        die("Admin Akademik hanya dapat mengajukan Review atau mengembalikan ke Draft.");
    }

    if ($role_id == ROLE_KEPSEK && !in_array($new_status, [STATUS_REVIEW, STATUS_VERIFIED])) {
        die("Kepala Sekolah hanya dapat memverifikasi atau mengembalikan ke status Review.");
    }

    try {
        $pdo->beginTransaction();

        // Get old status
        $stmt = $pdo->prepare("SELECT status FROM questions WHERE id = ?");
        $stmt->execute([$question_id]);
        $old_status = $stmt->fetchColumn();

        // Update status
        $stmt = $pdo->prepare("UPDATE questions SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $question_id]);

        // [NEW] Get uploader and title for notification
        $stmt_info = $pdo->prepare("SELECT uploader_id, title FROM questions WHERE id = ?");
        $stmt_info->execute([$question_id]);
        $q_info = $stmt_info->fetch();

        // Log status change
        $stmt = $pdo->prepare("INSERT INTO question_status_logs (question_id, actor_id, old_status, new_status, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$question_id, $identityId, $old_status, $new_status, $notes]);

        // [NEW] Trigger Notification
        if ($q_info && $q_info['uploader_id'] && $q_info['uploader_id'] != $identityId) {
            addNotification($pdo, $q_info['uploader_id'], "Status soal '{$q_info['title']}' diperbarui menjadi $new_status.", "view-soal.php?id=$question_id");
        }

        // Activity Log
        $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
        $stmt->execute([$identityId, "Mengubah status soal ID $question_id menjadi $new_status", $_SERVER['REMOTE_ADDR']]);

        $pdo->commit();
        header("Location: ../view-soal.php?id=$question_id&update=success");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
?>

<?php
// actions/update_status.php
require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../includes/auth.php';

// Role check is handled dynamically below

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

    // Get old status and uploader
    $stmt = $pdo->prepare("SELECT status, uploader_id FROM questions WHERE id = ?");
    $stmt->execute([$question_id]);
    $q_data = $stmt->fetch();
    
    if (!$q_data) die("Soal tidak ditemukan.");
    $old_status = $q_data['status'];

    // Role-based status restrictions
    if (hasRoleId([ROLE_ADMIN_AKADEMIK, ROLE_ADMIN_SISTEM])) {
        // Admin Akademik can change status
        if ($new_status == STATUS_DRAFT && empty(trim($notes))) {
            echo "<script>alert('Catatan/Pesan wajib diisi jika mengembalikan soal ke Draft.'); window.history.back();</script>";
            exit();
        }
    } else if ($identityId == $q_data['uploader_id']) {
        // Uploader can only change from Draft to Review
        if ($old_status == STATUS_DRAFT && $new_status == STATUS_REVIEW) {
            // Allowed
        } else {
            die("Anda hanya diizinkan mengirim soal untuk di-review.");
        }
    } else {
        die("Anda tidak memiliki hak akses untuk mengubah status soal ini.");
    }

    try {
        $pdo->beginTransaction();

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
        if ($new_status == STATUS_REVIEW && $old_status == STATUS_DRAFT && $identityId == $q_info['uploader_id']) {
            // Guru mengirim ulang ke Admin
            $stmt_admin = $pdo->prepare("SELECT identity_id FROM staff WHERE role_id = ?");
            $stmt_admin->execute([ROLE_ADMIN_AKADEMIK]);
            $admins = $stmt_admin->fetchAll();
            foreach ($admins as $admin) {
                addNotification($pdo, $admin['identity_id'], "Soal '{$q_info['title']}' diajukan untuk direview.", "view-soal.php?id=$question_id");
            }
        } else if ($q_info && $q_info['uploader_id'] && $q_info['uploader_id'] != $identityId) {
            // Admin mengubah status, beritahu pembuat soal
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

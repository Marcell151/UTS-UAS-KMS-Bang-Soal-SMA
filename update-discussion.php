<?php
// update-discussion.php
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $discussion_id = $_POST['discussion_id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');
    $identityId = getIdentityId();

    if (!$discussion_id || empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }

    try {
        // Cek kepemilikan komentar
        $stmt = $pdo->prepare("SELECT actor_id FROM discussions WHERE id = ?");
        $stmt->execute([$discussion_id]);
        $disc = $stmt->fetch();

        if (!$disc) {
            echo json_encode(['success' => false, 'message' => 'Diskusi tidak ditemukan']);
            exit();
        }

        if ($disc['actor_id'] != $identityId) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak berhak mengedit komentar ini']);
            exit();
        }

        // Update komentar
        $stmt = $pdo->prepare("UPDATE discussions SET comment = ?, is_edited = 1 WHERE id = ?");
        $stmt->execute([$comment, $discussion_id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan database']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

<?php
// update-forum-reply.php
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply_id = $_POST['reply_id'] ?? null;
    $message = trim($_POST['message'] ?? '');
    $identityId = getIdentityId();

    if (!$reply_id || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit();
    }

    try {
        // Cek kepemilikan balasan
        $stmt = $pdo->prepare("SELECT actor_id FROM forum_replies WHERE id = ?");
        $stmt->execute([$reply_id]);
        $reply = $stmt->fetch();

        if (!$reply) {
            echo json_encode(['success' => false, 'message' => 'Balasan tidak ditemukan']);
            exit();
        }

        if ($reply['actor_id'] != $identityId) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak berhak mengedit balasan ini']);
            exit();
        }

        // Update balasan
        $stmt = $pdo->prepare("UPDATE forum_replies SET message = ?, is_edited = 1 WHERE id = ?");
        $stmt->execute([$message, $reply_id]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan database']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

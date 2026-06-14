<?php
// read_notif.php
require_once 'config/database.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'] ?? null;
$redirect = $_GET['redirect'] ?? 'index.php';
$actor_id = getIdentityId();

if ($id) {
    // Ensure the notification belongs to the current user
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND actor_id = ?");
    $stmt->execute([$id, $actor_id]);
}

// Redirect securely
header("Location: " . filter_var($redirect, FILTER_SANITIZE_URL));
exit();

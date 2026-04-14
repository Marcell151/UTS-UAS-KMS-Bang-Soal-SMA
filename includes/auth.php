<?php
// includes/auth.php
session_start();
ob_start(); // Buffer output to prevent "Headers already sent"

// Actor Types
define('ACTOR_STAFF', 'STAFF');
define('ACTOR_TEACHER', 'TEACHER');

/**
 * Returns the unified identity ID of the logged-in actor.
 */
function getIdentityId() {
    return $_SESSION['identity_id'] ?? null;
}

function isLoggedIn() {
    return isset($_SESSION['identity_id']);
}

/**
 * Verifies password or PIN in plain text (as requested per user requirements).
 */
function verifyAuth($input, $stored) {
    return $input === $stored;
}

/**
 * Checks if the user has one of the allowed role IDs.
 * Role 3 is logically mapped to ACTOR_TEACHER.
 */
function checkRoleId($allowedRoleIds) {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
    
    $currentRoleId = ($_SESSION['actor_type'] === ACTOR_TEACHER) ? 3 : $_SESSION['role_id'];
    
    if (!in_array($currentRoleId, $allowedRoleIds)) {
        header('Location: index.php?error=unauthorized');
        exit();
    }
}

/**
 * Returns true if the user has one of the allowed role IDs.
 */
function hasRoleId($allowedRoleIds) {
    if (!isLoggedIn()) return false;
    $currentRoleId = ($_SESSION['actor_type'] === ACTOR_TEACHER) ? 3 : $_SESSION['role_id'];
    return in_array($currentRoleId, $allowedRoleIds);
}

/**
 * Generic helper to get the display name of any identity.
 */
function getActorName($pdo, $identityId) {
    if (!$identityId) return "Anonim / Ex-Guru";
    
    // Check Staff first
    $stmt = $pdo->prepare("SELECT full_name FROM staff WHERE identity_id = ?");
    $stmt->execute([$identityId]);
    $name = $stmt->fetchColumn();
    
    if ($name) return $name;
    
    // Then check Teachers
    $stmt = $pdo->prepare("SELECT full_name FROM teachers WHERE identity_id = ?");
    $stmt->execute([$identityId]);
    return $stmt->fetchColumn() ?: "Anonim / Ex-Guru";
}

function addNotification($pdo, $recipientId, $message, $link = '#') {
    $stmt = $pdo->prepare("INSERT INTO notifications (actor_id, message, link) VALUES (?, ?, ?)");
    return $stmt->execute([$recipientId, $message, $link]);
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

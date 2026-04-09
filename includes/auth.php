<?php
// includes/auth.php
session_start();
ob_start(); // Buffer output to prevent "Headers already sent"

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Checks if the user has one of the allowed roles.
 * Must be called BEFORE any HTML output.
 */
function checkRole($allowedRoles) {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
    
    if (!in_array($_SESSION['role_name'], $allowedRoles)) {
        header('Location: index.php?error=unauthorized');
        exit();
    }
}

/**
 * Returns true if the user has one of the allowed roles.
 * Useful for showing/hiding UI elements.
 */
function hasRole($allowedRoles) {
    if (!isLoggedIn()) return false;
    return in_array($_SESSION['role_name'], $allowedRoles);
}

function logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

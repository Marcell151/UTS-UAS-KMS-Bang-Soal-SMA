<?php
// includes/header.php
require_once 'config/database.php';
require_once 'includes/auth.php';

// If a page hasn't called checkRole yet, at least ensure they are logged in.
// Specific role checks should be done in individual pages before including this header.
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KMS Bank Soal - SMA Kristen Kalam Kudus Malang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@700&display=swap" rel="stylesheet">
    <!-- TinyMCE CDN -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .heading-font { font-family: 'Manrope', sans-serif; }
        .bg-primary { background-color: #0053dc; }
        .text-primary { color: #0053dc; }
        .sidebar-item-active { background-color: rgba(0, 83, 220, 0.1); color: #0053dc; border-right: 4px solid #0053dc; }
    </style>
</head>
<body class="bg-[#F8F9FA]">
    <div class="flex min-h-screen">
        <?php include 'sidebar.php'; ?>
        
        <div class="flex-1">
            <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-8 sticky top-0 z-10">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-bold text-gray-800"><?php echo $pageTitle ?? 'Dashboard'; ?></h2>
                </div>
                <!-- ... Rest of Topbar ... -->
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-3 border-l pl-6 border-gray-100">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900"><?php echo $_SESSION['full_name']; ?></p>
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-widest"><?php echo $_SESSION['role_name']; ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-8">

<?php
// includes/header.php
require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'includes/auth.php';

// If a page hasn't called checkRole yet, at least ensure they are logged in.
// Specific role checks should be done in individual pages before including this header.
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Fetch unread notifications for current user
$identity_notif_id = getIdentityId();
$stmt_notif = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE actor_id = ? AND is_read = FALSE");
$stmt_notif->execute([$identity_notif_id]);
$unreadCount = $stmt_notif->fetchColumn();

// Fetch latest 5 notifications
$stmt_notif_list = $pdo->prepare("SELECT * FROM notifications WHERE actor_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt_notif_list->execute([$identity_notif_id]);
$notifications = $stmt_notif_list->fetchAll();
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
                <!-- Topbar Actions -->
                <div class="flex items-center space-x-6">
                    <!-- Notifications Bell -->
                    <div class="relative group">
                        <button class="p-2.5 text-gray-400 hover:bg-blue-50 hover:text-primary rounded-xl transition duration-200 relative">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <?php if ($unreadCount > 0): ?>
                            <span class="absolute top-2 right-2 w-4 h-4 bg-red-500 border-2 border-white rounded-full text-[10px] text-white flex items-center justify-center font-bold">
                                <?php echo $unreadCount; ?>
                            </span>
                            <?php endif; ?>
                        </button>
                        
                        <!-- Notifications Dropdown -->
                        <div class="absolute right-0 mt-2 w-80 bg-white border border-gray-100 rounded-[32px] shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 p-2">
                            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Internal Alerts</h4>
                                <span class="text-[9px] bg-blue-50 text-primary px-2 py-0.5 rounded-lg font-bold"><?php echo $unreadCount; ?> NEW</span>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <?php if (empty($notifications)): ?>
                                    <div class="p-10 text-center text-[11px] text-gray-400 italic">No new alerts.</div>
                                <?php endif; ?>
                                <?php foreach ($notifications as $notif): ?>
                                <a href="<?php echo $notif['link']; ?>" class="block p-5 hover:bg-gray-50 rounded-2xl transition <?php echo !$notif['is_read'] ? 'bg-blue-50/30' : ''; ?>">
                                    <p class="text-xs text-gray-700 leading-snug <?php echo !$notif['is_read'] ? 'font-bold' : ''; ?>"><?php echo $notif['message']; ?></p>
                                    <p class="text-[9px] text-gray-400 mt-2 font-bold uppercase tracking-widest italic"><?php echo date('H:i, d M Y', strtotime($notif['created_at'])); ?></p>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="p-4 border-t border-gray-50 text-center">
                                <a href="#" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">View All Activity</a>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 border-l pl-6 border-gray-100">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900"><?php echo $_SESSION['full_name']; ?></p>
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-widest"><?php echo $_SESSION['role_name']; ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>
            <main class="p-8">

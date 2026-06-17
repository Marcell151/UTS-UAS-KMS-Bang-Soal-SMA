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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#003366', // Kalam Kudus Navy
                        accent: '#E30613',  // Kalam Kudus Red
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; color: #1E293B; }
        .sidebar-item-active { background-color: #003366; color: white; box-shadow: 0 10px 15px -3px rgba(0, 51, 102, 0.2); }
        .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.3); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #CBD5E1; }
        
        /* TinyMCE / Rich Text Editor Reset */
        .editor-content p { margin-bottom: 1.25em; line-height: 1.7; }
        .editor-content ul { list-style-type: disc; padding-left: 1.5em; margin-bottom: 1.25em; }
        .editor-content ol { list-style-type: decimal; padding-left: 1.5em; margin-bottom: 1.25em; }
        .editor-content li { margin-bottom: 0.5em; }
        .editor-content h1, .editor-content h2, .editor-content h3 { font-weight: 800; margin-top: 1.5em; margin-bottom: 0.75em; color: #003366; }
        .editor-content strong { font-weight: 800; color: #1E293B; }
        
        /* CKEditor Custom Styling */
        .ck-editor__editable_inline { min-height: 300px; border-bottom-left-radius: 32px !important; border-bottom-right-radius: 32px !important; padding: 2rem !important; }
        .ck-toolbar { border-top-left-radius: 32px !important; border-top-right-radius: 32px !important; background: #f8fafc !important; border: 1px solid #e2e8f0 !important; border-bottom: none !important; }
        .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) { border-color: #e2e8f0 !important; }
    </style>
</head>
<body class="bg-[#F8FAFC]">
    <!-- Mobile sidebar backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 hidden lg:hidden transition-opacity" onclick="toggleSidebar()"></div>

    <div class="flex min-h-screen">
        <?php include 'sidebar.php'; ?>
        
        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-20 bg-white border-b border-gray-100 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-30">
                <!-- Hamburger Menu Button -->
                <button onclick="toggleSidebar()" class="p-2 -ml-2 mr-2 text-gray-500 hover:bg-gray-50 hover:text-primary rounded-xl transition duration-200 focus:outline-none">
                    <!-- Mobile Hamburger -->
                    <svg id="icon-burger" class="w-6 h-6 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <!-- Desktop Arrow -->
                    <svg id="icon-arrow" class="w-6 h-6 hidden lg:block transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <script>
                    if (window.innerWidth >= 1024 && localStorage.getItem('sidebarPinned') === 'false') {
                        document.getElementById('icon-arrow').classList.add('rotate-180');
                    }
                </script>
                
                <!-- Topbar Actions -->
                <div class="flex items-center space-x-4 lg:space-x-6">
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
                        <div class="absolute right-[-20px] sm:right-0 mt-2 w-[280px] sm:w-80 bg-white border border-gray-100 rounded-[32px] shadow-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 p-2">
                            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Internal Alerts</h4>
                                <span class="text-[9px] bg-blue-50 text-primary px-2 py-0.5 rounded-lg font-bold"><?php echo $unreadCount; ?> NEW</span>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <?php if (empty($notifications)): ?>
                                    <div class="p-10 text-center text-[11px] text-gray-400 italic">No new alerts.</div>
                                <?php endif; ?>
                                <?php foreach ($notifications as $notif): ?>
                                <a href="read_notif.php?id=<?php echo $notif['id']; ?>&redirect=<?php echo urlencode($notif['link'] ?? '#'); ?>" class="block p-5 hover:bg-gray-50 rounded-2xl transition <?php echo !$notif['is_read'] ? 'bg-blue-50/30' : ''; ?>">
                                    <p class="text-xs text-gray-700 leading-snug <?php echo !$notif['is_read'] ? 'font-bold' : ''; ?>"><?php echo $notif['message']; ?></p>
                                    <p class="text-[9px] text-gray-400 mt-2 font-bold uppercase tracking-widest italic"><?php echo date('H:i, d M Y', strtotime($notif['created_at'])); ?></p>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="p-4 border-t border-gray-50 text-center">
                                <a href="notifications.php" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">View All Activity</a>
                            </div>
                        </div>
                    </div>

                    <a href="profile.php" class="flex items-center space-x-3 border-l pl-4 sm:pl-6 border-gray-100 group cursor-pointer">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-900 group-hover:text-primary transition-colors"><?php echo $_SESSION['full_name']; ?></p>
                            <p class="text-xs text-gray-500 uppercase font-bold tracking-widest"><?php echo $_SESSION['role_name']; ?></p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold shrink-0 group-hover:ring-4 group-hover:ring-blue-50 transition-all">
                            <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
                        </div>
                    </a>
                </div>
            </header>
            <main class="p-8">

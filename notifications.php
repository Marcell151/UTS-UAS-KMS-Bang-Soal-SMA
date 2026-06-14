<?php
$pageTitle = 'Semua Notifikasi & Aktivitas';
require_once 'includes/header.php';

$identityId = getIdentityId();

// Handle Mark as Read
if (isset($_GET['read_all'])) {
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE actor_id = ?");
    $stmt->execute([$identityId]);
    header('Location: notifications.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE actor_id = ? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$identityId]);
$all_notifications = $stmt->fetchAll();
?>

<div class="max-w-4xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-black text-[#003366] tracking-tighter leading-none">Pusat Notifikasi</h2>
            <p class="text-[10px] text-gray-400 font-black uppercase tracking-[0.2em] mt-3">Daftar Aktivitas & Pemberitahuan Terbaru</p>
        </div>
        <a href="notifications.php?read_all=1" class="px-6 py-3.5 bg-blue-50 text-primary font-bold text-[11px] uppercase tracking-widest rounded-xl hover:bg-primary hover:text-white transition-all shadow-sm border border-blue-100 whitespace-nowrap">Tandai Semua Dibaca</a>
    </div>

    <div class="bg-white rounded-[32px] border border-gray-100 shadow-2xl shadow-blue-50 overflow-hidden">
        <?php if (empty($all_notifications)): ?>
            <div class="p-20 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-3xl mx-auto flex items-center justify-center mb-6 shadow-inner">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <p class="text-gray-400 text-sm font-medium">Belum ada riwayat aktivitas atau pemberitahuan baru.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-50">
                <?php foreach ($all_notifications as $notif): ?>
                <a href="<?php echo htmlspecialchars($notif['link'] ?? '#'); ?>" class="group flex items-start p-8 hover:bg-gray-50 transition duration-300 <?php echo !$notif['is_read'] ? 'bg-blue-50/30' : ''; ?>">
                    <div class="w-12 h-12 rounded-[20px] flex items-center justify-center shrink-0 mr-6 shadow-sm group-hover:scale-110 transition-transform duration-300 <?php echo !$notif['is_read'] ? 'bg-red-50 text-red-500' : 'bg-gray-50 text-gray-400'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-800 <?php echo !$notif['is_read'] ? 'font-black' : 'font-medium'; ?> leading-relaxed">
                            <?php echo htmlspecialchars($notif['message']); ?>
                        </p>
                        <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mt-2 flex items-center">
                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <?php echo date('d M Y, H:i', strtotime($notif['created_at'])); ?>
                        </p>
                    </div>
                    <?php if (!$notif['is_read']): ?>
                        <div class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-sm mt-2 shadow-red-200"></div>
                    <?php endif; ?>
                </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

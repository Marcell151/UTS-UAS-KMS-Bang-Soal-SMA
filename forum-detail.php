<?php
// forum-detail.php
$pageTitle = 'Diskusi Kolaboratif';
require_once 'includes/header.php';

// Check Role: Tacit knowledge sharing is for academic staff (Guru & Admin Akademik)
checkRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK, ROLE_ADMIN_SISTEM]);

$topic_id = $_GET['id'] ?? null;
if (!$topic_id) {
    header('Location: forum.php');
    exit();
}

// Handle New Reply
if (isset($_POST['post_reply'])) {
    $message = $_POST['message'];
    $identityId = getIdentityId();
    
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO forum_replies (topic_id, actor_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$topic_id, $identityId, $message]);
        
        // Log activity
        $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
        $stmt->execute([$identityId, "Membalas diskusi forum ID $topic_id", $_SERVER['REMOTE_ADDR']]);

        // [NEW] Notification Logic: Notify the Topic Owner
        $stmt_top = $pdo->prepare("SELECT title, actor_id FROM forum_topics WHERE id = ?");
        $stmt_top->execute([$topic_id]);
        $top_info = $stmt_top->fetch();

        if ($top_info && $top_info['actor_id'] && $top_info['actor_id'] != $identityId) {
            addNotification($pdo, $top_info['actor_id'], "Topik '{$top_info['title']}' Anda mendapat balasan baru.", "forum-detail.php?id=$topic_id");
        }
    }
}

// Fetch Topic
$stmt = $pdo->prepare("SELECT ft.*, 
                       COALESCE(
                           (SELECT full_name FROM staff WHERE identity_id = ft.actor_id),
                           (SELECT full_name FROM teachers WHERE identity_id = ft.actor_id)
                       ) as full_name,
                       COALESCE(
                           (SELECT r.role_name FROM staff s JOIN roles r ON s.role_id = r.id WHERE s.identity_id = ft.actor_id),
                           'Guru'
                       ) as role_name
                       FROM forum_topics ft 
                       WHERE ft.id = ?");
$stmt->execute([$topic_id]);
$topic = $stmt->fetch();

if (!$topic) {
    header('Location: forum.php');
    exit();
}

// Fetch Replies
$stmt = $pdo->prepare("SELECT fr.*, 
                       COALESCE(
                           (SELECT full_name FROM staff WHERE identity_id = fr.actor_id),
                           (SELECT full_name FROM teachers WHERE identity_id = fr.actor_id)
                       ) as full_name,
                       COALESCE(
                           (SELECT r.role_name FROM staff s JOIN roles r ON s.role_id = r.id WHERE s.identity_id = fr.actor_id),
                           'Guru'
                       ) as role_name
                       FROM forum_replies fr 
                       WHERE fr.topic_id = ? 
                       ORDER BY fr.created_at ASC");
$stmt->execute([$topic_id]);
$replies = $stmt->fetchAll();
?>

<div class="max-w-5xl mx-auto space-y-10">
    <!-- Question/Topic Header -->
    <div class="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
            <a href="forum.php" class="flex items-center text-primary font-bold text-xs group">
                <div class="p-2 bg-white rounded-xl shadow-sm mr-3 group-hover:bg-primary group-hover:text-white transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </div>
                Kembali ke Forum
            </a>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">Thread Context #<?php echo str_pad($topic['id'], 4, '0', STR_PAD_LEFT); ?></span>
        </div>
        <div class="p-12">
            <h2 class="text-4xl font-bold text-gray-900 leading-tight mb-10"><?php echo $topic['title']; ?></h2>
            <div class="flex items-center space-x-5">
                <div class="w-14 h-14 rounded-[20px] bg-primary text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-blue-200"><?php echo strtoupper(substr($topic['full_name'], 0, 1)); ?></div>
                <div>
                    <p class="text-lg font-bold text-gray-900 leading-none"><?php echo $topic['full_name']; ?></p>
                    <div class="flex items-center space-x-3 mt-2">
                        <span class="px-3 py-1 bg-blue-50 text-primary text-[9px] font-bold rounded-full uppercase tracking-widest border border-blue-100"><?php echo $topic['role_name']; ?></span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight"><?php echo date('d M Y, H:i', strtotime($topic['created_at'])); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Replies Section -->
    <div class="space-y-8 relative">
        <!-- Vertical Progress Line -->
        <div class="absolute left-7 top-0 bottom-0 w-[1px] bg-gray-100 hidden md:block"></div>

        <?php if (empty($replies)): ?>
        <div class="bg-gray-50 rounded-[32px] p-20 text-center border border-dashed border-gray-200">
             <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            <p class="text-gray-400 italic">Belum ada tanggapan untuk topik ini. Berikan perspektif Anda!</p>
        </div>
        <?php endif; ?>

        <?php foreach ($replies as $reply): ?>
        <div class="flex space-x-6 relative z-10 group">
            <div class="flex-shrink-0 w-14 h-14 rounded-[22px] bg-white border border-gray-100 shadow-sm flex items-center justify-center text-primary font-bold text-lg group-hover:scale-110 transition-transform duration-300"><?php echo strtoupper(substr($reply['full_name'], 0, 1)); ?></div>
            <div class="flex-1 bg-white border border-gray-100 shadow-xl shadow-gray-50 rounded-[32px] p-10 group-hover:border-primary/20 transition-all duration-300">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <span class="text-base font-bold text-gray-900"><?php echo $reply['full_name']; ?></span>
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1"><?php echo $reply['role_name']; ?></p>
                    </div>
                    <div class="text-right">
                         <p class="text-[10px] text-gray-400 font-bold"><?php echo date('d M, H:i', strtotime($reply['created_at'])); ?></p>
                    </div>
                </div>
                <div class="text-gray-700 text-sm leading-relaxed whitespace-pre-line bg-gray-50/50 p-6 rounded-2xl border border-gray-50 italic">
                    <?php echo $reply['message']; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Reply Form (Premium Dark Block) -->
    <?php if (hasRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK])): ?>
    <div class="bg-gray-900 rounded-[48px] p-12 text-white shadow-2xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary rounded-full blur-[120px] opacity-20"></div>
        <div class="relative z-10">
            <h3 class="text-3xl font-bold mb-8 flex items-center leading-none">
                <div class="p-3 bg-white bg-opacity-10 rounded-2xl mr-4 border border-white border-opacity-5">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l5 5m-5-5l5-5"></path></svg>
                </div>
                Kontribusi Tanggapan
            </h3>
            <form action="" method="POST" class="space-y-8">
                <textarea name="message" required rows="5" placeholder="Bagikan wawasan, solusi, atau pengalaman akademik Anda di sini..." class="w-full bg-white bg-opacity-[0.03] border border-white border-opacity-5 rounded-[28px] p-8 text-base text-white focus:bg-opacity-[0.06] focus:ring-2 focus:ring-blue-500 outline-none placeholder-gray-600 transition shadow-inner"></textarea>
                <div class="flex items-center justify-between">
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest flex items-center">
                        <svg class="w-3 h-3 mr-2 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                        Partisipasi Anda berkontribusi pada aset Tacit sekolah
                    </p>
                    <button type="submit" name="post_reply" class="bg-primary hover:bg-black text-white px-12 py-4 rounded-2xl font-bold transition-all shadow-xl shadow-blue-900 active:scale-95">Posting Balasan</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>

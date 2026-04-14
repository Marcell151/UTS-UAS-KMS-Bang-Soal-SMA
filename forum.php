<?php
// forum.php
$pageTitle = 'Forum Kolaborasi Guru';
require_once 'includes/header.php';

// Check Role: Tacit knowledge sharing is for academic staff (Guru & Admin Akademik)
// Kepala Sekolah is restricted from participating in general forum discussions
checkRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK]);

$message = '';

// Handle New Topic
if (isset($_POST['add_topic'])) {
    $title = $_POST['title'];
    $identityId = getIdentityId();
    
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO forum_topics (title, actor_id) VALUES (?, ?)");
        $stmt->execute([$title, $identityId]);
        $message = 'Topik diskusi berhasil dimulai.';
        
        // Log activity
        $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
        $stmt->execute([$identityId, "Memulai diskusi forum: $title", $_SERVER['REMOTE_ADDR']]);
    }
}

// Fetch Topics
$stmt = $pdo->query("SELECT ft.*, 
                    COALESCE(
                        (SELECT full_name FROM staff WHERE identity_id = ft.actor_id),
                        (SELECT full_name FROM teachers WHERE identity_id = ft.actor_id)
                    ) as full_name,
                    COALESCE(
                        (SELECT r.role_name FROM staff s JOIN roles r ON s.role_id = r.id WHERE s.identity_id = ft.actor_id),
                        'Guru'
                    ) as role_name,
                    (SELECT COUNT(*) FROM forum_replies WHERE topic_id = ft.id) as reply_count 
                    FROM forum_topics ft 
                    ORDER BY ft.created_at DESC");
$topics = $stmt->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0 italic">
    <div>
        <h3 class="text-3xl font-bold text-gray-900 leading-none">Forum Kolaborasi</h3>
        <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold mt-3 italic">Tacit Knowledge Sharing • Ruang Berbagi Rekan Sejawat</p>
    </div>
    <button onclick="document.getElementById('topicModal').classList.remove('hidden')" class="bg-primary text-white px-8 py-4 rounded-[20px] font-bold hover:bg-black transition shadow-xl shadow-blue-100 flex items-center group">
        <svg class="w-5 h-5 mr-3 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        Mulai Diskusi Baru
    </button>
</div>

<?php if ($message): ?>
<div class="mb-8 bg-blue-50 text-blue-600 p-6 rounded-[28px] text-sm border border-blue-100 flex items-center shadow-sm italic">
     <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
    <?php echo $message; ?>
</div>
<?php endif; ?>

<div class="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden mb-12 italic">
    <div class="p-8 border-b border-gray-50 bg-gray-50/30 text-[10px] font-bold text-gray-400 uppercase tracking-widest flex px-12">
        <div class="flex-1">Topik & Konteks Diskusi</div>
        <div class="w-32 text-center">Interaksi</div>
        <div class="w-64 text-right">Inisiator</div>
    </div>

    <div class="divide-y divide-gray-50">
        <?php if (empty($topics)): ?>
        <div class="p-24 text-center">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            <p class="text-gray-400 italic">Belum ada topik diskusi. Jadilah yang pertama memulai kolaborasi.</p>
        </div>
        <?php endif; ?>
        <?php foreach ($topics as $t): ?>
        <a href="forum-detail.php?id=<?php echo $t['id']; ?>" class="px-12 py-8 flex items-center hover:bg-gray-50 transition-all duration-300 group">
            <div class="flex-1">
                <h4 class="text-xl font-bold text-gray-900 group-hover:text-primary transition-colors leading-tight mb-2"><?php echo $t['title']; ?></h4>
                <div class="flex items-center space-x-3 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                    <span><?php echo date('d M Y', strtotime($t['created_at'])); ?></span>
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    <span>Knowledge Thread #<?php echo $t['id']; ?></span>
                </div>
            </div>
            <div class="w-32 text-center">
                <span class="inline-flex items-center px-4 py-2 bg-white bg-opacity-50 text-xs font-bold text-primary rounded-2xl border border-blue-50 shadow-sm">
                    <svg class="w-3 h-3 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path></svg>
                    <?php echo $t['reply_count']; ?>
                </span>
            </div>
            <div class="w-64 text-right flex items-center justify-end space-x-4">
                <div>
                     <p class="text-xs font-bold text-gray-800 leading-none"><?php echo $t['full_name']; ?></p>
                     <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-1 italic"><?php echo $t['role_name']; ?></p>
                </div>
                <div class="w-12 h-12 rounded-[18px] bg-blue-50 text-primary flex items-center justify-center font-bold text-sm shadow-inner group-hover:scale-110 transition-transform">
                    <?php echo strtoupper(substr($t['full_name'], 0, 1)); ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Topic -->
<div id="topicModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[40px] max-w-lg w-full p-12 shadow-2xl relative overflow-hidden italic">
        <div class="absolute top-0 left-0 w-full h-2 bg-primary"></div>
        <h3 class="text-3xl font-bold text-gray-900 mb-2 leading-none">Mulai Kolaborasi</h3>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-10">Bagikan pemahaman atau diskusikan strategi akademik</p>
        
        <form action="" method="POST" class="space-y-6">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Judul Topik / Pertanyaan</label>
                <textarea name="title" required placeholder="Contoh: Bagaimana metode terbaik mengajarkan konsep Termodinamika di kelas XI?" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner transition min-h-[120px]"></textarea>
            </div>
            <div class="flex space-x-4 pt-8">
                <button type="button" onclick="document.getElementById('topicModal').classList.add('hidden')" class="flex-1 px-8 py-5 border border-gray-100 text-gray-400 rounded-3xl font-bold hover:bg-gray-100 transition">Batal</button>
                <button type="submit" name="add_topic" class="flex-1 px-8 py-5 bg-primary text-white rounded-3xl font-bold hover:bg-gray-900 transition shadow-xl shadow-blue-100">Kirim Ke Forum</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
// forum.php
$pageTitle = 'Forum Diskusi (Tacit Knowledge)';
require_once 'includes/header.php';

// Check Role
checkRole(['Guru', 'Admin Akademik', 'Kepala Sekolah']);

$message = '';

// Handle New Topic
if (isset($_POST['add_topic'])) {
    $title = $_POST['title'];
    $userId = $_SESSION['user_id'];
    
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO forum_topics (title, user_id) VALUES (?, ?)");
        $stmt->execute([$title, $userId]);
        $message = 'Topik diskusi berhasil dibuat.';
        
        // Log
        $stmt = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, ?)");
        $stmt->execute([$userId, "Created forum topic: $title"]);
    }
}

// Fetch Topics
$stmt = $pdo->query("SELECT ft.*, u.full_name, 
                    (SELECT COUNT(*) FROM forum_replies WHERE topic_id = ft.id) as reply_count 
                    FROM forum_topics ft 
                    JOIN users u ON ft.user_id = u.id 
                    ORDER BY ft.created_at DESC");
$topics = $stmt->fetchAll();
?>

<div class="mb-8 flex justify-between items-center">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Forum Kolaborasi Guru</h3>
        <p class="text-sm text-gray-500 mt-1">Ruang berbagi wawasan, pengalaman, dan ide akademik (Tacit Knowledge).</p>
    </div>
    <button onclick="document.getElementById('topicModal').classList.remove('hidden')" class="bg-primary text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
        Mulai Diskusi Baru
    </button>
</div>

<?php if ($message): ?>
<div class="mb-8 bg-blue-50 text-blue-600 p-4 rounded-xl text-sm italic"><?php echo $message; ?></div>
<?php endif; ?>

<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-8 border-b border-gray-100 bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest flex">
        <div class="flex-1">Topik Pembahasan</div>
        <div class="w-32 text-center">Balasan</div>
        <div class="w-48 text-right">Dibuat Oleh</div>
    </div>

    <div class="divide-y divide-gray-100 italic">
        <?php if (empty($topics)): ?>
        <div class="p-20 text-center text-gray-400">Belum ada topik diskusi yang dimulai.</div>
        <?php endif; ?>
        <?php foreach ($topics as $t): ?>
        <a href="forum-detail.php?id=<?php echo $t['id']; ?>" class="p-8 flex items-center hover:bg-gray-50 transition group">
            <div class="flex-1">
                <h4 class="text-lg font-bold text-gray-900 group-hover:text-primary transition-colors leading-tight mb-1"><?php echo $t['title']; ?></h4>
                <p class="text-xs text-gray-400"><?php echo date('d M Y, H:i', strtotime($t['created_at'])); ?></p>
            </div>
            <div class="w-32 text-center text-sm font-bold text-gray-500">
                <span class="px-3 py-1 bg-gray-100 rounded-full"><?php echo $t['reply_count']; ?></span>
            </div>
            <div class="w-48 text-right flex items-center justify-end space-x-3">
                <span class="text-xs font-semibold text-gray-700"><?php echo $t['full_name']; ?></span>
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-primary flex items-center justify-center font-bold text-xs"><?php echo substr($t['full_name'], 0, 1); ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Topic Modal -->
<div id="topicModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 font-primary">Mulai Topik Baru</h3>
        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul Pembahasan</label>
                <input type="text" name="title" required placeholder="Contoh: Strategi Pembuatan Soal HOTS Matematika" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
            </div>
            <div class="flex space-x-4 pt-4">
                <button type="button" onclick="document.getElementById('topicModal').classList.add('hidden')" class="flex-1 px-6 py-3 border border-gray-200 text-gray-600 rounded-xl font-semibold hover:bg-gray-50 transition">Batal</button>
                <button type="submit" name="add_topic" class="flex-1 px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-blue-700 transition">Buat Topik</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

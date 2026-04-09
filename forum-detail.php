<?php
// forum-detail.php
$pageTitle = 'Diskusi Forum';
require_once 'includes/header.php';

$topic_id = $_GET['id'] ?? null;
if (!$topic_id) {
    header('Location: forum.php');
    exit();
}

// Handle New Reply
if (isset($_POST['post_reply'])) {
    $message = $_POST['message'];
    $userId = $_SESSION['user_id'];
    
    if (!empty($message)) {
        $stmt = $pdo->prepare("INSERT INTO forum_replies (topic_id, user_id, message) VALUES (?, ?, ?)");
        $stmt->execute([$topic_id, $userId, $message]);
    }
}

// Fetch Topic
$stmt = $pdo->prepare("SELECT ft.*, u.full_name, r.role_name 
                      FROM forum_topics ft 
                      JOIN users u ON ft.user_id = u.id 
                      JOIN roles r ON u.role_id = r.id
                      WHERE ft.id = ?");
$stmt->execute([$topic_id]);
$topic = $stmt->fetch();

if (!$topic) {
    header('Location: forum.php');
    exit();
}

// Fetch Replies
$stmt = $pdo->prepare("SELECT fr.*, u.full_name, r.role_name 
                      FROM forum_replies fr 
                      JOIN users u ON fr.user_id = u.id 
                      JOIN roles r ON u.role_id = r.id
                      WHERE fr.topic_id = ? 
                      ORDER BY fr.created_at ASC");
$stmt->execute([$topic_id]);
$replies = $stmt->fetchAll();
?>

<div class="max-w-4xl mx-auto space-y-8">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
            <a href="forum.php" class="flex items-center text-primary font-bold text-sm">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Forum
            </a>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Topic ID: #<?php echo $topic['id']; ?></span>
        </div>
        <div class="p-8 italic">
            <h2 class="text-3xl font-bold text-gray-900 leading-tight mb-6"><?php echo $topic['title']; ?></h2>
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-primary flex items-center justify-center font-bold font-primary"><?php echo substr($topic['full_name'], 0, 1); ?></div>
                <div>
                    <p class="font-bold text-gray-900 leading-none"><?php echo $topic['full_name']; ?></p>
                    <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-widest"><?php echo $topic['role_name']; ?> • <?php echo date('d M Y, H:i', strtotime($topic['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Replies -->
    <div class="space-y-6">
        <?php foreach ($replies as $reply): ?>
        <div class="flex space-x-4">
            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-white shadow-sm"><?php echo substr($reply['full_name'], 0, 1); ?></div>
            <div class="flex-1 bg-white border border-gray-100 shadow-sm rounded-3xl p-6 italic">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm font-bold text-gray-900"><?php echo $reply['full_name']; ?> <span class="text-[10px] font-normal text-gray-400 ml-2">(<?php echo $reply['role_name']; ?>)</span></span>
                    <span class="text-[10px] text-gray-400"><?php echo date('d M, H:i', strtotime($reply['created_at'])); ?></span>
                </div>
                <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line"><?php echo $reply['message']; ?></p>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($replies)): ?>
        <p class="text-center text-gray-400 italic py-10">Belum ada diskusi di topik ini.</p>
        <?php endif; ?>
    </div>

    <!-- Reply Form -->
    <div class="bg-gray-900 rounded-3xl p-8 text-white shadow-xl">
        <h3 class="text-lg font-bold mb-6 flex items-center">
            <svg class="w-5 h-5 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            Berikan Tanggapan / Solusi
        </h3>
        <form action="" method="POST" class="space-y-6">
            <textarea name="message" required rows="4" placeholder="Bagikan perspektif akademik Anda di sini..." class="w-full bg-white bg-opacity-10 border-none rounded-2xl p-6 text-sm text-white focus:ring-2 focus:ring-blue-500 outline-none placeholder-gray-500 transition"></textarea>
            <div class="text-right">
                <button type="submit" name="post_reply" class="bg-primary hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg shadow-blue-900">Posting Balasan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

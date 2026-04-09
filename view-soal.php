<?php
// view-soal.php
$pageTitle = 'Detail Soal & Knowledge Sharing';
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: bank-soal.php');
    exit();
}

// Handle Status Update (Admin Akademik, TU, or Kepsek)
if (isset($_POST['update_status'])) {
    checkRole(['Admin Akademik', 'Administrator (TU)', 'Kepala Sekolah']);
    $newStatus = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE documents SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $id]);
}

// Handle Post Discussion (Tacit Knowledge)
if (isset($_POST['post_comment'])) {
    $comment = $_POST['comment'];
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("INSERT INTO discussions (document_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$id, $userId, $comment]);
}

// Fetch Document Data
$stmt = $pdo->prepare("SELECT d.*, c.name as category_name, u.full_name as uploader_name 
                       FROM documents d 
                       LEFT JOIN categories c ON d.category_id = c.id 
                       LEFT JOIN users u ON d.uploader_id = u.id 
                       WHERE d.id = ?");
$stmt->execute([$id]);
$doc = $stmt->fetch();

if (!$doc) {
    header('Location: bank-soal.php');
    exit();
}

// Fetch Discussions
$stmt = $pdo->prepare("SELECT di.*, u.full_name, r.role_name 
                       FROM discussions di 
                       JOIN users u ON di.user_id = u.id 
                       JOIN roles r ON u.role_id = r.id
                       WHERE di.document_id = ? 
                       ORDER BY di.created_at ASC");
$stmt->execute([$id]);
$discussions = $stmt->fetchAll();
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 italic">
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex justify-between items-start">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 leading-tight"><?php echo $doc['title']; ?></h2>
                    <div class="flex items-center space-x-4 mt-4">
                        <span class="px-3 py-1 bg-blue-50 text-sky-600 text-[10px] font-bold rounded-full uppercase tracking-widest"><?php echo $doc['category_name']; ?></span>
                        <span class="px-3 py-1 bg-gray-50 text-gray-500 border border-gray-200 text-[10px] font-bold rounded-full uppercase tracking-widest">KELAS <?php echo $doc['class_level']; ?></span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest <?php 
                        echo $doc['status'] == 'Verified' ? 'bg-teal-50 text-teal-600' : ($doc['status'] == 'Review' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-100 text-gray-400'); 
                    ?>">
                        <?php echo $doc['status']; ?>
                    </span>
                </div>
            </div>

            <div class="p-8">
                <div class="flex items-center justify-between p-6 bg-blue-50 border border-blue-100 rounded-3xl mb-10">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-white rounded-2xl text-primary shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900"><?php echo $doc['original_name']; ?></p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest"><?php echo $doc['file_type']; ?> File Content</p>
                        </div>
                    </div>
                    <a href="storage/documents/<?php echo $doc['file_path']; ?>" download="<?php echo $doc['original_name']; ?>" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-200">Unduh Soal</a>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pembahasan & Knowledge Detail (Explicit)
                    </h3>
                    <div class="bg-gray-50 p-8 rounded-[32px] border border-gray-100 text-gray-700 leading-relaxed italic prose max-w-none">
                        <?php echo $doc['explanation']; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tacit Knowledge: Discussion Section -->
        <div class="bg-white rounded-[40px] border border-gray-100 shadow-sm p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-8 flex items-center">
                <svg class="w-6 h-6 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                Diskusi Kolaboratif
            </h3>

            <div class="space-y-6 mb-10 h-[400px] overflow-y-auto pr-4">
                <?php if (empty($discussions)): ?>
                <p class="text-center text-gray-400 py-20">Belum ada diskusi untuk dokumen ini.</p>
                <?php endif; ?>
                <?php foreach ($discussions as $msg): ?>
                <div class="flex space-x-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-primary flex items-center justify-center font-bold text-xs shrink-0"><?php echo substr($msg['full_name'], 0, 1); ?></div>
                    <div class="flex-1 bg-gray-50 rounded-3xl p-6 relative">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-bold text-gray-900"><?php echo $msg['full_name']; ?> <span class="text-[10px] text-gray-400 font-normal ml-2 tracking-widest italic"><?php echo $msg['role_name']; ?></span></span>
                            <span class="text-[10px] text-gray-400"><?php echo date('d M, H:i', strtotime($msg['created_at'])); ?></span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed"><?php echo nl2br($msg['comment']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <form action="" method="POST" class="mt-8 pt-8 border-t border-gray-100 space-y-4">
                <textarea name="comment" required placeholder="Tuliskan saran atau wawasan tambahan terkait soal ini..." class="w-full px-6 py-4 bg-gray-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition"></textarea>
                <div class="text-right">
                    <button type="submit" name="post_comment" class="bg-primary text-white px-8 py-3 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg">Posting Komentar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="space-y-8">
        <?php if (in_array($_SESSION['role_name'], ['Admin Akademik', 'Administrator (TU)', 'Kepala Sekolah'])): ?>
        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Kontrol Validasi</h4>
            <form action="" method="POST" class="space-y-4">
                <select name="status" class="w-full px-4 py-3 border border-gray-200 rounded-2xl outline-none text-sm">
                    <option value="Draft" <?php echo $doc['status'] == 'Draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="Review" <?php echo $doc['status'] == 'Review' ? 'selected' : ''; ?>>Review</option>
                    <option value="Verified" <?php echo $doc['status'] == 'Verified' ? 'selected' : ''; ?>>Verified</option>
                </select>
                <button type="submit" name="update_status" class="w-full bg-gray-900 text-white py-3 rounded-2xl font-bold hover:bg-black transition">Update Status</button>
            </form>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Materi Terkait</h4>
            <p class="text-xl font-bold text-gray-900 leading-tight mb-2"><?php echo $doc['materi']; ?></p>
            <div class="mt-6 pt-6 border-t border-gray-50 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Tingkat Kesulitan</span>
                    <span class="text-xs font-bold text-primary"><?php echo $doc['difficulty']; ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Format File</span>
                    <span class="text-xs font-bold text-gray-900 uppercase"><?php echo $doc['file_type']; ?></span>
                </div>
            </div>
        </div>

        <div class="bg-gray-900 rounded-3xl p-8 text-white">
            <h4 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-6">Author Knowledge</h4>
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-white bg-opacity-10 rounded-xl flex items-center justify-center font-bold text-white"><?php echo substr($doc['uploader_name'], 0, 1); ?></div>
                <div>
                    <p class="text-sm font-bold text-white"><?php echo $doc['uploader_name']; ?></p>
                    <p class="text-[10px] text-gray-500">Guru Kontributor</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

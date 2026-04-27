<?php
// view-soal.php
$pageTitle = 'Detail Soal & Knowledge Sharing';
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: bank-soal.php');
    exit();
}

// Handle Post Discussion (Tacit Knowledge)
if (isset($_POST['post_comment'])) {
    $comment = $_POST['comment'];
    $identityId = getIdentityId();
    $stmt = $pdo->prepare("INSERT INTO discussions (question_id, actor_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$id, $identityId, $comment]);
    
    // Activity Log
    $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
    $stmt->execute([$identityId, "Menambahkan komentar pada soal ID $id", $_SERVER['REMOTE_ADDR']]);

    // [NEW] Notification Logic: Notify the Uploader
    // Fetch question title and uploader ID if not already available
    $stmt_q = $pdo->prepare("SELECT title, uploader_id FROM questions WHERE id = ?");
    $stmt_q->execute([$id]);
    $q_notif = $stmt_q->fetch();

    if ($q_notif && $q_notif['uploader_id'] && $q_notif['uploader_id'] != $identityId) {
        addNotification($pdo, $q_notif['uploader_id'], "Ada diskusi baru di soal '{$q_notif['title']}' Anda.", "view-soal.php?id=$id");
    }
}

// Fetch Question Data
$stmt = $pdo->prepare("SELECT q.*, c.name as category_name, cl.name as class_name, 
                       COALESCE(
                           (SELECT full_name FROM staff WHERE identity_id = q.uploader_id),
                           (SELECT full_name FROM teachers WHERE identity_id = q.uploader_id)
                       ) as uploader_name 
                       FROM questions q
                       LEFT JOIN categories c ON q.category_id = c.id 
                       LEFT JOIN classes cl ON q.class_id = cl.id
                       WHERE q.id = ?");
$stmt->execute([$id]);
$q = $stmt->fetch();

if (!$q) {
    header('Location: bank-soal.php');
    exit();
}

// Fetch Status Logs
$stmt = $pdo->prepare("SELECT l.*, 
                       COALESCE(
                           (SELECT full_name FROM staff WHERE identity_id = l.actor_id),
                           (SELECT full_name FROM teachers WHERE identity_id = l.actor_id)
                       ) as full_name,
                       COALESCE(
                           (SELECT r.role_name FROM staff s JOIN roles r ON s.role_id = r.id WHERE s.identity_id = l.actor_id),
                           'Guru'
                       ) as role_name
                       FROM question_status_logs l
                       WHERE l.question_id = ?
                       ORDER BY l.created_at DESC");
$stmt->execute([$id]);
$statusLogs = $stmt->fetchAll();

// Fetch Discussions
$stmt = $pdo->prepare("SELECT di.*, 
                       COALESCE(
                           (SELECT full_name FROM staff WHERE identity_id = di.actor_id),
                           (SELECT full_name FROM teachers WHERE identity_id = di.actor_id)
                       ) as full_name,
                       COALESCE(
                           (SELECT r.role_name FROM staff s JOIN roles r ON s.role_id = r.id WHERE s.identity_id = di.actor_id),
                           'Guru'
                       ) as role_name
                       FROM discussions di 
                       WHERE di.question_id = ? 
                       ORDER BY di.created_at ASC");
$stmt->execute([$id]);
$discussions = $stmt->fetchAll();

// Progress Calculation
$progress = 33; // Draft
if ($q['status'] == STATUS_REVIEW) $progress = 66;
if ($q['status'] == STATUS_VERIFIED) $progress = 100;
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
        <!-- Main Content -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex justify-between items-start bg-gradient-to-r from-white to-gray-50">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 leading-tight"><?php echo $q['title']; ?></h2>
                    <div class="flex items-center space-x-4 mt-4">
                        <span class="px-3 py-1 bg-blue-50 text-sky-600 text-[10px] font-bold rounded-full uppercase tracking-widest"><?php echo $q['category_name']; ?></span>
                        <span class="px-3 py-1 bg-gray-50 text-gray-500 border border-gray-200 text-[10px] font-bold rounded-full uppercase tracking-widest">KELAS <?php echo $q['class_name']; ?></span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-widest <?php 
                        echo $q['status'] == STATUS_VERIFIED ? 'bg-green-50 text-green-600' : ($q['status'] == STATUS_REVIEW ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-100 text-gray-400'); 
                    ?>">
                        <?php echo $q['status']; ?>
                    </span>
                </div>
            </div>

            <div class="p-8">
                <!-- Progress Bar -->
                <div class="mb-10">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Progress Validasi</span>
                        <span class="text-xs font-bold text-primary"><?php echo $progress; ?>%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-primary h-full transition-all duration-500" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                </div>

                <?php if ($q['file_path']): ?>
                <div class="flex items-center justify-between p-6 bg-blue-50 border border-blue-100 rounded-3xl mb-10 group hover:border-blue-300 transition-colors">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-white rounded-2xl text-primary shadow-sm group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900"><?php echo $q['original_name']; ?></p>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest"><?php echo $q['file_type']; ?> Document</p>
                        </div>
                    </div>
                    <a href="storage/documents/<?php echo $q['file_path']; ?>" download="<?php echo $q['original_name']; ?>" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-black transition shadow-lg shadow-blue-200">Unduh Soal</a>
                </div>
                <?php endif; ?>

                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pembahasan (Explicit Knowledge)
                    </h3>
                    <div class="bg-gray-50 p-8 rounded-[32px] border border-gray-100 text-gray-700 leading-relaxed prose max-w-none shadow-inner">
                        <?php echo $q['explanation']; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discussion Section -->
        <div class="bg-white rounded-[40px] border border-gray-100 shadow-sm p-10">
            <h3 class="text-xl font-bold text-gray-900 mb-8 flex items-center">
                <svg class="w-6 h-6 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                Diskusi Kolaboratif (Tacit Knowledge)
            </h3>

            <div class="space-y-6 mb-10 max-h-[500px] overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-gray-100">
                <?php if (empty($discussions)): ?>
                <div class="text-center py-20 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <p class="text-gray-400">Belum ada diskusi untuk soal ini.</p>
                </div>
                <?php endif; ?>
                <?php foreach ($discussions as $msg): ?>
                <div class="flex space-x-4">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-primary flex items-center justify-center font-bold text-xs shrink-0"><?php echo strtoupper(substr($msg['full_name'], 0, 1)); ?></div>
                    <div class="flex-1 bg-gray-50 rounded-3xl p-6 relative">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-bold text-gray-900"><?php echo $msg['full_name']; ?> <span class="text-[10px] text-gray-400 font-normal ml-2 tracking-widest uppercase italic"><?php echo $msg['role_name']; ?></span></span>
                            <span class="text-[10px] text-gray-400"><?php echo date('d M, H:i', strtotime($msg['created_at'])); ?></span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($msg['comment'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (hasRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK])): ?>
            <form action="" method="POST" class="mt-8 pt-8 border-t border-gray-100 space-y-4">
                <textarea name="comment" required placeholder="Tuliskan saran, feedback, atau wawasan tambahan terkait soal ini..." class="w-full px-6 py-4 bg-gray-50 border-none rounded-3xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition shadow-inner"></textarea>
                <div class="text-right">
                    <button type="submit" name="post_comment" class="bg-primary text-white px-8 py-3 rounded-2xl font-bold hover:bg-black transition shadow-lg">Posting Komentar</button>
                </div>
            </form>
            <?php else: ?>
            <div class="mt-8 pt-8 border-t border-gray-100">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-center italic">Kepala Sekolah memiliki akses verifikator (Hanya Baca/Verifikasi).</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="space-y-8">
        <!-- Status Update Controls -->
        <?php if (hasRoleId([ROLE_ADMIN_AKADEMIK])): ?>
        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6">Kontrol Validasi (Admin Akademik)</h4>
            <form action="actions/update_status.php" method="POST" class="space-y-4">
                <input type="hidden" name="question_id" value="<?php echo $id; ?>">
                <div>
                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-2">Ubah Status</label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-2xl outline-none text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="<?php echo STATUS_DRAFT; ?>" <?php echo $q['status'] == STATUS_DRAFT ? 'selected' : ''; ?>>Kembalikan ke Draft</option>
                        <option value="<?php echo STATUS_REVIEW; ?>" <?php echo $q['status'] == STATUS_REVIEW ? 'selected' : ''; ?>>Review Ulang</option>
                        <option value="<?php echo STATUS_VERIFIED; ?>" <?php echo $q['status'] == STATUS_VERIFIED ? 'selected' : ''; ?>>Verifikasi Soal (ACC)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-2">Catatan Perubahan</label>
                    <textarea name="notes" placeholder="Berikan alasan atau feedback..." class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-2xl outline-none text-sm min-h-[100px]"></textarea>
                </div>
                <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-bold hover:bg-black transition shadow-xl">Simpan & Perbarui Status</button>
            </form>
        </div>
        <?php elseif (hasRoleId([ROLE_KEPSEK])): ?>
        <div class="bg-blue-50 rounded-3xl border border-blue-100 p-8 shadow-sm">
            <h4 class="text-[10px] font-bold text-primary uppercase tracking-widest mb-4">Monitoring Kepala Sekolah</h4>
            <p class="text-xs text-gray-600 leading-relaxed italic">Status validasi dikelola sepenuhnya oleh Admin Akademik. Anda memiliki akses untuk memantau perkembangan dan kualitas aset pengetahuan ini.</p>
        </div>
        <?php endif; ?>

        <!-- History Log -->
        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6">Log Aktivitas Soal</h4>
            <div class="space-y-6">
                <?php foreach ($statusLogs as $log): ?>
                <div class="relative pl-6 border-l-2 border-gray-50 pb-2">
                    <div class="absolute -left-[5px] top-1 w-2 h-2 rounded-full bg-blue-500"></div>
                    <p class="text-[10px] font-bold text-gray-900"><?php echo $log['new_status']; ?></p>
                    <p class="text-[10px] text-gray-400"><?php echo date('d M Y, H:i', strtotime($log['created_at'])); ?></p>
                    <p class="text-[10px] text-gray-600 mt-1 italic"><?php echo $log['notes']; ?></p>
                    <p class="text-[9px] text-gray-400 mt-1 font-bold">BY: <?php echo $log['full_name']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Metadata Akademik</h4>
            <div class="space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Materi</span>
                    <span class="text-xs font-bold text-gray-900"><?php echo $q['materi']; ?></span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Tingkat Kesulitan</span>
                    <span class="text-xs font-bold text-primary"><?php echo $q['difficulty']; ?></span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Format File</span>
                    <span class="text-xs font-bold text-gray-900 uppercase"><?php echo $q['file_type']; ?></span>
                </div>
            </div>
        </div>

        <div class="bg-primary rounded-3xl p-8 text-white shadow-2xl shadow-blue-200">
            <h4 class="text-[10px] font-bold text-blue-200 uppercase tracking-widest mb-6">Author Knowledge</h4>
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center font-bold text-white"><?php echo strtoupper(substr($q['uploader_name'], 0, 1)); ?></div>
                <div>
                    <p class="text-sm font-bold text-white"><?php echo $q['uploader_name']; ?></p>
                    <p class="text-[10px] text-blue-200 uppercase font-bold tracking-widest">Guru Pengajar</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

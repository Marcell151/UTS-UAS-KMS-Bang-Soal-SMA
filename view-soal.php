<?php
// view-soal.php
$pageTitle = 'Detail Soal & Knowledge Sharing';
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: bank-soal.php');
    exit();
}
$identityId = getIdentityId();

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
$stmt->execute([$id]);
$discussions = $stmt->fetchAll();

// Add Related Questions Fetch here:
$mapel_id = $q['category_id'];
$kelas = $q['class_id'];
$current_question_id = $id;

$stmt_terkait = $pdo->prepare("
    SELECT id, title as isi_soal, tingkat_kognitif, created_at 
    FROM questions 
    WHERE category_id = :mapel_id 
      AND class_id = :kelas 
      AND status = 'Verified' 
      AND id != :current_id 
    ORDER BY RAND() 
    LIMIT 3
");
$stmt_terkait->execute([
    ':mapel_id'   => $mapel_id,
    ':kelas'      => $kelas,
    ':current_id' => $current_question_id
]);
$soal_terkait = $stmt_terkait->fetchAll();

// Progress Calculation
$progress = 33; // Draft
if ($q['status'] == STATUS_REVIEW) $progress = 66;
if ($q['status'] == STATUS_VERIFIED) $progress = 100;
?>
<div class="flex justify-between items-center mb-6">
    <a href="bank-soal.php" class="flex items-center text-sm font-bold text-gray-500 hover:text-primary transition group">
        <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Kembali ke Koleksi Soal
    </a>
    
    <?php if ($q['uploader_id'] == $identityId || hasRoleId([ROLE_ADMIN_AKADEMIK, ROLE_ADMIN_SISTEM])): ?>
    <div class="flex items-center space-x-3">
        <a href="edit-soal.php?id=<?php echo $id; ?>" class="flex items-center text-xs font-bold text-blue-600 hover:text-white hover:bg-blue-600 transition-all bg-blue-50 px-5 py-2.5 rounded-xl border border-blue-100">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            Edit / Revisi
        </a>
        <?php if ($q['is_archived'] == 1): ?>
        <a href="restore_soal.php?id=<?php echo $id; ?>" onclick="return confirm('Apakah Anda yakin ingin memulihkan soal ini ke daftar utama?');" class="flex items-center text-xs font-bold text-green-600 hover:text-white hover:bg-green-600 transition-all bg-green-50 px-5 py-2.5 rounded-xl border border-green-100">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
            Pulihkan Soal
        </a>
        <?php else: ?>
        <a href="archive_soal.php?id=<?php echo $id; ?>" onclick="return confirm('Apakah Anda yakin ingin mengarsipkan soal ini? Soal tidak akan dihapus permanen, tapi akan disembunyikan dari daftar utama.');" class="flex items-center text-xs font-bold text-red-600 hover:text-white hover:bg-red-600 transition-all bg-red-50 px-5 py-2.5 rounded-xl border border-red-100">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Arsipkan Soal
        </a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

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
                        <?php if (!empty($q['tags'])): ?>
                            <?php $tags = explode(',', $q['tags']); foreach ($tags as $tag): ?>
                            <span class="px-3 py-1 bg-purple-50 text-purple-600 text-[10px] font-bold rounded-full uppercase tracking-widest border border-purple-100">#<?php echo trim($tag); ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                    <a href="download.php?id=<?php echo $id; ?>" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-black transition shadow-lg shadow-blue-200">Unduh Soal</a>
                </div>
                <?php endif; ?>

                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pembahasan (Explicit Knowledge)
                    </h3>
                    <div class="bg-gray-50 p-8 rounded-[32px] border border-gray-100 text-gray-700 leading-relaxed editor-content shadow-inner">
                        <?php echo (strpos($q['explanation'], '<') !== false) ? $q['explanation'] : nl2br(htmlspecialchars($q['explanation'])); ?>
                    </div>
                </div>

                <!-- Section Knowledge Retrieval (Rekomendasi Soal) -->
                <div class="mt-10 pt-6 border-t border-slate-200">
                    <h3 class="text-lg font-bold text-[#000080] mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Pengetahuan Serupa (Rekomendasi Soal)
                    </h3>
                    
                    <?php if (count($soal_terkait) > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <?php foreach ($soal_terkait as $soal): ?>
                                <a href="view-soal.php?id=<?= $soal['id'] ?>" class="group block p-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-[#000080]/40 hover:shadow-md transition-all duration-300 relative overflow-hidden">
                                    <div class="absolute top-0 left-0 w-1 h-full bg-[#000080] opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <span class="inline-block px-2.5 py-1 text-[10px] font-bold tracking-wider bg-[#000080]/10 text-[#000080] uppercase rounded-full mb-3">
                                        <?= htmlspecialchars($soal['tingkat_kognitif'] ?? 'N/A') ?>
                                    </span>
                                    <p class="text-sm text-slate-700 line-clamp-3 leading-relaxed mb-3">
                                        <?= htmlspecialchars($soal['isi_soal']) ?>
                                    </p>
                                    <div class="text-[11px] text-slate-400 font-medium">
                                        Dibuat: <?= date('d M Y', strtotime($soal['created_at'])) ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-4 rounded-lg bg-slate-50 border border-slate-100 text-center">
                            <p class="text-sm text-slate-500 italic">Belum ada soal dengan mata pelajaran dan kelas yang sama.</p>
                        </div>
                    <?php endif; ?>
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
                            <span class="text-[10px] text-gray-400">
                                <?php echo date('d M, H:i', strtotime($msg['created_at'])); ?>
                                <?php if (isset($msg['is_edited']) && $msg['is_edited']): ?>
                                    <i class="ml-1 text-gray-400">(diedit)</i>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <!-- View Mode -->
                        <div id="comment-view-<?php echo $msg['id']; ?>">
                            <p class="text-sm text-gray-600 leading-relaxed" id="comment-text-<?php echo $msg['id']; ?>"><?php echo nl2br(htmlspecialchars($msg['comment'])); ?></p>
                            <?php if ($msg['actor_id'] == $identityId): ?>
                                <div class="mt-2 text-right">
                                    <button onclick="toggleEdit(<?php echo $msg['id']; ?>)" class="text-[10px] text-gray-400 hover:text-blue-600 font-bold tracking-widest uppercase transition">(Edit)</button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Edit Mode -->
                        <?php if ($msg['actor_id'] == $identityId): ?>
                        <div id="comment-edit-<?php echo $msg['id']; ?>" class="hidden mt-2">
                            <!-- JS will dynamically adjust height based on scrollHeight -->
                            <textarea id="comment-input-<?php echo $msg['id']; ?>" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none text-sm transition overflow-hidden min-h-[80px]"><?php echo htmlspecialchars($msg['comment']); ?></textarea>
                            <div class="mt-2 flex justify-end space-x-2">
                                <button onclick="toggleEdit(<?php echo $msg['id']; ?>)" class="px-4 py-1.5 text-[10px] text-gray-500 hover:text-gray-700 font-bold tracking-widest uppercase">Batal</button>
                                <button onclick="saveEdit(<?php echo $msg['id']; ?>)" id="btn-save-<?php echo $msg['id']; ?>" class="px-4 py-1.5 text-[10px] bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold tracking-widest uppercase transition">Simpan</button>
                            </div>
                        </div>
                        <?php endif; ?>
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
            <form action="actions/update_status.php" method="POST" class="space-y-4" onsubmit="return validateStatusForm()">
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

        <?php if ($identityId == $q['uploader_id'] && $q['status'] == STATUS_DRAFT): ?>
        <div class="bg-yellow-50 rounded-3xl border border-yellow-200 p-8 shadow-sm">
            <h4 class="text-[10px] font-bold text-yellow-600 uppercase tracking-widest mb-4">Pengajuan Verifikasi</h4>
            <p class="text-xs text-yellow-700 leading-relaxed italic mb-6">Soal Anda saat ini berstatus Draft dan tersembunyi dari publik. Kirimkan ke TU/Admin Akademik untuk direview.</p>
            <form action="actions/update_status.php" method="POST">
                <input type="hidden" name="question_id" value="<?php echo $id; ?>">
                <input type="hidden" name="status" value="<?php echo STATUS_REVIEW; ?>">
                <button type="submit" onclick="return confirm('Kirim soal ini untuk di-review?');" class="w-full bg-yellow-500 text-white py-4 rounded-2xl font-bold hover:bg-yellow-600 transition shadow-xl shadow-yellow-200">Kirim untuk Direview</button>
            </form>
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

<script>
function validateStatusForm() {
    const statusSelect = document.querySelector('select[name="status"]');
    const notesArea = document.querySelector('textarea[name="notes"]');
    if (statusSelect && notesArea) {
        if (statusSelect.value === '<?php echo STATUS_DRAFT; ?>' && notesArea.value.trim() === '') {
            alert('Catatan Perubahan (Pesan) WAJIB diisi jika mengembalikan soal ke Draft agar guru mengetahui letak kesalahannya.');
            notesArea.focus();
            return false;
        }
    }
    return true;
}

function toggleEdit(id) {
    const viewDiv = document.getElementById('comment-view-' + id);
    const editDiv = document.getElementById('comment-edit-' + id);
    const textarea = document.getElementById('comment-input-' + id);
    
    if (editDiv.classList.contains('hidden')) {
        viewDiv.classList.add('hidden');
        editDiv.classList.remove('hidden');
        // Auto-resize to fit content
        textarea.style.height = ''; 
        textarea.style.height = textarea.scrollHeight + 'px';
        textarea.focus();
    } else {
        viewDiv.classList.remove('hidden');
        editDiv.classList.add('hidden');
    }
}

function saveEdit(id) {
    const textarea = document.getElementById('comment-input-' + id);
    const comment = textarea.value.trim();
    const btnSave = document.getElementById('btn-save-' + id);
    
    if (comment === '') {
        alert('Komentar tidak boleh kosong');
        return;
    }
    
    btnSave.disabled = true;
    btnSave.innerText = 'Menyimpan...';
    
    const formData = new FormData();
    formData.append('discussion_id', id);
    formData.append('comment', comment);
    
    fetch('update-discussion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Gagal: ' + data.message);
            btnSave.disabled = false;
            btnSave.innerText = 'Simpan';
        }
    })
    .catch(error => {
        alert('Terjadi kesalahan jaringan.');
        btnSave.disabled = false;
        btnSave.innerText = 'Simpan';
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>

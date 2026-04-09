<?php
// index.php
$pageTitle = 'Dashboard Knowledge Management';
require_once 'includes/header.php';

// Fetch Statistics based on refined schema (documents table)
$stmt = $pdo->query("SELECT COUNT(*) FROM documents");
$totalSoal = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUser = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM forum_topics");
$totalForum = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM categories");
$totalMapel = $stmt->fetchColumn();

// Fetch Recent Activity (Logs)
$stmt = $pdo->query("SELECT l.*, u.full_name 
                     FROM logs l 
                     LEFT JOIN users u ON l.user_id = u.id 
                     ORDER BY l.created_at DESC LIMIT 5");
$recentLogs = $stmt->fetchAll();
?>

<!-- Bento Grid Layout -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12 italic">
    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
        <div class="flex items-center justify-between mb-6">
            <div class="p-4 bg-blue-50 rounded-2xl text-primary group-hover:bg-primary group-hover:text-white transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Explicit</span>
        </div>
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Aset Bank Soal</h3>
        <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $totalSoal; ?></p>
        <p class="text-[10px] text-gray-400 mt-3 font-medium">Dokumen Tersimpan</p>
    </div>

    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
        <div class="flex items-center justify-between mb-6">
            <div class="p-4 bg-orange-50 rounded-2xl text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-orange-400 uppercase tracking-widest">Tacit</span>
        </div>
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Diskusi Forum</h3>
        <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $totalForum; ?></p>
        <p class="text-[10px] text-gray-400 mt-3 font-medium">Kolaborasi Aktif</p>
    </div>

    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
        <div class="flex items-center justify-between mb-6">
            <div class="p-4 bg-purple-50 rounded-2xl text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Tenaga Akademik</h3>
        <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $totalUser; ?></p>
        <p class="text-[10px] text-gray-400 mt-3 font-medium">Pengguna Sistem</p>
    </div>

    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-xl transition-all group">
        <div class="flex items-center justify-between mb-6">
            <div class="p-4 bg-teal-50 rounded-2xl text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
        </div>
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Bidang Studi</h3>
        <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $totalMapel; ?></p>
        <p class="text-[10px] text-gray-400 mt-3 font-medium">Mata Pelajaran</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10 italic">
    <div class="lg:col-span-2 bg-white rounded-[48px] border border-gray-100 shadow-sm p-10">
        <div class="flex items-center justify-between mb-10">
            <h3 class="text-2xl font-bold text-gray-800">Aktivitas Terbaru</h3>
            <a href="reports.php" class="text-xs font-bold text-primary hover:underline uppercase tracking-widest">Lihat Semua</a>
        </div>
        <div class="space-y-8">
            <?php foreach ($recentLogs as $log): ?>
            <div class="flex items-start space-x-5 group">
                <div class="w-1.5 h-1.5 mt-2 rounded-full bg-blue-400 group-hover:scale-150 transition-transform shadow-[0_0_10px_rgba(96,165,250,0.8)]"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-700 leading-snug">
                        <span class="font-bold text-gray-900"><?php echo $log['full_name'] ?? 'Sistem'; ?></span> 
                        <?php echo $log['action']; ?>
                    </p>
                    <p class="text-[10px] text-gray-400 mt-2 font-bold uppercase tracking-tight"><?php echo date('d M Y, H:i', strtotime($log['created_at'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-gray-900 rounded-[56px] p-12 text-white relative overflow-hidden shadow-2xl flex flex-col justify-between">
        <div class="relative z-10">
            <h3 class="text-3xl font-bold mb-8 leading-tight">Misi KMS SMA KK Malang</h3>
            <p class="text-gray-400 text-sm leading-relaxed mb-10">Mentransformasi wawasan personal menjadi aset institusional melalui sistem yang terstruktur dan kolaboratif.</p>
            
            <div class="space-y-5 pt-10 border-t border-gray-800">
                <div class="flex justify-between text-[10px] font-bold uppercase tracking-[0.2em] text-blue-400">
                    <span>Target Validasi</span>
                    <span>Verified: 0%</span>
                </div>
                <div class="w-full bg-gray-800 rounded-full h-1">
                    <div class="bg-blue-500 h-full w-0"></div>
                </div>
            </div>
        </div>
        
        <div class="mt-12 p-6 bg-white bg-opacity-5 rounded-3xl border border-white border-opacity-5">
            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2">Login Sebagai:</p>
            <p class="text-sm font-bold text-white"><?php echo $_SESSION['full_name']; ?></p>
            <p class="text-[10px] text-primary font-bold uppercase tracking-widest mt-1"><?php echo $_SESSION['role_name']; ?></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

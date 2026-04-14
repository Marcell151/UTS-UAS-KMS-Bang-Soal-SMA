<?php
// index.php
$pageTitle = 'Dashboard Manajemen Pengetahuan Akademik';
require_once 'includes/header.php';

// Fetch Statistics based on refined schema (questions table)
$stmt = $pdo->query("SELECT COUNT(*) FROM questions");
$totalSoal = $stmt->fetchColumn();

// Status Statistics
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM questions GROUP BY status");
$statusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$draftCount = $statusCounts[STATUS_DRAFT] ?? 0;
$reviewCount = $statusCounts[STATUS_REVIEW] ?? 0;
$verifiedCount = $statusCounts[STATUS_VERIFIED] ?? 0;

$stmt = $pdo->query("SELECT (SELECT COUNT(*) FROM staff) + (SELECT COUNT(*) FROM teachers)");
$totalUser = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM discussions");
$totalDiskusi = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM categories");
$totalMapel = $stmt->fetchColumn();

// Calculate Verification Percentage
$verifyPercentage = $totalSoal > 0 ? round(($verifiedCount / $totalSoal) * 100) : 0;

// Fetch Recent Activity (Logs)
$stmt = $pdo->query("SELECT l.*, 
                     COALESCE(
                         (SELECT full_name FROM staff WHERE identity_id = l.actor_id),
                         (SELECT full_name FROM teachers WHERE identity_id = l.actor_id)
                     ) as full_name,
                     COALESCE(
                         (SELECT username FROM staff WHERE identity_id = l.actor_id),
                         'GURU'
                     ) as username
                     FROM logs l 
                     ORDER BY l.created_at DESC LIMIT 6");
$recentLogs = $stmt->fetchAll();
?>

<!-- Premium Header Info -->
<div class="mb-12 bg-white p-10 rounded-[48px] border border-gray-100 shadow-xl shadow-gray-50 flex items-center justify-between relative overflow-hidden">
    <div class="relative z-10">
        <h2 class="text-3xl font-bold text-gray-900 leading-tight">Selamat Datang, <?php echo explode(' ', $_SESSION['full_name'])[0]; ?>!</h2>
        <p class="text-gray-500 mt-2 italic">Sistem KMS Bank Soal SMA Kristen Kalam Kudus Malang siap membantu pengelolaan akademik Anda.</p>
    </div>
    <div class="relative z-10 hidden md:block">
        <div class="bg-primary text-white px-8 py-4 rounded-3xl font-bold shadow-2xl shadow-blue-200">
            <?php echo date('d F Y'); ?>
        </div>
    </div>
    <!-- Abstract Decoration -->
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-50 rounded-full opacity-50 scale-110"></div>
</div>

<!-- Bento Grid Layout -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12 italic">
    <!-- Total Soal Card -->
    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 group">
        <div class="flex items-center justify-between mb-6">
            <div class="p-4 bg-blue-50 rounded-2xl text-primary group-hover:bg-primary group-hover:text-white transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Explicit</span>
        </div>
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Dokumen Soal</h3>
        <p class="text-5xl font-bold text-gray-900 mt-2"><?php echo $totalSoal; ?></p>
        <div class="flex items-center mt-3 text-[10px] space-x-3">
             <span class="text-green-500 font-bold"><?php echo $verifiedCount; ?> Verified</span>
             <span class="text-yellow-500 font-bold"><?php echo $reviewCount; ?> Review</span>
        </div>
    </div>

    <!-- Tacit Card -->
    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 group">
        <div class="flex items-center justify-between mb-6">
            <div class="p-4 bg-orange-50 rounded-2xl text-orange-500 group-hover:bg-orange-500 group-hover:text-white transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-orange-400 uppercase tracking-widest">Tacit</span>
        </div>
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Diskusi Pengetahuan</h3>
        <p class="text-5xl font-bold text-gray-900 mt-2"><?php echo $totalDiskusi; ?></p>
        <p class="text-[10px] text-gray-400 mt-3 font-bold uppercase tracking-widest">Total Komentar</p>
    </div>

    <!-- Users Card -->
    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 group">
        <div class="flex items-center justify-between mb-6">
            <div class="p-4 bg-purple-50 rounded-2xl text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Tenaga Pendidik</h3>
        <p class="text-5xl font-bold text-gray-900 mt-2"><?php echo $totalUser; ?></p>
        <p class="text-[10px] text-gray-400 mt-3 font-bold uppercase tracking-widest">Pengguna Terdaftar</p>
    </div>

    <!-- Mapel Card -->
    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 group">
        <div class="flex items-center justify-between mb-6">
            <div class="p-4 bg-teal-50 rounded-2xl text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
        </div>
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Mata Pelajaran</h3>
        <p class="text-5xl font-bold text-gray-900 mt-2"><?php echo $totalMapel; ?></p>
        <p class="text-[10px] text-gray-400 mt-3 font-bold uppercase tracking-widest">Kategori Aktif</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10 italic">
    <!-- Recent Activity -->
    <div class="lg:col-span-2 bg-white rounded-[56px] border border-gray-100 shadow-xl p-12">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Aktivitas Terbaru</h3>
                <p class="text-xs text-gray-400 mt-1 uppercase font-bold tracking-widest">Audit Log & Progress System</p>
            </div>
            <a href="reports.php" class="p-3 bg-gray-50 text-gray-900 rounded-2xl hover:bg-primary hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
        </div>
        <div class="space-y-10 relative">
            <!-- Timeline Line -->
            <div class="absolute left-[7px] top-2 bottom-0 w-[1px] bg-gray-100"></div>

<?php foreach ($recentLogs as $log): ?>
            <div class="flex items-start space-x-6 group relative z-10 text-pretty">
                <div class="w-4 h-4 mt-1.5 rounded-full border-2 border-white bg-blue-500 shadow-blue-200 shadow-lg group-hover:scale-125 transition-transform duration-300 shrink-0"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-700 leading-snug">
                        <span class="font-bold text-gray-900"><?php echo $log['full_name'] ?? $log['username'] ?? 'Sistem'; ?></span> 
                        <span class="text-gray-500"><?php echo $log['action']; ?></span>
                    </p>
                    <p class="text-[9px] text-gray-300 mt-2 font-bold uppercase tracking-widest"><?php echo date('d M Y, H:i', strtotime($log['created_at'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Guru Ranking Section -->
        <div class="mt-20 pt-12 border-t border-gray-50">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-10 italic">Top Kontributor (Verified Assets)</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $stmt = $pdo->query("SELECT t.full_name, t.nip, COUNT(q.id) as total 
                                     FROM teachers t 
                                     JOIN questions q ON t.identity_id = q.uploader_id 
                                     WHERE q.status = 'Verified' 
                                     GROUP BY t.id 
                                     ORDER BY total DESC LIMIT 3");
                $rankers = $stmt->fetchAll();
                foreach ($rankers as $index => $rank):
                ?>
                <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100 flex items-center space-x-4">
                    <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold shadow-lg shadow-blue-100 italic">#<?php echo $index+1; ?></div>
                    <div>
                        <p class="text-sm font-bold text-gray-900"><?php echo explode(',', $rank['full_name'])[0]; ?></p>
                        <p class="text-[9px] text-primary font-bold uppercase tracking-widest italic"><?php echo $rank['total']; ?> Soal Valid</p>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if (empty($rankers)): ?>
                    <div class="col-span-3 text-center text-[11px] text-gray-400 italic">Belum ada kontribusi tervirifikasi.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Dark Side Card -->
    <div class="space-y-8">
        <?php if (hasRoleId([ROLE_ADMIN_AKADEMIK, ROLE_KEPSEK]) && $reviewCount > 0): ?>
        <div class="bg-red-600 rounded-[40px] p-8 text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white opacity-10 rounded-full group-hover:scale-110 transition-transform"></div>
            <h4 class="text-[10px] font-bold text-red-200 uppercase tracking-[0.2em] mb-4 relative z-10">Antrian Review</h4>
            <p class="text-2xl font-bold mb-2 relative z-10"><?php echo $reviewCount; ?> Soal Menunggu</p>
            <p class="text-[10px] text-red-100 italic mb-6 relative z-10">Segera lakukan validasi instruksional untuk menjaga standar KMS.</p>
            <a href="bank-soal.php?status=Review" class="inline-flex items-center space-x-2 bg-white text-red-600 px-6 py-3 rounded-2xl text-xs font-bold hover:shadow-xl transition-all relative z-10">
                <span>Periksa Sekarang</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
        <?php endif; ?>

        <div class="bg-gray-900 rounded-[64px] p-12 text-white relative overflow-hidden shadow-2xl flex flex-col justify-between group min-h-[400px]">
            <!-- Decoration -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600 rounded-full blur-[120px] opacity-20 group-hover:opacity-40 transition-opacity"></div>
            
            <div class="relative z-10">
                <h3 class="text-4xl font-bold mb-6 leading-tight">Misi KMS SMA KK Malang</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-12 italic">"Mentransformasi wawasan personal menjadi aset institusional melalui sistem yang terstruktur dan kolaboratif."</p>
                
                <div class="space-y-6 pt-12 border-t border-gray-800">
                    <div class="flex justify-between text-[11px] font-bold uppercase tracking-[0.2em] text-blue-400">
                        <span>Target Akreditasi Soal</span>
                        <span>Verified: <?php echo $verifyPercentage; ?>%</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-2 overflow-hidden">
                        <div class="bg-blue-500 h-full transition-all duration-1000 shadow-[0_0_15px_#3b82f6]" style="width: <?php echo $verifyPercentage; ?>%"></div>
                    </div>
                </div>
            </div>
            
            <div class="mt-auto p-8 bg-white bg-opacity-[0.03] rounded-[32px] border border-white border-opacity-[0.05] backdrop-blur-sm relative z-10 group-hover:bg-opacity-[0.06] transition-all">
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2">Akses Terotorisasi:</p>
                <p class="text-lg font-bold text-white"><?php echo $_SESSION['full_name']; ?></p>
                <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest mt-1"><?php echo $_SESSION['role_name']; ?></p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

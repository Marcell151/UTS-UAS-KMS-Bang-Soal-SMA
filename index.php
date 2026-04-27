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
<div class="mb-12 bg-white p-12 rounded-[48px] border border-gray-100 shadow-2xl shadow-blue-50 flex items-center justify-between relative overflow-hidden">
    <div class="relative z-10">
        <span class="px-4 py-1.5 bg-blue-50 text-[#003366] text-[10px] font-black rounded-full uppercase tracking-widest mb-4 inline-block">Sistem Manajemen Pengetahuan</span>
        <h2 class="text-4xl font-black text-gray-900 leading-tight">Selamat Datang,<br><?php echo $_SESSION['full_name']; ?></h2>
        <p class="text-gray-500 mt-4 max-w-md">Mentransformasi wawasan personal menjadi aset institusional SMA Kristen Kalam Kudus Malang.</p>
    </div>
    <div class="relative z-10 hidden lg:block text-right">
        <div class="bg-[#003366] text-white px-10 py-6 rounded-[32px] font-black shadow-2xl shadow-blue-200 inline-block">
            <p class="text-[10px] opacity-70 uppercase tracking-widest mb-1">Tanggal Hari Ini</p>
            <p class="text-xl"><?php echo date('d F Y'); ?></p>
        </div>
    </div>
    <!-- Abstract Decoration -->
    <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-50 rounded-full opacity-50 blur-3xl"></div>
</div>

<?php if (hasRoleId([ROLE_ADMIN_AKADEMIK]) && $reviewCount > 0): ?>
<!-- SNA Pillar: Bottleneck Mitigation (Antrean Review) -->
<div class="mb-12 bg-red-600 rounded-[40px] p-10 text-white shadow-2xl shadow-red-200 flex flex-col md:flex-row items-center justify-between relative overflow-hidden group">
    <div class="absolute top-0 right-0 w-64 h-full bg-white opacity-5 -skew-x-12 translate-x-10"></div>
    <div class="relative z-10 mb-6 md:mb-0">
        <h4 class="text-[11px] font-black text-red-200 uppercase tracking-[0.3em] mb-3">Antrean Review (SNA Pillar)</h4>
        <p class="text-3xl font-black mb-2">Ada <?php echo $reviewCount; ?> soal menunggu validasi Anda</p>
        <p class="text-red-100 opacity-80 text-sm">Segera lakukan verifikasi untuk menjaga kelancaran distribusi pengetahuan.</p>
    </div>
    <a href="bank-soal.php?status=Review" class="relative z-10 px-10 py-4 bg-white text-red-600 rounded-2xl font-black hover:scale-105 transition-transform shadow-xl flex items-center">
        Buka Antrean
        <svg class="w-5 h-5 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
    </a>
</div>
<?php endif; ?>

<!-- Bento Grid Layout -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
    <!-- Total Soal Card -->
    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 group flex flex-col justify-between h-full">
        <div>
            <div class="w-14 h-14 bg-blue-50 rounded-2xl text-[#003366] flex items-center justify-center mb-8 group-hover:bg-[#003366] group-hover:text-white transition-all duration-500 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-3">Explicit Assets</h3>
            <p class="text-5xl font-black text-gray-900"><?php echo $totalSoal; ?></p>
        </div>
        <div class="flex items-center mt-8 text-[10px] font-black uppercase tracking-tighter space-x-3">
             <span class="text-green-600 bg-green-50 px-3 py-1 rounded-lg"><?php echo $verifiedCount; ?> Verified</span>
             <span class="text-yellow-600 bg-yellow-50 px-3 py-1 rounded-lg"><?php echo $reviewCount; ?> Pending</span>
        </div>
    </div>

    <!-- Tacit Card -->
    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 group flex flex-col justify-between h-full">
        <div>
            <div class="w-14 h-14 bg-red-50 rounded-2xl text-red-600 flex items-center justify-center mb-8 group-hover:bg-red-600 group-hover:text-white transition-all duration-500 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            </div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-3">Tacit Knowledge</h3>
            <p class="text-5xl font-black text-gray-900"><?php echo $totalDiskusi; ?></p>
        </div>
        <p class="text-[10px] text-gray-400 mt-8 font-black uppercase tracking-widest">Total Kontribusi Diskusi</p>
    </div>

    <!-- Users Card -->
    <div class="bg-white p-8 rounded-[40px] border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 group flex flex-col justify-between h-full">
        <div>
            <div class="w-14 h-14 bg-gray-50 rounded-2xl text-gray-900 flex items-center justify-center mb-8 group-hover:bg-gray-900 group-hover:text-white transition-all duration-500 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-3">Community</h3>
            <p class="text-5xl font-black text-gray-900"><?php echo $totalUser; ?></p>
        </div>
        <p class="text-[10px] text-gray-400 mt-8 font-black uppercase tracking-widest">Aktor Terotorisasi</p>
    </div>

    <!-- Mapel Card -->
    <div class="bg-[#003366] p-8 rounded-[40px] shadow-xl flex flex-col justify-between h-full group overflow-hidden relative">
        <div class="absolute -right-4 -top-4 w-32 h-32 bg-white opacity-5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
        <div class="relative z-10">
            <div class="w-14 h-14 bg-white bg-opacity-10 rounded-2xl text-white flex items-center justify-center mb-8">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            </div>
            <h3 class="text-[10px] font-black text-blue-200 uppercase tracking-widest leading-none mb-3">Mata Pelajaran</h3>
            <p class="text-5xl font-black text-white"><?php echo $totalMapel; ?></p>
        </div>
        <p class="text-[10px] text-blue-300 mt-8 font-black uppercase tracking-widest relative z-10">Kategori Aset Aktif</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    <!-- Recent Activity -->
    <div class="lg:col-span-2 bg-white rounded-[56px] border border-gray-100 shadow-2xl p-12">
        <div class="flex items-center justify-between mb-12">
            <div>
                <h3 class="text-2xl font-black text-gray-900">Audit Log KMS</h3>
                <p class="text-[10px] text-gray-400 mt-1 uppercase font-black tracking-[0.2em]">Monitoring Jejak Pengetahuan</p>
            </div>
        </div>
        <div class="space-y-10">
<?php foreach ($recentLogs as $log): ?>
            <div class="flex items-start space-x-6 group">
                <div class="w-3 h-3 mt-1.5 rounded-full border-2 border-white bg-red-600 shadow-lg shadow-red-200"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-700 leading-snug">
                        <span class="font-black text-gray-900"><?php echo $log['full_name'] ?? $log['username']; ?></span> 
                        <span class="text-gray-500"><?php echo $log['action']; ?></span>
                    </p>
                    <p class="text-[9px] text-gray-300 mt-2 font-black uppercase tracking-widest"><?php echo date('d M Y, H:i', strtotime($log['created_at'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Guru Ranking Section -->
        <div class="mt-20 pt-12 border-t border-gray-50">
            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-10">Top Kontributor (Verified Assets)</h4>
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
                <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 flex items-center space-x-4 hover:scale-105 transition-transform duration-300">
                    <div class="w-10 h-10 bg-[#003366] text-white rounded-full flex items-center justify-center font-black shadow-lg shadow-blue-100 italic text-xs">#<?php echo $index+1; ?></div>
                    <div>
                        <p class="text-xs font-black text-gray-900"><?php echo explode(',', $rank['full_name'])[0]; ?></p>
                        <p class="text-[9px] text-red-600 font-black uppercase tracking-widest"><?php echo $rank['total']; ?> Soal Valid</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Dark Side Card (Misi) -->
    <div class="bg-gray-900 rounded-[64px] p-12 text-white relative overflow-hidden shadow-2xl flex flex-col justify-between group min-h-[500px]">
        <div class="absolute top-0 right-0 w-80 h-80 bg-[#003366] rounded-full blur-[120px] opacity-30"></div>
        
        <div class="relative z-10">
            <img src="assets/img/logo_kk.png" alt="Logo" class="w-20 h-auto mb-10 opacity-80 filter grayscale brightness-200">
            <h3 class="text-4xl font-black mb-6 leading-tight">Misi KMS<br>SMA KK Malang</h3>
            <p class="text-gray-400 text-sm leading-relaxed mb-12 italic">"Mentransformasi wawasan personal menjadi aset institusional melalui sistem yang terstruktur dan kolaboratif."</p>
            
            <div class="space-y-6 pt-12 border-t border-gray-800">
                <div class="flex justify-between text-[11px] font-black uppercase tracking-[0.2em] text-blue-400">
                    <span>Akurasi Aset Terverifikasi</span>
                    <span><?php echo $verifyPercentage; ?>%</span>
                </div>
                <div class="w-full bg-gray-800 rounded-full h-2.5 overflow-hidden p-0.5">
                    <div class="bg-blue-500 h-full rounded-full shadow-[0_0_20px_#3b82f6] transition-all duration-1000" style="width: <?php echo $verifyPercentage; ?>%"></div>
                </div>
            </div>
        </div>
        
        <div class="relative z-10 mt-20 p-8 bg-white bg-opacity-[0.03] rounded-[32px] border border-white border-opacity-[0.05] backdrop-blur-sm">
            <p class="text-[9px] text-gray-500 font-black uppercase tracking-[0.3em] mb-2">Akses Terotorisasi:</p>
            <p class="text-lg font-black text-white"><?php echo $_SESSION['full_name']; ?></p>
            <p class="text-[10px] text-red-500 font-black uppercase tracking-widest mt-1"><?php echo $_SESSION['role_name']; ?></p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

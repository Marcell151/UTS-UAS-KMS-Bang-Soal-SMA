<?php
// reports.php
$pageTitle = 'Laporan Knowledge Management';
require_once 'includes/header.php';

// Check Role: Only Admin Akademik and Kepala Sekolah can access reports
checkRoleId([ROLE_ADMIN_AKADEMIK, ROLE_KEPSEK]);

// Stats per Category
$stmt = $pdo->query("SELECT c.name, COUNT(q.id) as total 
                     FROM categories c 
                     LEFT JOIN questions q ON c.id = q.category_id 
                     GROUP BY c.name");
$statsCategory = $stmt->fetchAll();

// Stats per Status
$stmt = $pdo->query("SELECT status, COUNT(*) as total FROM questions GROUP BY status");
$statsStatus = $stmt->fetchAll();

// Most Active Contributors (Explicit Knowledge)
$stmt = $pdo->query("SELECT 
                        COALESCE(
                            (SELECT full_name FROM staff WHERE identity_id = q.uploader_id),
                            (SELECT full_name FROM teachers WHERE identity_id = q.uploader_id)
                        ) as full_name,
                        COUNT(q.id) as total 
                     FROM questions q 
                     WHERE q.uploader_id IS NOT NULL
                     GROUP BY full_name 
                     ORDER BY total DESC LIMIT 5");
$topContributors = $stmt->fetchAll();
?>

<div class="mb-10 italic">
    <h3 class="text-3xl font-bold text-gray-900 leading-none">Analitik Aset Pengetahuan</h3>
    <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold mt-3 italic">Laporan Konsolidasi Bank Soal SMA Kristen Kalam Kudus Malang</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10 italic">
    <div class="lg:col-span-2 bg-white rounded-[48px] border border-gray-100 shadow-xl overflow-hidden p-10">
        <h3 class="text-xl font-bold text-gray-900 mb-10 flex items-center">
            <svg class="w-6 h-6 mr-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Distribusi Pengetahuan per Mata Pelajaran
        </h3>
        <div class="space-y-8">
            <?php 
                $columnTotals = array_column($statsCategory, 'total');
                $maxTotal = (!empty($columnTotals) && max($columnTotals) > 0) ? max($columnTotals) : 1;
                foreach ($statsCategory as $stat): 
                    $pct = ($stat['total'] / $maxTotal) * 100;
            ?>
            <div class="group">
                <div class="flex justify-between text-[11px] font-bold uppercase tracking-wider mb-3">
                    <span class="text-gray-500 group-hover:text-primary transition-colors"><?php echo $stat['name']; ?></span>
                    <span class="text-gray-900"><?php echo $stat['total']; ?> Aset Soal</span>
                </div>
                <div class="w-full bg-gray-50 rounded-full h-2 overflow-hidden shadow-inner">
                    <div class="bg-primary h-full rounded-full transition-all duration-1000 group-hover:bg-blue-600" style="width: <?php echo $pct; ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="space-y-8">
        <div class="bg-white rounded-[40px] border border-gray-100 shadow-xl p-10">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-8 text-center">Integritas & Validasi Status</h3>
            <div class="space-y-4">
                <?php 
                $statusColors = [
                    STATUS_DRAFT => ['bg' => 'bg-gray-50', 'text' => 'text-gray-400', 'accent' => 'bg-gray-200'],
                    STATUS_REVIEW => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-600', 'accent' => 'bg-yellow-400'],
                    STATUS_VERIFIED => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'accent' => 'bg-emerald-400']
                ];
                foreach ($statsStatus as $stat): 
                    $style = $statusColors[$stat['status']] ?? ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'accent' => 'bg-blue-400'];
                ?>
                <div class="<?php echo $style['bg']; ?> p-6 rounded-[28px] border border-white relative overflow-hidden group hover:scale-105 transition-transform">
                    <div class="absolute right-0 top-0 bottom-0 w-1 <?php echo $style['accent']; ?>"></div>
                    <p class="text-[10px] font-bold <?php echo $style['text']; ?> uppercase tracking-widest mb-2"><?php echo $stat['status']; ?></p>
                    <p class="text-4xl font-bold text-gray-900"><?php echo $stat['total']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="bg-gray-900 rounded-[48px] p-10 text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500 rounded-full blur-[60px] opacity-20 group-hover:opacity-40 transition-opacity"></div>
            <h4 class="text-[10px] font-bold text-blue-400 uppercase tracking-widest mb-8 relative z-10">Pahlawan Pengetahuan (Top Kontributor)</h4>
            <div class="space-y-6 relative z-10">
                <?php foreach ($topContributors as $user): ?>
                <div class="flex items-center justify-between group/item">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 rounded-[14px] bg-white bg-opacity-10 text-xs flex items-center justify-center font-bold border border-white border-opacity-10 group-hover/item:bg-primary transition-colors">
                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                        </div>
                        <span class="text-sm font-medium text-gray-300 group-hover/item:text-white"><?php echo $user['full_name']; ?></span>
                    </div>
                    <span class="text-xs font-bold text-blue-400"><?php echo $user['total']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-12 bg-white rounded-[56px] border border-gray-100 shadow-xl p-12 overflow-hidden italic">
    <div class="flex justify-between items-center mb-10">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 leading-none">Audit Trail Sistem</h3>
            <p class="text-xs text-gray-400 font-bold uppercase mt-2 tracking-widest">Aktivitas Terakhir Pengguna</p>
        </div>
        <div class="px-5 py-2 bg-gray-50 rounded-full text-[10px] font-bold text-gray-400 uppercase tracking-widest border border-gray-100">Live Updates</div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                    <th class="pb-6 px-6">Timestamp</th>
                    <th class="pb-6 px-6">User / Operator</th>
                    <th class="pb-6 px-6">Rincian Aktivitas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                <?php 
                $logs = $pdo->query("SELECT l.*, 
                                     COALESCE(
                                         (SELECT full_name FROM staff WHERE identity_id = l.actor_id),
                                         (SELECT full_name FROM teachers WHERE identity_id = l.actor_id)
                                     ) as full_name,
                                     COALESCE(
                                         (SELECT username FROM staff WHERE identity_id = l.actor_id),
                                         'GURU'
                                     ) as username
                                     FROM logs l 
                                     ORDER BY l.created_at DESC LIMIT 15")->fetchAll();
                foreach ($logs as $log): 
                ?>
                <tr class="hover:bg-gray-50 transition group">
                    <td class="py-6 px-6 text-gray-400 text-xs font-medium"><?php echo date('d M, H:i:s', strtotime($log['created_at'])); ?></td>
                    <td class="py-6 px-6">
                        <div class="flex items-center space-x-3">
                            <span class="font-bold text-gray-900 group-hover:text-primary transition-colors"><?php echo $log['full_name'] ?? $log['username'] ?? 'System Process'; ?></span>
                        </div>
                    </td>
                    <td class="py-6 px-6 text-gray-600 font-medium italic"><?php echo $log['action']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

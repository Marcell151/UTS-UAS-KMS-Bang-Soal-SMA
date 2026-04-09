<?php
// reports.php
$pageTitle = 'Laporan Knowledge Management';
require_once 'includes/header.php';

// Check Role
checkRole(['Admin Akademik', 'Kepala Sekolah']);

// Stats per Category
$stmt = $pdo->query("SELECT c.name, COUNT(d.id) as total 
                     FROM categories c 
                     LEFT JOIN documents d ON c.id = d.category_id 
                     GROUP BY c.name");
$statsCategory = $stmt->fetchAll();

// Stats per Status
$stmt = $pdo->query("SELECT status, COUNT(*) as total FROM documents GROUP BY status");
$statsStatus = $stmt->fetchAll();

// Most Active Contributors (Explicit Knowledge)
$stmt = $pdo->query("SELECT u.full_name, COUNT(d.id) as total 
                     FROM users u 
                     JOIN documents d ON u.id = d.uploader_id 
                     GROUP BY u.full_name 
                     ORDER BY total DESC LIMIT 5");
$topContributors = $stmt->fetchAll();
?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Distribusi Pengetahuan per Mapel</h3>
        <div class="space-y-4">
            <?php 
                $columnTotals = array_column($statsCategory, 'total');
                $maxTotal = (!empty($columnTotals) && max($columnTotals) > 0) ? max($columnTotals) : 1;
                foreach ($statsCategory as $stat): 
                    $pct = ($stat['total'] / $maxTotal) * 100;
            ?>
            <div>
                <div class="flex justify-between text-xs font-bold uppercase tracking-wider mb-2">
                    <span class="text-gray-500"><?php echo $stat['name']; ?></span>
                    <span class="text-gray-900"><?php echo $stat['total']; ?> Dokumen</span>
                </div>
                <div class="w-full bg-gray-50 rounded-full h-3">
                    <div class="bg-primary h-3 rounded-full transition-all duration-500" style="width: <?php echo $pct; ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Vaiditas Aset Pengetahuan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <?php 
            $statusColors = ['Draft' => 'gray', 'Review' => 'yellow', 'Verified' => 'teal'];
            foreach ($statsStatus as $stat): 
                $color = $statusColors[$stat['status']] ?? 'blue';
            ?>
            <div class="p-6 bg-<?php echo $color; ?>-50 rounded-2xl text-center">
                <p class="text-[10px] font-bold text-<?php echo $color; ?>-600 uppercase tracking-widest mb-2"><?php echo $stat['status']; ?></p>
                <p class="text-2xl font-bold text-<?php echo $color; ?>-700"><?php echo $stat['total']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-8 p-6 bg-gray-900 rounded-3xl text-white">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Kontributor Teraktif</h4>
            <div class="space-y-4">
                <?php foreach ($topContributors as $user): ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-6 h-6 rounded-full bg-blue-500 text-[10px] flex items-center justify-center font-bold">
                            <?php echo substr($user['full_name'], 0, 1); ?>
                        </div>
                        <span class="text-sm"><?php echo $user['full_name']; ?></span>
                    </div>
                    <span class="text-xs font-bold text-blue-400"><?php echo $user['total']; ?> Dokumen</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-8 bg-white rounded-3xl border border-gray-100 shadow-sm p-8 overflow-hidden">
    <h3 class="text-lg font-bold text-gray-800 mb-6">Audit Trail Sistem</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                <tr>
                    <th class="pb-4 px-4">Timestamp</th>
                    <th class="pb-4 px-4">User</th>
                    <th class="pb-4 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm italic">
                <?php 
                $logs = $pdo->query("SELECT l.*, u.username FROM logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 10")->fetchAll();
                foreach ($logs as $log): 
                ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-4 px-4 text-gray-400"><?php echo date('d M H:i', strtotime($log['created_at'])); ?></td>
                    <td class="py-4 px-4 font-bold text-gray-900"><?php echo $log['username'] ?? 'System'; ?></td>
                    <td class="py-4 px-4 text-gray-600"><?php echo $log['action']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

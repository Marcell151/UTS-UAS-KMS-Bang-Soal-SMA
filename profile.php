<?php
// profile.php
$pageTitle = 'Profil Pengguna';
require_once 'includes/header.php';

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Fetch activity counts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE uploader_id = ?");
$stmt->execute([$userId]);
$qc = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_topics WHERE user_id = ?");
$stmt->execute([$userId]);
$ftc = $stmt->fetchColumn();
?>

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
        <div class="h-48 bg-primary relative">
            <div class="absolute -bottom-12 left-12 w-32 h-32 rounded-3xl bg-white p-2 shadow-lg">
                <div class="w-full h-full bg-blue-100 rounded-2xl flex items-center justify-center text-primary text-4xl font-bold">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
            </div>
        </div>
        
        <div class="pt-16 pb-12 px-12">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900"><?php echo $user['full_name']; ?></h2>
                    <p class="text-gray-500 font-medium mt-1">@<?php echo $user['username']; ?> • <span class="text-primary"><?php echo $user['role_name']; ?></span></p>
                </div>
                <button class="px-6 py-2 border border-gray-200 rounded-xl font-bold text-gray-600 hover:bg-gray-50 transition">Edit Profil</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-8 border-t border-gray-100">
                <div class="p-6 bg-gray-50 rounded-2xl text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Soal Dibuat</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $qc; ?></p>
                </div>
                <div class="p-6 bg-gray-50 rounded-2xl text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Topik Diskusi</p>
                    <p class="text-3xl font-bold text-gray-900"><?php echo $ftc; ?></p>
                </div>
                <div class="p-6 bg-gray-50 rounded-2xl text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Skor Kontribusi</p>
                    <p class="text-3xl font-bold text-primary"><?php echo ($qc * 10) + ($ftc * 5); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-8 bg-white rounded-3xl border border-gray-100 p-8 shadow-sm italic">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Akun</h3>
        <div class="space-y-4 text-sm">
            <div class="flex">
                <span class="w-32 text-gray-400">ID Pengguna</span>
                <span class="font-bold text-gray-900">#<?php echo $user['id']; ?></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-400">Terdaftar Pada</span>
                <span class="font-bold text-gray-900"><?php echo date('d F Y', strtotime($user['created_at'])); ?></span>
            </div>
            <div class="flex">
                <span class="w-32 text-gray-400">Status</span>
                <span class="font-bold text-green-500">Aktif</span>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

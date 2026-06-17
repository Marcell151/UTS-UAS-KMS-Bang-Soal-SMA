<?php
// profile.php
$pageTitle = 'Profil Pengguna';
require_once 'includes/header.php';

$identityId = getIdentityId();
$actorType = $_SESSION['actor_type'];

if ($actorType === ACTOR_STAFF) {
    $stmt = $pdo->prepare("SELECT s.*, r.role_name FROM staff s JOIN roles r ON s.role_id = r.id WHERE s.identity_id = ?");
} else {
    $stmt = $pdo->prepare("SELECT *, 'Guru' as role_name FROM teachers WHERE identity_id = ?");
}
$stmt->execute([$identityId]);
$user = $stmt->fetch();

// Fetch activity counts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE uploader_id = ?");
$stmt->execute([$identityId]);
$qc = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM forum_topics WHERE actor_id = ?");
$stmt->execute([$identityId]);
$ftc = $stmt->fetchColumn();

// Handle Credential Change
$msg = '';
$err = '';
if (isset($_POST['change_credential'])) {
    if ($actorType === ACTOR_STAFF) {
        $old_pass = $_POST['old_password'];
        $new_pass = $_POST['new_password'];
        $conf_pass = $_POST['confirm_password'];
        
        if ($old_pass !== $user['password']) {
            $err = "Password lama tidak sesuai.";
        } elseif (strlen($new_pass) < 6) {
            $err = "Password baru minimal 6 karakter.";
        } elseif ($new_pass !== $conf_pass) {
            $err = "Konfirmasi password tidak cocok.";
        } else {
            $update = $pdo->prepare("UPDATE staff SET password = ? WHERE identity_id = ?");
            $update->execute([$new_pass, $identityId]);
            $msg = "Password berhasil diperbarui.";
            $user['password'] = $new_pass; // Update current user array
        }
    } else {
        $old_pin = $_POST['old_pin'];
        $new_pin = $_POST['new_pin'];
        $conf_pin = $_POST['confirm_pin'];
        
        if ($old_pin !== $user['pin']) {
            $err = "PIN lama tidak sesuai.";
        } elseif (strlen($new_pin) !== 6 || !is_numeric($new_pin)) {
            $err = "PIN baru harus berupa 6 digit angka.";
        } elseif ($new_pin !== $conf_pin) {
            $err = "Konfirmasi PIN tidak cocok.";
        } else {
            $update = $pdo->prepare("UPDATE teachers SET pin = ? WHERE identity_id = ?");
            $update->execute([$new_pin, $identityId]);
            $msg = "PIN Login berhasil diperbarui.";
            $user['pin'] = $new_pin; // Update current user array
        }
    }
}
?>

<div class="max-w-4xl mx-auto space-y-8">
    <?php if ($msg): ?>
        <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-3xl text-emerald-700 text-sm font-bold flex items-center italic shadow-sm">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>
    <?php if ($err): ?>
        <div class="p-6 bg-red-50 border border-red-100 rounded-3xl text-red-700 text-sm font-bold flex items-center italic shadow-sm">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <?php echo $err; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
        <div class="h-48 bg-primary relative">
            <div class="absolute -bottom-12 left-12 w-32 h-32 rounded-3xl bg-white p-2 shadow-lg">
                <div class="w-full h-full bg-blue-100 rounded-2xl flex items-center justify-center text-primary text-4xl font-bold">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
            </div>
        </div>
        
        <div class="pt-16 pb-12 px-12">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900"><?php echo $user['full_name']; ?></h2>
                    <p class="text-gray-500 font-medium mt-1">
                        <?php if (isset($user['username'])): ?>@<?php echo $user['username']; ?> • <?php endif; ?>
                        <span class="text-primary"><?php echo $user['role_name']; ?></span>
                    </p>
                </div>
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
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm italic">
            <h3 class="text-lg font-bold text-gray-800 mb-6">Informasi Akun</h3>
            <div class="space-y-4 text-sm">
                <div class="flex">
                    <span class="w-32 text-gray-400 font-medium">ID Pengguna</span>
                    <span class="font-bold text-gray-900">#<?php echo $user['id']; ?></span>
                </div>
                <?php if ($actorType !== ACTOR_STAFF): ?>
                <div class="flex">
                    <span class="w-32 text-gray-400 font-medium">NIP Guru</span>
                    <span class="font-bold text-primary"><?php echo $user['nip']; ?></span>
                </div>
                <?php endif; ?>
                <div class="flex">
                    <span class="w-32 text-gray-400 font-medium">Terdaftar Pada</span>
                    <span class="font-bold text-gray-900"><?php echo date('d F Y', strtotime($user['created_at'])); ?></span>
                </div>
                <div class="flex">
                    <span class="w-32 text-gray-400 font-medium">Status</span>
                    <span class="font-bold text-green-500">Aktif</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
            <h3 class="text-lg font-bold text-gray-800 mb-2 italic">Pengaturan Keamanan</h3>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-6">Ubah Kredensial Akses Anda</p>
            
            <form action="" method="POST" class="space-y-5">
                <?php if ($actorType === ACTOR_STAFF): ?>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">Password Saat Ini</label>
                        <input type="password" name="old_password" required class="w-full px-5 py-3 bg-gray-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">Password Baru <span class="text-gray-400 font-normal">(Min. 6 Karakter)</span></label>
                        <input type="password" name="new_password" required minlength="6" class="w-full px-5 py-3 bg-gray-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" required minlength="6" class="w-full px-5 py-3 bg-gray-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
                    </div>
                <?php else: ?>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">PIN Login Saat Ini</label>
                        <input type="password" name="old_pin" required maxlength="6" pattern="\d{6}" class="w-full px-5 py-3 bg-gray-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 shadow-inner font-mono tracking-widest">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">PIN Baru <span class="text-gray-400 font-normal">(Wajib 6 Angka)</span></label>
                        <input type="password" name="new_pin" required maxlength="6" pattern="\d{6}" class="w-full px-5 py-3 bg-gray-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 shadow-inner font-mono tracking-widest">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-2">Konfirmasi PIN Baru</label>
                        <input type="password" name="confirm_pin" required maxlength="6" pattern="\d{6}" class="w-full px-5 py-3 bg-gray-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500 shadow-inner font-mono tracking-widest">
                    </div>
                <?php endif; ?>
                
                <div class="pt-2 text-right">
                    <button type="submit" name="change_credential" class="px-6 py-3 bg-gray-900 text-white rounded-xl font-bold text-sm hover:bg-black transition shadow-lg">Perbarui Kredensial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
// users.php
$pageTitle = 'Manajemen Akun Staf & Pimpinan';
require_once 'includes/header.php';

// Check Role: Only Admin Sistem can manage staff accounts
checkRoleId([ROLE_ADMIN_SISTEM]);

$message = '';
$error = '';

// Handle Add/Edit Staff
if (isset($_POST['save_staff'])) {
    $id = $_POST['id'] ?? null;
    $username = $_POST['username'];
    $password = $_POST['password']; // Default is admin123, but can be changed
    $full_name = $_POST['full_name'];
    $role_id = $_POST['role_id'];

    if (empty($username) || empty($password) || empty($full_name) || empty($role_id)) {
        $error = "Semua field wajib diisi.";
    } else {
        try {
            $pdo->beginTransaction();
            
            if ($id) {
                // Update Existing Staff
                $stmt = $pdo->prepare("UPDATE staff SET username = ?, password = ?, full_name = ?, role_id = ? WHERE id = ?");
                $stmt->execute([$username, $password, $full_name, $role_id, $id]);
                $message = 'Akun staf berhasil diperbarui.';
            } else {
                // Create New Identity First
                $stmt = $pdo->prepare("INSERT INTO identities (actor_type) VALUES ('STAFF')");
                $stmt->execute();
                $identity_id = $pdo->lastInsertId();
                
                // Create New Staff record
                $stmt = $pdo->prepare("INSERT INTO staff (identity_id, username, password, full_name, role_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$identity_id, $username, $password, $full_name, $role_id]);
                $message = 'Akun staf baru berhasil dibuat.';
            }
            
            $pdo->commit();
            
            // Log action
            $current_actor_id = getIdentityId();
            $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
            $stmt->execute([$current_actor_id, ($id ? "Update Staff: $username" : "Tambah Staff: $username"), $_SERVER['REMOTE_ADDR']]);
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Gagal menyimpan akun: ' . $e->getMessage();
        }
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $pdo->beginTransaction();
        
        // Get identity before deletion
        $stmt = $pdo->prepare("SELECT identity_id FROM staff WHERE id = ?");
        $stmt->execute([$id]);
        $identity_id = $stmt->fetchColumn();
        
        if ($identity_id) {
            // Delete staff and their identity (Cascades)
            $stmt = $pdo->prepare("DELETE FROM identities WHERE id = ?");
            $stmt->execute([$identity_id]);
            $pdo->commit();
            $message = 'Akun staf berhasil dihapus.';
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = 'Gagal menghapus akun: ' . $e->getMessage();
    }
}

// Fetch Staff and Roles
$staff_list = $pdo->query("SELECT s.*, r.role_name FROM staff s JOIN roles r ON s.role_id = r.id ORDER BY s.role_id ASC")->fetchAll();
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>

<div class="mb-10 flex justify-between items-center italic">
    <div>
        <h3 class="text-3xl font-bold text-gray-900 leading-none">Manajemen Akun Sistem</h3>
        <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold mt-3">Kontrol Akses Pimpinan & Admin Akademik</p>
    </div>
    <button onclick="openModal()" class="bg-primary text-white px-8 py-4 rounded-[20px] font-bold hover:bg-black transition shadow-xl shadow-blue-100 flex items-center group">
        <svg class="w-5 h-5 mr-3 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
        Tambah Akun Staf
    </button>
</div>

<?php if ($message): ?>
    <div class="mb-8 p-6 bg-emerald-50 border border-emerald-100 rounded-3xl text-emerald-700 text-sm font-bold flex items-center italic">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="mb-8 p-6 bg-red-50 border border-red-100 rounded-3xl text-red-700 text-sm font-bold flex items-center italic">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden italic">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                    <th class="py-8 px-10">Nama Lengkap</th>
                    <th class="py-8 px-10">Username</th>
                    <th class="py-8 px-10">Jabatan / Role</th>
                    <th class="py-8 px-10 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($staff_list as $s): ?>
                <tr class="hover:bg-gray-50 transition group">
                    <td class="py-6 px-10">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-primary flex items-center justify-center font-bold text-[10px]"><?php echo strtoupper(substr($s['full_name'], 0, 1)); ?></div>
                            <span class="font-bold text-gray-900"><?php echo $s['full_name']; ?></span>
                        </div>
                    </td>
                    <td class="py-6 px-10 text-sm italic">@<?php echo $s['username']; ?></td>
                    <td class="py-6 px-10">
                        <span class="px-3 py-1 bg-gray-100 text-[10px] font-bold uppercase tracking-widest rounded-full text-gray-500"><?php echo $s['role_name']; ?></span>
                    </td>
                    <td class="py-6 px-10">
                        <div class="flex items-center justify-center space-x-2">
                             <button onclick="editStaff(<?php echo htmlspecialchars(json_encode($s)); ?>)" class="p-2 border border-gray-100 rounded-lg text-blue-600 hover:bg-blue-600 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <a href="?delete=<?php echo $s['id']; ?>" onclick="return confirm('Hapus akun staf ini?')" class="p-2 border border-gray-100 rounded-lg text-red-500 hover:bg-red-500 hover:text-white transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="staffModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[40px] max-w-lg w-full p-12 shadow-2xl relative overflow-hidden italic">
        <h3 id="modalTitle" class="text-3xl font-bold text-gray-900 mb-8 leading-none">Input Akun Staf</h3>
        
        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="id" id="staff-id">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                <input type="text" name="full_name" id="staff-name" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Username</label>
                <input type="text" name="username" id="staff-username" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Password</label>
                <input type="text" name="password" id="staff-password" required value="admin123" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
                <p class="text-[9px] text-gray-400 mt-1">*Default: admin123</p>
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Jabatan / Level Akses</label>
                <select name="role_id" id="staff-role" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
                    <?php foreach ($roles as $r): ?>
                        <option value="<?php echo $r['id']; ?>"><?php echo $r['role_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex space-x-4 pt-8">
                <button type="button" onclick="closeModal()" class="flex-1 px-8 py-5 border border-gray-100 text-gray-400 rounded-3xl font-bold hover:bg-gray-50 transition">Batal</button>
                <button type="submit" name="save_staff" class="flex-1 px-8 py-5 bg-primary text-white rounded-3xl font-bold hover:bg-gray-900 transition shadow-xl">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('staffModal').classList.remove('hidden');
        document.getElementById('modalTitle').innerText = 'Daftarkan Akun Staf';
        document.getElementById('staff-id').value = '';
        document.getElementById('staff-name').value = '';
        document.getElementById('staff-username').value = '';
        document.getElementById('staff-password').value = 'admin123';
        document.getElementById('staff-role').value = '2';
    }

    function closeModal() {
        document.getElementById('staffModal').classList.add('hidden');
    }

    function editStaff(staff) {
        document.getElementById('staffModal').classList.remove('hidden');
        document.getElementById('modalTitle').innerText = 'Update Akun Staf';
        document.getElementById('staff-id').value = staff.id;
        document.getElementById('staff-name').value = staff.full_name;
        document.getElementById('staff-username').value = staff.username;
        document.getElementById('staff-password').value = staff.password;
        document.getElementById('staff-role').value = staff.role_id;
    }
</script>

<?php require_once 'includes/footer.php'; ?>

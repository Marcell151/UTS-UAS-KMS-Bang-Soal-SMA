<?php
// users.php
$pageTitle = 'Manajemen Pengguna (Administrator TU)';
require_once 'includes/header.php';

// Check Role: Only Administrator (TU) can manage users
checkRole(['Administrator (TU)']);

$message = '';

// Handle Add User
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];

    $stmt = $pdo->prepare("INSERT INTO users (username, full_name, password, role_id) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$username, $full_name, $password, $role_id]);
        $message = 'User berhasil ditambahkan ke sistem.';
    } catch (PDOException $e) {
        $message = 'Error: Username sudah digunakan.';
    }
}

// Handle Delete User
if (isset($_POST['delete_user'])) {
    $id = $_POST['id'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'User berhasil dihapus.';
    } else {
        $message = 'Akses ditolak: Tidak bisa menghapus akun sendiri.';
    }
}

// Fetch all users
$stmt = $pdo->query("SELECT u.*, r.role_name 
                     FROM users u 
                     JOIN roles r ON u.role_id = r.id 
                     ORDER BY r.role_name ASC, u.full_name ASC");
$users = $stmt->fetchAll();

// Fetch Roles
$roles = $pdo->query("SELECT * FROM roles")->fetchAll();
?>

<div class="bg-white rounded-[40px] border border-gray-100 shadow-sm overflow-hidden italic">
    <div class="p-10 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 leading-tight">Manajemen SDM & Role</h3>
            <p class="text-xs text-gray-400 mt-2 uppercase font-bold tracking-widest">Administrator (Tata Usaha) Control Panel</p>
        </div>
        <button onclick="document.getElementById('addUserModal').classList.remove('hidden')" class="bg-gray-900 text-white px-8 py-3.5 rounded-2xl font-bold hover:bg-black transition flex items-center shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Personel
        </button>
    </div>

    <?php if ($message): ?>
    <div class="m-10 bg-blue-50 text-blue-600 p-6 rounded-3xl text-sm italic border border-blue-100 italic">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-white text-gray-400 uppercase text-[10px] font-bold tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-10 py-6">Nama Personel</th>
                    <th class="px-10 py-6">Username</th>
                    <th class="px-10 py-6">Peran (Role)</th>
                    <th class="px-10 py-6">Tanggal Join</th>
                    <th class="px-10 py-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 italic">
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-10 py-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-primary flex items-center justify-center font-bold text-sm uppercase">
                                <?php echo substr($user['full_name'], 0, 1); ?>
                            </div>
                            <span class="font-bold text-gray-900"><?php echo $user['full_name']; ?></span>
                        </div>
                    </td>
                    <td class="px-10 py-6 text-xs text-gray-500 font-bold tracking-wider">@<?php echo $user['username']; ?></td>
                    <td class="px-10 py-6">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?php 
                            if($user['role_name'] == 'Admin Akademik') echo 'bg-red-50 text-red-600';
                            elseif($user['role_name'] == 'Guru') echo 'bg-green-50 text-green-600';
                            elseif($user['role_name'] == 'Kepala Sekolah') echo 'bg-purple-50 text-purple-600';
                            else echo 'bg-blue-50 text-primary'; 
                        ?>">
                            <?php echo $user['role_name']; ?>
                        </span>
                    </td>
                    <td class="px-10 py-6 text-xs text-gray-400"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                    <td class="px-10 py-6 text-center">
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                        <form action="" method="POST" onsubmit="return confirm('Hapus user ini?')" class="inline">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" class="text-gray-300 hover:text-red-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                        <?php else: ?>
                            <span class="text-[10px] text-gray-300 font-bold uppercase italic tracking-widest">You</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[40px] max-w-lg w-full p-10 shadow-2xl">
        <h3 class="text-2xl font-bold text-gray-900 mb-8 font-primary italic">Registrasi User Baru</h3>
        <form action="" method="POST" class="space-y-6">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                <input type="text" name="full_name" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Username</label>
                <input type="text" name="username" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Password</label>
                <input type="password" name="password" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Hak Akses (Role)</label>
                <select name="role_id" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo $role['role_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex space-x-4 pt-6">
                <button type="button" onclick="document.getElementById('addUserModal').classList.add('hidden')" class="flex-1 px-8 py-4 border border-gray-100 text-gray-500 rounded-3xl font-bold hover:bg-gray-50 transition">Batal</button>
                <button type="submit" name="add_user" class="flex-1 px-8 py-4 bg-primary text-white rounded-3xl font-bold hover:bg-blue-700 transition">Simpan User</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

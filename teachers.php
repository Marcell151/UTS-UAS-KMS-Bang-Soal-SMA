<?php
// teachers.php
$pageTitle = 'Manajemen Personel Guru';
require_once 'includes/header.php';

// Check Role: Only Admin Akademik (Administrasi) can manage teachers
checkRoleId([ROLE_ADMIN_AKADEMIK]);

$message = '';
$error = '';

// Handle Add/Edit Teacher
if (isset($_POST['save_teacher'])) {
    $id = $_POST['id'] ?? null;
    $nip = $_POST['nip'];
    $full_name = $_POST['full_name'];
    $pin = $_POST['pin'] ?? '123456';

    if (strlen($nip) < 5) {
        $error = "NIP tidak valid. Mohon periksa kembali.";
    } else {
        try {
            $pdo->beginTransaction();
            
            if ($id) {
                // Update Existing Master Guru
                $stmt = $pdo->prepare("UPDATE teachers SET nip = ?, full_name = ?, pin = ? WHERE id = ?");
                $stmt->execute([$nip, $full_name, $pin, $id]);
                $message = 'Data Guru berhasil diperbarui.';
            } else {
                // Check if NIP exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM teachers WHERE nip = ?");
                $stmt->execute([$nip]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("NIP $nip sudah terdaftar di sistem.");
                }

                // Create New Identity First
                $stmt = $pdo->prepare("INSERT INTO identities (actor_type) VALUES ('TEACHER')");
                $stmt->execute();
                $identity_id = $pdo->lastInsertId();
                
                // Create New Master Guru Record
                $stmt = $pdo->prepare("INSERT INTO teachers (identity_id, nip, full_name, pin) VALUES (?, ?, ?, ?)");
                $stmt->execute([$identity_id, $nip, $full_name, $pin]);
                $message = 'Guru baru berhasil ditambahkan ke Master Data.';
            }
            
            $pdo->commit();
            
            // Log action
            $identity_id_log = getIdentityId();
            $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
            $stmt->execute([$identity_id_log, ($id ? "Update Guru: $full_name ($nip)" : "Tambah Guru: $full_name ($nip)"), $_SERVER['REMOTE_ADDR']]);
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }
}

// Handle Delete (Option B: Keep questions but remove Master Record)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("SELECT full_name FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $t_name = $stmt->fetchColumn();
        
        if ($t_name) {
            $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = ?");
            $stmt->execute([$id]);
            
            $pdo->commit();
            $message = 'Data Guru berhasil dihapus dari Master Data. Soal-soal tetap tersimpan sebagai anonim.';
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

// Fetch Master Teachers
$teachers = $pdo->query("SELECT * FROM teachers ORDER BY full_name ASC")->fetchAll();
?>

<div class="mb-10 flex justify-between items-center italic">
    <div>
        <h3 class="text-3xl font-bold text-gray-900 leading-none">Master Hub: Personel Guru</h3>
        <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold mt-3 italic">Pengaturan Master Data & Identitas NIP Akademik</p>
    </div>
    <button onclick="openModal()" class="bg-primary text-white px-8 py-4 rounded-[20px] font-bold hover:bg-black transition shadow-xl shadow-blue-100 flex items-center group">
        <svg class="w-5 h-5 mr-3 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Registrasi Guru Baru
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

<div class="bg-white rounded-[48px] border border-gray-100 shadow-xl overflow-hidden italic">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                    <th class="py-10 px-10">Identitas NIP</th>
                    <th class="py-10 px-10">Nama Lengkap Guru</th>
                    <th class="py-10 px-10">PIN Login</th>
                    <th class="py-10 px-10 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($teachers as $t): ?>
                <tr class="hover:bg-gray-50 transition group">
                    <td class="py-8 px-10 text-[11px] text-primary font-bold tracking-wider">
                        <?php echo $t['nip']; ?>
                    </td>
                    <td class="py-8 px-10 border-l-4 border-transparent group-hover:border-primary transition-all">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-blue-50 text-primary rounded-xl flex items-center justify-center font-bold text-xs ring-4 ring-white shadow-sm"><?php echo strtoupper(substr($t['full_name'], 0, 1)); ?></div>
                            <span class="text-base font-bold text-gray-900"><?php echo $t['full_name']; ?></span>
                        </div>
                    </td>
                    <td class="py-8 px-10">
                        <span class="px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-xl tracking-[0.2em] shadow-lg"><?php echo $t['pin']; ?></span>
                    </td>
                    <td class="py-8 px-10">
                        <div class="flex items-center justify-center space-x-3">
                            <button onclick="editTeacher(<?php echo htmlspecialchars(json_encode($t)); ?>)" class="p-3 bg-white border border-gray-100 rounded-xl text-blue-600 hover:bg-blue-600 hover:text-white transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <a href="?delete=<?php echo $t['id']; ?>" onclick="return confirm('Hapus data guru dari master?')" class="p-3 bg-white border border-gray-100 rounded-xl text-red-500 hover:bg-red-500 hover:text-white transition shadow-sm">
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
<div id="teacherModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[48px] max-w-lg w-full p-12 shadow-2xl relative overflow-hidden italic">
        <div class="absolute top-0 left-0 w-full h-2 bg-primary"></div>
        <h3 id="modalTitle" class="text-3xl font-bold text-gray-900 mb-2 leading-none">Master Guru Input</h3>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-10 italic">Pendaftaran Data Master Personel Akademik</p>
        
        <form action="" method="POST" class="space-y-6">
            <input type="hidden" name="id" id="teacher-id">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">NIP (18 Digit)</label>
                <input type="text" name="nip" id="teacher-nip" required maxlength="18" placeholder="Contoh: 198501012010121001" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner italic">
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap & Gelar</label>
                <input type="text" name="full_name" id="teacher-name" required placeholder="Contoh: Budi Santoso, S.Pd" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner italic">
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-primary uppercase tracking-widest ml-1">PIN Login (6 Digit)</label>
                <input type="text" name="pin" id="teacher-pin" required maxlength="6" value="123456" class="w-full px-6 py-4 bg-blue-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner font-bold tracking-[1em] text-center italic">
            </div>
            <div class="flex space-x-4 pt-8">
                <button type="button" onclick="closeModal()" class="flex-1 px-8 py-5 border border-gray-100 text-gray-400 rounded-3xl font-bold hover:bg-gray-50 transition">Batal</button>
                <button type="submit" name="save_teacher" class="flex-1 px-8 py-5 bg-primary text-white rounded-3xl font-bold hover:bg-gray-900 transition shadow-xl shadow-blue-100">Simpan Ke Master</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('teacherModal').classList.remove('hidden');
        document.getElementById('modalTitle').innerText = 'Daftarkan Master Guru';
        document.getElementById('teacher-id').value = '';
        document.getElementById('teacher-nip').value = '';
        document.getElementById('teacher-name').value = '';
        document.getElementById('teacher-pin').value = '123456';
    }

    function closeModal() {
        document.getElementById('teacherModal').classList.add('hidden');
    }

    function editTeacher(teacher) {
        document.getElementById('teacherModal').classList.remove('hidden');
        document.getElementById('modalTitle').innerText = 'Update Data Master';
        document.getElementById('teacher-id').value = teacher.id;
        document.getElementById('teacher-nip').value = teacher.nip;
        document.getElementById('teacher-name').value = teacher.full_name;
        document.getElementById('teacher-pin').value = teacher.pin;
    }
</script>

<?php require_once 'includes/footer.php'; ?>

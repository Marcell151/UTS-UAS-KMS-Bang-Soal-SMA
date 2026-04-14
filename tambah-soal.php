<?php
// tambah-soal.php
$pageTitle = 'Kontribusi Soal Baru';
require_once 'includes/header.php';

// Check Role: Only Guru and Admin Akademik can contribute questions
checkRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK]);

$error = '';
$success = '';

// Fetch Categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

// Fetch Classes
$stmt = $pdo->query("SELECT * FROM classes ORDER BY name ASC");
$classes = $stmt->fetchAll();

// Fetch SOPs and Templates for reference sidebar
$sops_ref = $pdo->query("SELECT * FROM kms_explicit ORDER BY type DESC, created_at DESC LIMIT 5")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $class_id = $_POST['class_id'];
    $materi = $_POST['materi'];
    $explanation = $_POST['explanation'];
    $difficulty = $_POST['difficulty'];
    $identityId = getIdentityId();
    
    $newFileName = null;
    $originalName = null;
    $fileExtension = null;

    // Handle File Upload if exists
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $originalName = $_FILES['document']['name'];
        $fileNameCmps = explode(".", $originalName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExtensions = ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $originalName) . '.' . $fileExtension;
            $uploadFileDir = 'storage/documents/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            if (!move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                $error = 'Gagal mengupload file.';
            }
        } else {
            $error = 'Format file tidak didukung.';
        }
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO questions (title, class_id, category_id, materi, file_path, original_name, file_type, explanation, uploader_id, difficulty, status) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $class_id, $category_id, $materi, $newFileName, $originalName, $fileExtension, $explanation, $identityId, $difficulty, STATUS_DRAFT]);
            
            $question_id = $pdo->lastInsertId();

            // Log Initial Status
            $stmt = $pdo->prepare("INSERT INTO question_status_logs (question_id, actor_id, old_status, new_status, notes) VALUES (?, ?, NULL, ?, ?)");
            $stmt->execute([$question_id, $identityId, STATUS_DRAFT, 'Soal pertama kali dibuat sebagai Draft']);

            // Activity Log
            $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
            $stmt->execute([$identityId, "Membuat soal baru: $title", $_SERVER['REMOTE_ADDR']]);

            // [NEW] Notification Logic: Notify all Admin Akademik
            $stmt_admin = $pdo->prepare("SELECT identity_id FROM staff WHERE role_id = ?");
            $stmt_admin->execute([ROLE_ADMIN_AKADEMIK]);
            $admins = $stmt_admin->fetchAll();
            foreach ($admins as $admin) {
                addNotification($pdo, $admin['identity_id'], "Soal baru '$title' butuh review Anda.", "bank-soal.php?status=Review");
            }

            $pdo->commit();
            header('Location: bank-soal.php?success=1');
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }
}
?>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-10 italic">
    <!-- Form Section -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden mb-12">
            <div class="bg-primary p-10 text-white relative">
                <h3 class="text-2xl font-bold">Kontribusi Bank Soal</h3>
                <p class="text-blue-100 mt-2">Gunakan formulir di bawah untuk menambahkan soal ke dalam sistem.</p>
                <svg class="absolute top-0 right-0 w-32 h-32 text-white opacity-10 -mr-8 -mt-8" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-3 3 5z"></path></svg>
            </div>

            <?php if ($error): ?>
            <div class="m-10 bg-red-50 text-red-600 p-6 rounded-3xl text-sm border border-red-100">
                <strong>Gagal:</strong> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Judul Soal / Dokumen</label>
                        <input type="text" name="title" required placeholder="Contoh: PAKET UTS MATEMATIKA X" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner italic">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mata Pelajaran</label>
                        <select name="category_id" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner italic">
                            <option value="">Pilih Mapel</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kelas</label>
                        <select name="class_id" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner italic">
                            <option value="">Pilih Kelas</option>
                            <?php foreach ($classes as $cls): ?>
                                <option value="<?php echo $cls['id']; ?>">Kelas <?php echo $cls['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Tingkat Kesulitan</label>
                        <select name="difficulty" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner italic">
                            <option value="Mudah">Mudah</option>
                            <option value="Sedang" selected>Sedang</option>
                            <option value="Sulit">Sulit</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Materi / Topik Utama</label>
                    <input type="text" name="materi" required placeholder="Contoh: Trigonometri Lanjut" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner italic">
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-blue-600 uppercase tracking-widest">File Pendukung (Optional)</label>
                    <input type="file" name="document" class="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-red-500 uppercase tracking-widest">Pembahasan / Knowledge Reflection (Explicit Knowledge)</label>
                    <textarea id="editor" name="explanation" class="w-full px-6 py-4 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner min-h-[250px]"></textarea>
                    <p class="text-[10px] text-gray-400 mt-2 font-bold italic">* Bagian ini wajib diisi sebagai dokumentasi pengetahuan akademik.</p>
                </div>

                <div class="flex space-x-4 pt-10">
                    <a href="bank-soal.php" class="flex-1 text-center px-10 py-5 border border-gray-100 text-gray-500 rounded-3xl font-bold hover:bg-gray-50 transition">Batal</a>
                    <button type="submit" class="flex-1 px-10 py-5 bg-primary text-white rounded-3xl font-bold hover:bg-black transition shadow-2xl shadow-blue-200">Simpan Draft Soal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar Reference -->
    <div class="space-y-8">
        <div class="bg-gray-900 rounded-[40px] p-8 text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-primary opacity-20 rounded-full blur-2xl"></div>
            <h4 class="text-[10px] font-bold text-blue-400 uppercase tracking-[0.2em] mb-6 relative z-10">Academic Guidelines</h4>
            
            <div class="space-y-6 relative z-10">
                <?php if (empty($sops_ref)): ?>
                    <p class="text-[10px] text-gray-500 italic">Belum ada panduan resmi.</p>
                <?php endif; ?>
                <?php foreach ($sops_ref as $ref): ?>
                <div class="flex items-start space-x-3 group/item">
                    <div class="w-8 h-8 rounded-lg bg-white bg-opacity-[0.05] flex items-center justify-center shrink-0 group-hover/item:bg-primary transition-colors">
                        <svg class="w-4 h-4 text-blue-400 group-hover/item:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <a href="storage/kms/<?php echo $ref['file_path']; ?>" download class="text-[11px] font-bold text-gray-200 hover:text-blue-400 transition-colors line-clamp-2"><?php echo $ref['title']; ?></a>
                        <span class="text-[8px] text-gray-500 uppercase font-bold tracking-widest"><?php echo $ref['type']; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-10 p-5 bg-white bg-opacity-[0.03] rounded-2xl border border-white border-opacity-5">
                <p class="text-[10px] text-gray-400 leading-relaxed italic">
                    "Pastikan soal Anda memenuhi standar HOTS dan mencantumkan penjelasan yang detail untuk membantu proses KMS."
                </p>
            </div>
        </div>

        <div class="bg-blue-50 rounded-[32px] p-8 border border-blue-100 italic">
            <h5 class="text-xs font-bold text-primary uppercase tracking-widest mb-3">Butuh Bantuan?</h5>
            <p class="text-[11px] text-gray-500 leading-relaxed mb-4">Jika Anda mengalami kesulitan dalam mengupload soal, silakan hubungi <span class="text-primary font-bold">Admin Akademik</span> atau diskusikan di forum.</p>
            <a href="forum.php" class="text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">Buka Forum Diskusi &rarr;</a>
        </div>
    </div>
</div>

<script>
    tinymce.init({
        selector: '#editor',
        height: 400,
        plugins: 'lists link image code table help wordcount',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        menubar: false,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>

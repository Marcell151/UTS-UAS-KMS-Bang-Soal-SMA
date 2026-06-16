<?php
// edit-soal.php
$pageTitle = 'Revisi & Update Soal';
require_once 'includes/header.php';

// Check Role: Only Guru and Admin Akademik can contribute questions
checkRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK, ROLE_ADMIN_SISTEM]);

$error = '';
$success = '';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: bank-soal.php');
    exit();
}

$identityId = getIdentityId();

// Fetch the existing question
$stmt = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmt->execute([$id]);
$question = $stmt->fetch();

if (!$question) {
    header('Location: bank-soal.php');
    exit();
}

// Only the original uploader or Admin can edit
if ($question['uploader_id'] != $identityId && !hasRoleId([ROLE_ADMIN_AKADEMIK, ROLE_ADMIN_SISTEM])) {
    echo "<script>alert('Anda tidak memiliki akses untuk mengedit soal ini.'); window.location.href='view-soal.php?id=$id';</script>";
    exit();
}

// Fetch Categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

// Fetch Classes
$stmt = $pdo->query("SELECT * FROM classes ORDER BY name ASC");
$classes = $stmt->fetchAll();

// Fetch SOPs and Templates for reference sidebar
$sops_ref = $pdo->query("SELECT * FROM kms_explicit WHERE is_archived = 0 ORDER BY type DESC, created_at DESC LIMIT 5")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $class_id = $_POST['class_id'];
    $materi = $_POST['materi'];
    $tags = $_POST['tags'] ?? '';
    $explanation = $_POST['explanation'];
    $difficulty = $_POST['difficulty'];
    $tingkat_kognitif = $_POST['tingkat_kognitif'] ?? 'C2 - Memahami';
    $jenis_soal = $_POST['jenis_soal'] ?? 'Pilihan Ganda';
    
    // Default to keep old file
    $newFileName = $question['file_path'];
    $originalName = $question['original_name'];
    $fileExtension = $question['file_type'];
    $isFileUpdated = false;

    // Handle File Upload if exists
    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $originalName = $_FILES['document']['name'];
        $fileNameCmps = explode(".", $originalName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExtensions = ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $originalName) . '.' . $fileExtension;
            $uploadFileDir = 'upload/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            if (!move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                $error = 'Gagal mengupload file baru.';
            } else {
                $isFileUpdated = true;
            }
        } else {
            $error = 'Format file tidak didukung.';
        }
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction();

            $update_stmt = $pdo->prepare("UPDATE questions SET title = ?, class_id = ?, category_id = ?, materi = ?, tags = ?, file_path = ?, original_name = ?, file_type = ?, explanation = ?, difficulty = ?, tingkat_kognitif = ?, jenis_soal = ?, status = ? WHERE id = ?");
            // Reset status to Review when edited
            $update_stmt->execute([$title, $class_id, $category_id, $materi, $tags, $newFileName, $originalName, $fileExtension, $explanation, $difficulty, $tingkat_kognitif, $jenis_soal, STATUS_REVIEW, $id]);
            
            // Log Status Change to Review
            $stmt = $pdo->prepare("INSERT INTO question_status_logs (question_id, actor_id, old_status, new_status, notes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id, $identityId, $question['status'], STATUS_REVIEW, 'Soal direvisi dan diupdate. Otomatis dikembalikan ke status Review.']);

            // Activity Log
            $logMsg = $isFileUpdated ? "Merevisi dan mengunggah dokumen baru untuk soal: $title" : "Merevisi metadata soal: $title";
            $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
            $stmt->execute([$identityId, $logMsg, $_SERVER['REMOTE_ADDR']]);

            // Notification Logic: Notify Admin
            $stmt_admin = $pdo->prepare("SELECT identity_id FROM staff WHERE role_id = ?");
            $stmt_admin->execute([ROLE_ADMIN_AKADEMIK]);
            $admins = $stmt_admin->fetchAll();
            foreach ($admins as $admin) {
                addNotification($pdo, $admin['identity_id'], "Soal '$title' telah direvisi dan butuh review ulang Anda.", "view-soal.php?id=$id");
            }

            $pdo->commit();
            header("Location: view-soal.php?id=$id&updated=1");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Gagal menyimpan data: ' . $e->getMessage();
        }
    }
}
?>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
    <!-- Form Section -->
    <div class="lg:col-span-3">
        <div class="bg-white rounded-[48px] border border-gray-100 shadow-2xl shadow-blue-50 overflow-hidden mb-12">
            <div class="bg-teal-700 p-12 text-white relative">
                <div class="relative z-10">
                    <span class="px-4 py-1.5 bg-white bg-opacity-10 text-white text-[10px] font-black rounded-full uppercase tracking-widest mb-4 inline-block">Knowledge Update</span>
                    <h3 class="text-3xl font-black">Revisi Bank Soal</h3>
                    <p class="text-teal-100 mt-3 max-w-lg opacity-80">Perbarui aset soal ini. Setelah disimpan, status akan kembali menjadi "Review" untuk divalidasi ulang.</p>
                </div>
                <svg class="absolute top-0 right-0 w-48 h-48 text-white opacity-5 -mr-12 -mt-12" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-3 3 5z"></path></svg>
            </div>

            <?php if ($error): ?>
            <div class="mx-12 mt-10 bg-red-50 text-red-600 p-8 rounded-3xl text-sm border border-red-100 flex items-center">
                <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <strong>Gagal:</strong> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="p-12 space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Judul Soal / Nama Dokumen</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($question['title']); ?>" required class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-semibold">
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Mata Pelajaran</label>
                        <select name="category_id" required class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-bold">
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $question['category_id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Level Kelas</label>
                        <select name="class_id" required class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-bold">
                            <?php foreach ($classes as $cls): ?>
                                <option value="<?php echo $cls['id']; ?>" <?php echo $cls['id'] == $question['class_id'] ? 'selected' : ''; ?>>Kelas <?php echo $cls['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kesulitan</label>
                        <select name="difficulty" class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-bold text-red-600">
                            <option value="Mudah" <?php echo $question['difficulty'] == 'Mudah' ? 'selected' : ''; ?>>🟢 Mudah</option>
                            <option value="Sedang" <?php echo $question['difficulty'] == 'Sedang' ? 'selected' : ''; ?>>🟡 Sedang</option>
                            <option value="Sulit" <?php echo $question['difficulty'] == 'Sulit' ? 'selected' : ''; ?>>🔴 Sulit</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Tingkat Kognitif</label>
                        <select name="tingkat_kognitif" class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-bold text-[#003366]" required>
                            <option value="C1 - Mengingat" <?php echo $question['tingkat_kognitif'] == 'C1 - Mengingat' ? 'selected' : ''; ?>>C1 - Mengingat (Recall)</option>
                            <option value="C2 - Memahami" <?php echo $question['tingkat_kognitif'] == 'C2 - Memahami' ? 'selected' : ''; ?>>C2 - Memahami (Understand)</option>
                            <option value="C3 - Mengaplikasikan" <?php echo $question['tingkat_kognitif'] == 'C3 - Mengaplikasikan' ? 'selected' : ''; ?>>C3 - Mengaplikasikan (Apply)</option>
                            <option value="C4 - Menganalisis" <?php echo $question['tingkat_kognitif'] == 'C4 - Menganalisis' ? 'selected' : ''; ?>>C4 - Menganalisis (Analyze)</option>
                            <option value="C5 - Mengevaluasi" <?php echo $question['tingkat_kognitif'] == 'C5 - Mengevaluasi' ? 'selected' : ''; ?>>C5 - Mengevaluasi (Evaluate)</option>
                            <option value="C6 - Mencipta" <?php echo $question['tingkat_kognitif'] == 'C6 - Mencipta' ? 'selected' : ''; ?>>C6 - Mencipta (Create)</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Soal</label>
                        <select name="jenis_soal" class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-bold text-teal-600" required>
                            <option value="Pilihan Ganda" <?php echo $question['jenis_soal'] == 'Pilihan Ganda' ? 'selected' : ''; ?>>📝 Pilihan Ganda</option>
                            <option value="Essay" <?php echo $question['jenis_soal'] == 'Essay' ? 'selected' : ''; ?>>✍️ Essay</option>
                            <option value="Isian Singkat" <?php echo $question['jenis_soal'] == 'Isian Singkat' ? 'selected' : ''; ?>>✏️ Isian Singkat</option>
                            <option value="Praktikum" <?php echo $question['jenis_soal'] == 'Praktikum' ? 'selected' : ''; ?>>🔬 Praktikum</option>
                            <option value="Lainnya" <?php echo $question['jenis_soal'] == 'Lainnya' ? 'selected' : ''; ?>>📌 Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Materi / Topik Utama</label>
                    <input type="text" name="materi" value="<?php echo htmlspecialchars($question['materi']); ?>" required class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner font-semibold">
                </div>

                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kata Kunci / Tag (Pisahkan dengan koma)</label>
                    <input type="text" name="tags" value="<?php echo htmlspecialchars($question['tags']); ?>" placeholder="Contoh: PTS, HOTS, Kurikulum Merdeka" class="w-full px-8 py-5 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner font-semibold text-blue-700">
                </div>

                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-[#003366] uppercase tracking-widest ml-1">File Pendukung Baru (Abaikan jika tidak ingin mengganti file)</label>
                    <div class="p-6 bg-teal-50 rounded-3xl border-2 border-dashed border-teal-200 text-center hover:bg-teal-100 transition duration-300">
                        <input type="file" name="document" class="text-sm text-gray-500 file:mr-4 file:py-3 file:px-6 file:rounded-2xl file:border-0 file:text-xs file:font-black file:bg-teal-700 file:text-white hover:file:bg-black file:cursor-pointer cursor-pointer">
                        <?php if ($question['original_name']): ?>
                            <p class="text-[10px] text-teal-700 mt-3 font-bold">File Terpasang Saat Ini: <?php echo $question['original_name']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="block text-[10px] font-black text-red-600 uppercase tracking-widest ml-1">Pembahasan / Knowledge Reflection</label>
                    <textarea id="editor" name="explanation" class="w-full px-8 py-5 bg-gray-50 border-none rounded-[40px] outline-none focus:ring-2 focus:ring-[#003366] transition shadow-inner min-h-[300px]"><?php echo htmlspecialchars($question['explanation']); ?></textarea>
                </div>

                <div class="flex items-center space-x-6 pt-10">
                    <a href="view-soal.php?id=<?php echo $id; ?>" class="px-10 py-5 text-gray-400 font-black uppercase text-[10px] tracking-widest hover:text-gray-600 transition">Batalkan</a>
                    <button type="submit" class="flex-1 px-12 py-6 bg-teal-700 text-white rounded-[32px] font-black uppercase text-xs tracking-[0.2em] hover:bg-black transition-all duration-300 shadow-2xl shadow-teal-100">Simpan Perubahan & Ajukan Review</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar Reference -->
    <div class="space-y-10">
        <div class="bg-gray-900 rounded-[48px] p-10 text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-[#003366] opacity-30 rounded-full blur-3xl"></div>
            <h4 class="text-[11px] font-black text-blue-400 uppercase tracking-[0.3em] mb-10 relative z-10">Juknis Akademik</h4>
            
            <div class="space-y-8 relative z-10">
                <?php if (empty($sops_ref)): ?>
                    <p class="text-[10px] text-gray-500 italic">Belum ada panduan resmi diunggah.</p>
                <?php endif; ?>
                <?php foreach ($sops_ref as $ref): ?>
                <div class="flex items-start space-x-4 group/item">
                    <div class="w-10 h-10 rounded-2xl bg-white bg-opacity-5 flex items-center justify-center shrink-0 group-hover/item:bg-red-600 transition-colors duration-300">
                        <svg class="w-5 h-5 text-blue-400 group-hover/item:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <a href="storage/kms/<?php echo $ref['file_path']; ?>" download class="text-[11px] font-black text-gray-200 hover:text-blue-400 transition-colors line-clamp-2"><?php echo $ref['title']; ?></a>
                        <span class="text-[8px] text-gray-500 uppercase font-black tracking-widest mt-1 block"><?php echo $ref['type']; ?> Asset</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    ClassicEditor
        .create( document.querySelector( '#editor' ), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo' ]
        } )
        .catch( error => {
            console.error( error );
        } );
</script>

<?php require_once 'includes/footer.php'; ?>

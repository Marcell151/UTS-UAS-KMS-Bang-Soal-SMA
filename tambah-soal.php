<?php
// tambah-soal.php
$pageTitle = 'Kontribusi Soal Baru';
require_once 'includes/header.php';

// Check Role
checkRole(['Guru', 'Admin Akademik']);

$error = '';

// Fetch Categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category_id = $_POST['category_id'];
    $class_level = $_POST['class_level'];
    $materi = $_POST['materi'];
    $explanation = $_POST['explanation'];
    $difficulty = $_POST['difficulty'];
    $uploader_id = $_SESSION['user_id'];

    if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['document']['tmp_name'];
        $originalName = $_FILES['document']['name'];
        $fileSize = $_FILES['document']['size'];
        $fileType = $_FILES['document']['type'];
        $fileNameCmps = explode(".", $originalName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['doc', 'docx', 'pdf', 'xls', 'xlsx', 'ppt', 'pptx'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = md5(time() . $originalName) . '.' . $fileExtension;
            $uploadFileDir = 'storage/documents/';
            
            // Ensure dir exists
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }

            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $stmt = $pdo->prepare("INSERT INTO documents (title, class_level, materi, file_path, original_name, file_type, explanation, uploader_id, category_id, difficulty, status) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Draft')");
                try {
                    $stmt->execute([$title, $class_level, $materi, $newFileName, $originalName, $fileExtension, $explanation, $uploader_id, $category_id, $difficulty]);
                    
                    // Log
                    $stmt = $pdo->prepare("INSERT INTO logs (user_id, action) VALUES (?, ?)");
                    $stmt->execute([$uploader_id, "Uploaded new bank soal: $title"]);

                    echo "<script>window.location.href='bank-soal.php?success=1';</script>";
                    exit();
                } catch (PDOException $e) {
                    $error = 'Gagal menyimpan data ke database: ' . $e->getMessage();
                }
            } else {
                $error = 'Terjadi kesalahan saat mengupload file ke server.';
            }
        } else {
            $error = 'Format file tidak didukung. Gunakan PDF, Word, Excel, atau PPT.';
        }
    } else {
        $error = 'Harap pilih file dokumen soal untuk diupload.';
    }
}
?>

<div class="max-w-4xl mx-auto italic">
    <div class="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden mb-12">
        <div class="bg-primary p-10 text-white relative">
            <h3 class="text-2xl font-bold">Kontribusi Pengetahuan Eksplisit</h3>
            <p class="text-blue-100 mt-2">Upload dokumen soal dan berikan pembahasan mendalam.</p>
            <svg class="absolute top-0 right-0 w-32 h-32 text-white opacity-10 -mr-8 -mt-8" fill="currentColor" viewBox="0 0 20 20"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-3 3 5z"></path></svg>
        </div>

        <?php if ($error): ?>
        <div class="m-10 bg-red-50 text-red-600 p-6 rounded-3xl text-sm italic border border-red-100 italic">
            <strong>Gagal:</strong> <?php echo $error; ?>
        </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Judul Dokumen</label>
                    <input type="text" name="title" required placeholder="Contoh: PAKET SOAL UTS FISIKA - KELAS X" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mata Pelajaran</label>
                    <select name="category_id" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
                        <option value="">Pilih Mapel</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kelas</label>
                    <select name="class_level" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
                        <option value="X">Kelas X</option>
                        <option value="XI">Kelas XI</option>
                        <option value="XII">Kelas XII</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">File Soal (Word/PDF)</label>
                    <input type="file" name="document" required class="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Materi / Topik Pembahasan</label>
                <input type="text" name="materi" required placeholder="Contoh: Kinematika Gerak Lurus" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-red-500 uppercase tracking-widest">Pembahasan Lengkap (WAJIB - Knowledge Reflection)</label>
                <textarea id="editor" name="explanation" class="w-full px-6 py-4 bg-gray-50 border-none rounded-3xl outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner min-h-[200px]"></textarea>
                <p class="text-[10px] text-gray-400 mt-2 font-bold italic">* Bagian ini sangat penting untuk pembagian pengetahuan antar guru.</p>
            </div>

            <div class="flex space-x-4 pt-10">
                <a href="bank-soal.php" class="flex-1 text-center px-10 py-5 border border-gray-100 text-gray-500 rounded-3xl font-bold hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="flex-1 px-10 py-5 bg-primary text-white rounded-3xl font-bold hover:bg-black transition shadow-2xl shadow-blue-200">Kirim Pengetahuan</button>
            </div>
        </form>
    </div>
</div>

<script>
    tinymce.init({
        selector: '#editor',
        height: 400,
        plugins: 'lists link code',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | code',
        menubar: false,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>

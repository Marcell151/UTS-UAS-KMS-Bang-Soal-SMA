<?php
// templates.php
$pageTitle = 'Explicit Knowledge: SOP & Templates';
require_once 'includes/header.php';

// Check Role: Everyone can see, but Admin Akademik can upload
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Handle Upload (Admin Akademik)
if (isset($_POST['upload_kms']) && hasRoleId([ROLE_ADMIN_AKADEMIK])) {
    $title = $_POST['title'];
    $type = $_POST['type']; // SOP or Template
    $identityId = getIdentityId();

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $originalName = $_FILES['file']['name'];
        $fileNameCmps = explode(".", $originalName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $newFileName = md5(time() . $originalName) . '.' . $fileExtension;
        $uploadFileDir = 'storage/kms/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
            $stmt = $pdo->prepare("INSERT INTO kms_explicit (title, type, file_path, uploader_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $type, $newFileName, $identityId]);
            
            // Log activity
            $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
            $stmt->execute([$identityId, "Upload KMS Explicit ($type): $title", $_SERVER['REMOTE_ADDR']]);
            
            $message = 'Dokumen pengetahuan berhasil diunggah.';
        } else {
            $error = 'Gagal mengupload file ke direktori.';
        }
    }
}

// Fetch KMS Documents
$templates = $pdo->query("SELECT * FROM kms_explicit WHERE type = 'Template' ORDER BY created_at DESC")->fetchAll();
$sops = $pdo->query("SELECT * FROM kms_explicit WHERE type = 'SOP' ORDER BY created_at DESC")->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
    <div>
        <h3 class="text-3xl font-bold text-gray-900 leading-none">Pusat Pengetahuan Eksplisit</h3>
        <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold mt-3 italic">SMA Kristen Kalam Kudus Malang • SOP & Template Resmi</p>
    </div>
    <?php if (hasRoleId([ROLE_ADMIN_AKADEMIK])): ?>
    <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" class="bg-primary text-white px-8 py-4 rounded-[20px] font-bold hover:bg-black transition shadow-xl shadow-blue-100 flex items-center group">
        <svg class="w-5 h-5 mr-3 group-hover:rotate-45 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Upload SOP/Template
    </button>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
    <!-- Templates Section -->
    <div class="bg-white rounded-[48px] border border-gray-100 shadow-xl overflow-hidden p-10">
        <div class="flex items-center space-x-5 mb-10">
            <div class="p-4 bg-blue-50 text-primary rounded-2xl shadow-sm">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Template Akademik</h3>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Standarisasi Format Dokumen</p>
            </div>
        </div>

        <div class="space-y-6">
            <?php if (empty($templates)): ?>
                <div class="p-10 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200 text-gray-400">Belum ada template tersedia.</div>
            <?php endif; ?>
            <?php foreach ($templates as $tmpl): ?>
            <div class="flex items-center justify-between p-6 bg-gray-50 hover:bg-white hover:border-primary hover:shadow-xl hover:shadow-blue-50 rounded-3xl border border-gray-50 transition-all duration-300 group">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-base font-bold text-gray-800"><?php echo $tmpl['title']; ?></p>
                        <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold mt-1">Uploaded: <?php echo date('d M Y', strtotime($tmpl['created_at'])); ?></p>
                    </div>
                </div>
                <a href="storage/kms/<?php echo $tmpl['file_path']; ?>" download class="bg-white text-primary hover:bg-primary hover:text-white border border-blue-100 p-3 rounded-xl transition-all duration-300 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SOP Section (Premium Dark) -->
    <div class="bg-gray-900 rounded-[48px] p-10 text-white shadow-2xl relative overflow-hidden flex flex-col group">
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary rounded-full blur-[100px] opacity-20"></div>
        
        <div class="relative z-10 flex items-center space-x-5 mb-10">
            <div class="p-4 bg-white bg-opacity-10 rounded-2xl shadow-inner border border-white border-opacity-5">
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-white leading-none">Prosedur Standar (SOP)</h3>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[0.2em] mt-2">Governance & Quality Integrity</p>
            </div>
        </div>

        <div class="relative z-10 space-y-4 mb-20">
            <?php if (empty($sops)): ?>
                <div class="p-10 text-center bg-white bg-opacity-[0.03] rounded-3xl border border-dashed border-gray-800 text-gray-500 italic">Belum ada panduan SOP tersedia.</div>
            <?php endif; ?>
            <?php foreach ($sops as $sop): ?>
            <div class="flex items-center justify-between p-6 bg-white bg-opacity-[0.03] hover:bg-opacity-[0.06] rounded-3xl border border-white border-opacity-5 transition-all duration-300">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-blue-500 bg-opacity-20 rounded-xl flex items-center justify-center text-blue-400">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-200"><?php echo $sop['title']; ?></p>
                        <p class="text-[9px] text-gray-600 font-bold uppercase tracking-widest mt-1">Official Document • Published: <?php echo date('d M Y', strtotime($sop['created_at'])); ?></p>
                    </div>
                </div>
                <a href="storage/kms/<?php echo $sop['file_path']; ?>" download class="bg-gray-800 text-white hover:bg-blue-600 p-2.5 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-auto p-8 bg-blue-600 rounded-[32px] text-white shadow-2xl shadow-blue-900 shadow-opacity-40">
            <h5 class="text-[10px] font-bold text-blue-200 uppercase tracking-widest mb-3 italic leading-none">Pesan Integritas:</h5>
            <p class="text-xs leading-relaxed font-medium">Standardisasi Prosedur adalah langkah awal memastikan <span class="font-bold underline italic">Academic Excellence</span> tetap terjaga di seluruh lini SMA KK Malang.</p>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[40px] max-w-lg w-full p-12 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-2 bg-primary"></div>
        <h3 class="text-3xl font-bold text-gray-900 mb-2 leading-none">Kontribusi KMS</h3>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-10">Upload SOP atau Template Soal Baru</p>
        
        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Judul Dokumen</label>
                <input type="text" name="title" required placeholder="Contoh: Format Soal HOTS 2026" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner">
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Jenis Pengetahuan</label>
                <select name="type" required class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner text-sm">
                    <option value="<?php echo KMS_TYPE_TEMPLATE; ?>">Template (Format Dokumen)</option>
                    <option value="<?php echo KMS_TYPE_SOP; ?>">SOP (Panduan Prosedur)</option>
                </select>
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih File</label>
                <input type="file" name="file" required class="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner text-xs file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-bold file:bg-primary file:text-white">
            </div>
            <div class="flex space-x-4 pt-8">
                <button type="button" onclick="document.getElementById('uploadModal').classList.add('hidden')" class="flex-1 px-8 py-5 border border-gray-100 text-gray-400 rounded-3xl font-bold hover:bg-gray-50 transition">Batal</button>
                <button type="submit" name="upload_kms" class="flex-1 px-8 py-5 bg-primary text-white rounded-3xl font-bold hover:bg-gray-900 transition shadow-xl shadow-blue-100">Simpan Aset</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

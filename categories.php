<?php
// categories.php
$pageTitle = 'Tata Kelola Domain Pengetahuan';
require_once 'includes/header.php';

// Check Role: Admin Akademik / Administrasi focus on academic categories
checkRoleId([ROLE_ADMIN_AKADEMIK]);

$message = '';
$error = '';

// Handle Add/Delete Mapel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $code = strtoupper($_POST['code']);
        $name = $_POST['name'];
        $desc = $_POST['description'] ?? '';
        
        try {
            $pdo->beginTransaction();
            
            // Check if code exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE code = ?");
            $stmt->execute([$code]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Kode Mapel $code sudah digunakan.");
            }

            $stmt = $pdo->prepare("INSERT INTO categories (code, name, description) VALUES (?, ?, ?)");
            $stmt->execute([$code, $name, $desc]);
            
            // Log activity
            $identityId = getIdentityId();
            $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
            $stmt->execute([$identityId, "Menambahkan mata pelajaran baru: $name ($code)", $_SERVER['REMOTE_ADDR']]);
            
            $pdo->commit();
            $message = 'Mata pelajaran berhasil ditambahkan ke basis pengetahuan.';
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Gagal menyimpan: ' . $e->getMessage();
        }
    } elseif (isset($_POST['delete_category'])) {
        $id = $_POST['id'];
        
        // Check if category is used
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE category_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            $error = 'Gagal menghapus: Mata pelajaran masih digunakan oleh soal yang ada.';
        } else {
             // Get name for log
            $stmt = $pdo->prepare("SELECT name, code FROM categories WHERE id = ?");
            $stmt->execute([$id]);
            $cat = $stmt->fetch();

            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$id]);

            // Log activity
            $identityId = getIdentityId();
            $stmt = $pdo->prepare("INSERT INTO logs (actor_id, action, ip_address) VALUES (?, ?, ?)");
            $stmt->execute([$identityId, "Menghapus mata pelajaran: " . $cat['name'] . " (" . $cat['code'] . ")", $_SERVER['REMOTE_ADDR']]);

            $message = 'Mata pelajaran berhasil dihapus.';
        }
    }
}

// Fetch Mapel
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

// Fetch Classes for display
$classes = $pdo->query("SELECT * FROM classes ORDER BY id ASC")->fetchAll();
?>

<div class="mb-10 italic">
    <h3 class="text-3xl font-bold text-gray-900 leading-none">Domain Mata Pelajaran</h3>
    <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold mt-3 italic">Pusat Pengaturan Pengetahuan Akademik SMA KK Malang</p>
</div>

<?php if ($message): ?>
<div class="mb-8 bg-green-50 text-green-600 p-6 rounded-[28px] text-sm border border-green-100 flex items-center shadow-sm italic">
     <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
    <?php echo $message; ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="mb-8 bg-red-50 text-red-600 p-6 rounded-[28px] text-sm border border-red-100 flex items-center shadow-sm italic">
     <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
    <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-10 italic">
    <!-- Mapel Management -->
    <div class="lg:col-span-2 bg-white rounded-[48px] border border-gray-100 shadow-xl overflow-hidden p-2">
        <div class="p-8 flex justify-between items-center">
            <div>
                 <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kategori Tersimpan</h4>
            </div>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-gray-900 text-white px-8 py-3.5 rounded-2xl font-bold hover:bg-black transition flex items-center shadow-lg group">
                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Registrasi Mapel
            </button>
        </div>
        
        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($categories as $cat): 
                $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE category_id = ?");
                $count_stmt->execute([$cat['id']]);
                $count = $count_stmt->fetchColumn();
            ?>
            <div class="p-8 rounded-[32px] border border-gray-50 bg-gray-50/50 flex justify-between items-center group hover:bg-white hover:border-primary hover:shadow-2xl hover:shadow-blue-50 transition-all duration-300">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="px-2 py-1 bg-blue-100 text-primary text-[9px] font-extrabold rounded-lg tracking-widest uppercase italic"><?php echo $cat['code']; ?></span>
                    </div>
                    <p class="font-bold text-gray-900 text-lg"><?php echo $cat['name']; ?></p>
                    <div class="flex items-center space-x-2 mt-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            <span class="text-primary"><?php echo $count; ?></span> Question Assets
                        </p>
                    </div>
                </div>
                <form action="" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus domain pengetahuan ini?')">
                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                    <button type="submit" name="delete_category" class="w-12 h-12 rounded-2xl bg-white text-gray-300 flex items-center justify-center opacity-0 group-hover:opacity-100 hover:text-red-500 hover:bg-red-50 transition-all duration-300 shadow-sm border border-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Right Column: Fixed/Academic Context -->
    <div class="space-y-8">
        <!-- Academic Context Dark Card -->
        <div class="bg-gray-900 rounded-[48px] p-10 text-white shadow-2xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-40 h-40 bg-blue-600 rounded-full blur-[80px] opacity-20"></div>
            <h4 class="text-[10px] font-bold text-blue-400 uppercase tracking-[0.2em] mb-8 relative z-10">Struktur Akademik (Fixed)</h4>
            
            <div class="mb-10 relative z-10">
                <p class="text-xs font-bold mb-5 italic text-gray-300">Tingkat Kelas Terdaftar</p>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($classes as $cls): ?>
                    <span class="px-5 py-2 bg-white bg-opacity-5 border border-white border-opacity-10 rounded-2xl text-xs font-bold text-white shadow-sm">
                        Kelas <?php echo $cls['name']; ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="relative z-10">
                <p class="text-xs font-bold mb-5 italic text-gray-300">Metrik Kesulitan (HOTS Standard)</p>
                <div class="space-y-5">
                    <div class="flex items-center justify-between p-4 bg-white bg-opacity-[0.03] rounded-2xl border border-white border-opacity-5">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-400">Mudah</span>
                        <span class="text-[9px] text-gray-500 font-bold uppercase">Basic Understanding</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-white bg-opacity-[0.03] rounded-2xl border border-white border-opacity-5">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-yellow-400">Sedang</span>
                        <span class="text-[9px] text-gray-500 font-bold uppercase">Standard Application</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-white bg-opacity-[0.03] rounded-2xl border border-white border-opacity-5">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-red-400">Sulit</span>
                        <span class="text-[9px] text-gray-500 font-bold uppercase">Analytical / HOTS</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-primary bg-opacity-5 border border-primary border-opacity-10 rounded-[40px] p-10 italic shadow-inner">
             <div class="flex items-center space-x-3 mb-4">
                 <div class="w-8 h-8 bg-primary rounded-xl flex items-center justify-center text-white">
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 </div>
                 <h5 class="text-xs font-bold text-gray-900 uppercase tracking-widest">Governance Info</h5>
             </div>
            <p class="text-xs text-gray-500 leading-relaxed">
                Standardisasi kategori memastikan setiap <span class="text-primary font-bold">Explicit Knowledge</span> terdokumentasi dengan benar sesuai kurikulum SMA Kristen Kalam Kudus Malang.
            </p>
        </div>
    </div>
</div>

<!-- Modal Mapel -->
<div id="addModal" class="hidden fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-[40px] max-w-sm w-full p-10 shadow-2xl relative overflow-hidden italic">
        <div class="absolute top-0 left-0 w-full h-2 bg-primary"></div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2 leading-none">Registrasi Domain</h3>
        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-10 italic">Mata Pelajaran / Bidang Studi Baru</p>
        
        <form action="" method="POST" class="space-y-6">
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Kode Mapel (Singkatan)</label>
                <input type="text" name="code" required placeholder="Misal: MTK-U" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner transition italic">
            </div>
            <div class="space-y-1">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Mata Pelajaran</label>
                <input type="text" name="name" required placeholder="Contoh: Matematika Umum" class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 shadow-inner transition italic">
            </div>
            <div class="flex space-x-3 pt-6">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 py-4 border border-gray-100 rounded-3xl font-bold text-gray-400 hover:bg-gray-100 transition">Batal</button>
                <button type="submit" name="add_category" class="flex-1 py-4 bg-primary text-white rounded-3xl font-bold hover:bg-gray-900 transition shadow-xl shadow-blue-100">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

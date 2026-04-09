<?php
// categories.php
$pageTitle = 'Knowledge Governance (Kategori)';
require_once 'includes/header.php';

// Check Role
checkRole(['Admin Akademik']);

$message = '';

// Handle Add/Delete Mapel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = $_POST['name'];
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        try {
            $stmt->execute([$name]);
            $message = 'Mata pelajaran berhasil ditambahkan.';
        } catch (PDOException $e) {
            $message = 'Error: Mapel sudah ada.';
        }
    } elseif (isset($_POST['delete_category'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Mata pelajaran berhasil dihapus.';
    }
}

// Fetch Mapel
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 italic">
    <!-- Mapel Management -->
    <div class="lg:col-span-2 bg-white rounded-[40px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-10 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 leading-tight">Domain Mata Pelajaran</h3>
                <p class="text-[10px] text-gray-400 mt-2 uppercase font-bold tracking-widest">Knowledge Domains - Pengelolaan Pusat Pengetahuan</p>
            </div>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-primary text-white px-8 py-3.5 rounded-2xl font-bold hover:bg-black transition flex items-center shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Mapel
            </button>
        </div>
        
        <?php if ($message): ?>
        <div class="m-10 bg-blue-50 text-blue-600 p-6 rounded-3xl text-sm italic border border-blue-100 italic">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($categories as $cat): 
                $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM documents WHERE category_id = ?");
                $count_stmt->execute([$cat['id']]);
                $count = $count_stmt->fetchColumn();
            ?>
            <div class="p-6 rounded-[32px] border border-gray-100 flex justify-between items-center group hover:border-primary hover:shadow-xl hover:shadow-blue-50 transition-all duration-300">
                <div>
                    <p class="font-bold text-gray-900 text-lg"><?php echo $cat['name']; ?></p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                        <span class="text-primary"><?php echo $count; ?></span> Soal Tersimpan
                    </p>
                </div>
                <form action="" method="POST" onsubmit="return confirm('Hapus mapel ini?')">
                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                    <button type="submit" name="delete_category" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center opacity-0 group-hover:opacity-100 hover:bg-red-500 hover:text-white transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Fixed Parameters (Class & Difficulty) -->
    <div class="space-y-8">
        <div class="bg-gray-900 rounded-3xl p-8 text-white">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">Parameter Akademik (Fixed)</h4>
            
            <div class="mb-8">
                <p class="text-sm font-bold mb-4 italic text-blue-400">Tingkat Kelas</p>
                <div class="flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-white bg-opacity-10 rounded-lg text-xs font-bold">Kelas X</span>
                    <span class="px-3 py-1 bg-white bg-opacity-10 rounded-lg text-xs font-bold">Kelas XI</span>
                    <span class="px-3 py-1 bg-white bg-opacity-10 rounded-lg text-xs font-bold">Kelas XII</span>
                </div>
            </div>

            <div>
                <p class="text-sm font-bold mb-4 italic text-blue-400">Tingkat Kesulitan</p>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest">
                        <span>Mudah</span>
                        <span class="text-teal-400">Basic</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest">
                        <span>Sedang</span>
                        <span class="text-yellow-400">Standard</span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest">
                        <span>Sulit</span>
                        <span class="text-red-400">Hots</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-100 rounded-3xl p-8 italic">
            <p class="text-xs text-blue-600 leading-relaxed font-medium text-center">
                Pengelolaan kategori dan parameter sistem memastikan standardisasi <span class="font-bold">Explicit Knowledge</span> yang berkualitas di SMA Kristen Kalam Kudus Malang.
            </p>
        </div>
    </div>
</div>

<!-- Modal Mapel -->
<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-8 shadow-2xl">
        <h3 class="text-xl font-bold text-gray-900 mb-6">Tambah Mata Pelajaran</h3>
        <form action="" method="POST" class="space-y-6">
            <input type="text" name="name" required placeholder="Nama Mapel" class="w-full px-4 py-3 border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex space-x-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 py-3 border border-gray-100 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition">Batal</button>
                <button type="submit" name="add_category" class="flex-1 py-3 bg-primary text-white rounded-xl font-bold hover:bg-blue-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
// bank-soal.php
$pageTitle = 'Bank Soal (Explicit Knowledge)';
require_once 'includes/header.php';

// Check Role
checkRole(['Guru', 'Admin Akademik', 'Kepala Sekolah', 'Administrator (TU)']);

// Pagination & Filters
$category_filter = $_GET['category'] ?? '';
$class_filter = $_GET['class'] ?? '';
$diff_filter = $_GET['difficulty'] ?? '';

$query = "SELECT d.*, c.name as category_name, u.full_name as uploader_name 
          FROM documents d 
          LEFT JOIN categories c ON d.category_id = c.id 
          LEFT JOIN users u ON d.uploader_id = u.id 
          WHERE 1=1";
$params = [];

if ($category_filter) {
    $query .= " AND d.category_id = ?";
    $params[] = $category_filter;
}
if ($class_filter) {
    $query .= " AND d.class_level = ?";
    $params[] = $class_filter;
}
if ($diff_filter) {
    $query .= " AND d.difficulty = ?";
    $params[] = $diff_filter;
}

$query .= " ORDER BY d.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$documents = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Manajemen Koleksi Dokumen Soal</h3>
        <p class="text-sm text-gray-500 mt-1">Daftar bank soal berupa file dokumen (Explicit Knowledge).</p>
    </div>
    <a href="tambah-soal.php" class="bg-primary text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-700 transition shadow-lg flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"></path></svg>
        Upload Soal Baru
    </a>
</div>

<!-- Filters -->
<form action="" method="GET" class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm mb-8 flex flex-wrap gap-4 items-end italic">
    <div class="flex-1 min-w-[200px]">
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mata Pelajaran</label>
        <select name="category" class="w-full px-4 py-2 bg-gray-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Mapel</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex-1 min-w-[120px]">
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kelas</label>
        <select name="class" class="w-full px-4 py-2 bg-gray-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua</option>
            <option value="X" <?php echo $class_filter == 'X' ? 'selected' : ''; ?>>X</option>
            <option value="XI" <?php echo $class_filter == 'XI' ? 'selected' : ''; ?>>XI</option>
            <option value="XII" <?php echo $class_filter == 'XII' ? 'selected' : ''; ?>>XII</option>
        </select>
    </div>
    <div class="flex-1 min-w-[120px]">
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Kesulitan</label>
        <select name="difficulty" class="w-full px-4 py-2 bg-gray-50 border-none rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Semua</option>
            <option value="Mudah" <?php echo $diff_filter == 'Mudah' ? 'selected' : ''; ?>>Mudah</option>
            <option value="Sedang" <?php echo $diff_filter == 'Sedang' ? 'selected' : ''; ?>>Sedang</option>
            <option value="Sulit" <?php echo $diff_filter == 'Sulit' ? 'selected' : ''; ?>>Sulit</option>
        </select>
    </div>
    <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-black transition">Filter</button>
</form>

<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-5">Judul & Mapel</th>
                    <th class="px-8 py-5">Kelas</th>
                    <th class="px-8 py-5">Kesulitan</th>
                    <th class="px-8 py-5">Pembuat</th>
                    <th class="px-8 py-5">Status</th>
                    <th class="px-8 py-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($documents)): ?>
                <tr>
                    <td colspan="6" class="px-8 py-10 text-center text-gray-400 italic">Belum ada koleksi soal yang tersedia.</td>
                </tr>
                <?php endif; ?>
                <?php foreach ($documents as $doc): ?>
                <tr class="hover:bg-gray-50 transition italic">
                    <td class="px-8 py-5">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-50 text-primary rounded-lg mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900 leading-tight"><?php echo $doc['title']; ?></p>
                                <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-widest"><?php echo $doc['category_name']; ?> • <?php echo $doc['materi']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-5 font-bold text-gray-600">Kelas <?php echo $doc['class_level']; ?></td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 bg-gray-100 text-[10px] font-bold rounded-full uppercase tracking-tighter <?php echo $doc['difficulty'] == 'Sulit' ? 'text-red-500' : ($doc['difficulty'] == 'Sedang' ? 'text-orange-500' : 'text-green-500'); ?>">
                            <?php echo $doc['difficulty']; ?>
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-xs font-semibold text-gray-700"><?php echo $doc['uploader_name']; ?></span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest <?php 
                            echo $doc['status'] == 'Verified' ? 'bg-teal-50 text-teal-600' : ($doc['status'] == 'Review' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-100 text-gray-400'); 
                        ?>">
                            <?php echo $doc['status']; ?>
                        </span>
                    </td>
                    <td class="px-8 py-5 text-center flex items-center justify-center space-x-3">
                        <a href="view-soal.php?id=<?php echo $doc['id']; ?>" class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition" title="Lihat Detail & Pembahasan">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </a>
                        <a href="storage/documents/<?php echo $doc['file_path']; ?>" download="<?php echo $doc['original_name']; ?>" class="p-2 text-green-500 hover:bg-green-50 rounded-lg transition" title="Unduh File">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<?php
// bank-soal.php
$pageTitle = 'Bank Soal (Explicit Knowledge)';
require_once 'includes/header.php';

// Check Role: Guru, Admin Akademik, Kepsek, and Superadmin (Viewer)
checkRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK, ROLE_KEPSEK, ROLE_ADMIN_SISTEM]);

// Pagination & Filters
$category_filter = $_GET['category'] ?? '';
$class_filter = $_GET['class'] ?? '';
$diff_filter = $_GET['difficulty'] ?? '';

$query = "SELECT q.*, c.name as category_name, c.code as category_code, cl.name as class_name,
          COALESCE(
              (SELECT full_name FROM staff WHERE identity_id = q.uploader_id),
              (SELECT full_name FROM teachers WHERE identity_id = q.uploader_id)
          ) as uploader_name 
          FROM questions q
          LEFT JOIN categories c ON q.category_id = c.id 
          LEFT JOIN classes cl ON q.class_id = cl.id
          WHERE 1=1";
$params = [];

if ($category_filter) {
    $query .= " AND q.category_id = ?";
    $params[] = $category_filter;
}
if ($class_filter) {
    $query .= " AND q.class_id = ?";
    $params[] = $class_filter;
}
if ($diff_filter) {
    $query .= " AND q.difficulty = ?";
    $params[] = $diff_filter;
}

$query .= " ORDER BY q.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$questions = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$classes = $pdo->query("SELECT * FROM classes ORDER BY name ASC")->fetchAll();
?>

<div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
    <div>
        <h3 class="text-3xl font-bold text-gray-900 leading-none">Koleksi Bank Soal</h3>
        <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold mt-3 italic">SMA Kristen Kalam Kudus Malang • Knowledge Base</p>
    </div>
    <?php if (hasRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK])): ?>
    <a href="tambah-soal.php" class="bg-primary text-white px-8 py-4 rounded-[20px] font-bold hover:bg-black transition shadow-xl shadow-blue-100 flex items-center group">
        <svg class="w-5 h-5 mr-3 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Soal Baru
    </a>
    <?php endif; ?>
</div>

<!-- Advanced Filters -->
<form action="" method="GET" class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm mb-12 flex flex-wrap gap-6 items-end italic">
    <div class="flex-1 min-w-[200px]">
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 pr-2">Mata Pelajaran</label>
        <select name="category" class="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl text-sm outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
            <option value="">Semua Mapel</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex-1 min-w-[150px]">
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Kelas / Level</label>
        <select name="class" class="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl text-sm outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
            <option value="">Semua Kelas</option>
            <?php foreach ($classes as $cls): ?>
                <option value="<?php echo $cls['id']; ?>" <?php echo $class_filter == $cls['id'] ? 'selected' : ''; ?>>Kelas <?php echo $cls['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex-1 min-w-[150px]">
        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Tingkat Kesulitan</label>
        <select name="difficulty" class="w-full px-6 py-3.5 bg-gray-50 border-none rounded-2xl text-sm outline-none focus:ring-2 focus:ring-blue-500 transition shadow-inner">
            <option value="">Semua Level</option>
            <option value="Mudah" <?php echo $diff_filter == 'Mudah' ? 'selected' : ''; ?>>Mudah</option>
            <option value="Sedang" <?php echo $diff_filter == 'Sedang' ? 'selected' : ''; ?>>Sedang</option>
            <option value="Sulit" <?php echo $diff_filter == 'Sulit' ? 'selected' : ''; ?>>Sulit</option>
        </select>
    </div>
    <button type="submit" class="px-10 py-3.5 bg-gray-900 text-white rounded-2xl text-sm font-bold hover:bg-black transition shadow-lg shrink-0">Terapkan Filter</button>
</form>

<div class="bg-white rounded-[40px] border border-gray-100 shadow-xl overflow-hidden mb-12">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-[0.1em]">
                    <th class="px-10 py-6">Detail Soal & Topik</th>
                    <th class="px-8 py-6">Akademik</th>
                    <th class="px-8 py-6">Penulis</th>
                    <th class="px-8 py-6">Status Validasi</th>
                    <th class="px-10 py-6 text-center">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php if (empty($questions)): ?>
                <tr>
                    <td colspan="5" class="px-10 py-24 text-center">
                        <div class="max-w-xs mx-auto">
                            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p class="text-gray-400 italic">Belum ada bank soal yang sesuai dengan kriteria anda.</p>
                        </div>
                    </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($questions as $q): ?>
                <tr class="hover:bg-gray-50 cursor-pointer group transition-all duration-200 italic" onclick="window.location.href='view-soal.php?id=<?php echo $q['id']; ?>'">
                    <td class="px-10 py-7">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-50 text-primary rounded-2xl mr-4 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-base font-bold text-gray-900 leading-tight mb-1"><?php echo $q['title']; ?></p>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest"><span class="text-primary font-black">[<?php echo $q['category_code']; ?>]</span> <?php echo $q['category_name']; ?> • <?php echo $q['materi']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-7">
                        <div class="space-y-1.5">
                            <p class="text-xs font-bold text-gray-700">Kelas <?php echo $q['class_name']; ?></p>
                            <span class="inline-block px-2 py-0.5 bg-gray-100 text-[9px] font-bold rounded-md uppercase tracking-tighter <?php echo $q['difficulty'] == 'Sulit' ? 'border border-red-100 text-red-500' : ($q['difficulty'] == 'Sedang' ? 'border border-orange-100 text-orange-500' : 'border border-green-100 text-green-500'); ?>">
                                <?php echo $q['difficulty']; ?>
                            </span>
                        </div>
                    </td>
                    <td class="px-8 py-7">
                        <div class="flex items-center text-xs font-semibold text-gray-700">
                            <div class="w-6 h-6 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-[10px] mr-2"><?php echo strtoupper(substr($q['uploader_name'], 0, 1)); ?></div>
                            <?php echo $q['uploader_name']; ?>
                        </div>
                    </td>
                    <td class="px-8 py-7">
                        <div class="flex items-center space-x-2">
                             <div class="w-2 h-2 rounded-full <?php 
                                echo $q['status'] == STATUS_VERIFIED ? 'bg-green-500' : ($q['status'] == STATUS_REVIEW ? 'bg-yellow-500' : 'bg-gray-300'); 
                            ?>"></div>
                            <span class="text-[10px] font-bold uppercase tracking-widest <?php 
                                echo $q['status'] == STATUS_VERIFIED ? 'text-green-600' : ($q['status'] == STATUS_REVIEW ? 'text-yellow-600' : 'text-gray-400'); 
                            ?>">
                                <?php echo $q['status']; ?>
                            </span>
                        </div>
                    </td>
                    <td class="px-10 py-7 text-center">
                        <div class="flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <a href="view-soal.php?id=<?php echo $q['id']; ?>" class="p-3 bg-white text-primary border border-gray-100 rounded-xl hover:bg-primary hover:text-white transition shadow-sm" title="Buka Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            <?php if ($q['file_path']): ?>
                            <a href="storage/documents/<?php echo $q['file_path']; ?>" download="<?php echo $q['original_name']; ?>" class="p-3 bg-white text-teal-500 border border-gray-100 rounded-xl hover:bg-teal-500 hover:text-white transition shadow-sm" title="Download File">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

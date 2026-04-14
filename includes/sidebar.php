<?php
// includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role_name'];
?>
<aside class="w-80 bg-white border-r border-gray-100 flex flex-col sticky top-0 h-screen shadow-sm italic">
    <div class="h-24 flex items-center px-10 border-b border-gray-100">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-primary rounded-[18px] flex items-center justify-center shadow-lg shadow-blue-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight leading-none">KMS Soal</h1>
                <p class="text-[10px] text-gray-400 uppercase tracking-[0.2em] font-bold mt-1">SMA KK Malang</p>
            </div>
        </div>
    </div>

    <div class="flex-1 py-8 space-y-2 overflow-y-auto">
        <div class="px-10 mb-4">
            <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest leading-none">Main Hub</p>
        </div>
        
        <a href="index.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'index.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 12 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="text-sm font-bold">Dashboard Statistik</span>
        </a>

        <?php if (hasRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK, ROLE_KEPSEK, ROLE_ADMIN_SISTEM])): ?>
        <a href="bank-soal.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'bank-soal.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="text-sm font-bold">Koleksi Bank Soal</span>
        </a>
        <?php endif; ?>

        <?php if (hasRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK])): ?>
        <a href="tambah-soal.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'tambah-soal.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"></path></svg>
            <span class="text-sm font-bold">Tambah Soal Baru</span>
        </a>
        <?php endif; ?>

        <?php if (hasRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK])): ?>
        <a href="forum.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'forum.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
            <span class="text-sm font-bold">Diskusi Forum</span>
        </a>
        <?php endif; ?>

        <?php if (hasRoleId([ROLE_ADMIN_AKADEMIK])): ?>
        <a href="categories.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'categories.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            <span class="text-sm font-bold">Kategori Mapel</span>
        </a>
        <a href="teachers.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'teachers.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="text-sm font-bold">Data Personel Guru</span>
        </a>
        <?php endif; ?>

        <div class="px-10 mt-10 mb-4 border-t border-gray-50 pt-8">
            <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest leading-none">Management & Reports</p>
        </div>

        <?php if (hasRoleId([ROLE_ADMIN_AKADEMIK, ROLE_KEPSEK])): ?>
        <a href="reports.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'reports.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span class="text-sm font-bold">Laporan Statistik</span>
        </a>
        <?php endif; ?>

        <?php if (hasRoleId([ROLE_ADMIN_SISTEM])): ?>
        <a href="users.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'users.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="text-sm font-bold">Manajemen User</span>
        </a>
        <?php endif; ?>

        <a href="templates.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'templates.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            <span class="text-sm font-bold">Template & SOP</span>
        </a>
        
        <a href="profile.php" class="flex items-center px-10 py-3.5 space-x-3 <?php echo $current_page == 'profile.php' ? 'sidebar-item-active' : 'text-gray-500 hover:bg-gray-50'; ?> transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span class="text-sm font-bold">Profil Akun</span>
        </a>
    </div>

    <div class="p-8">
        <a href="logout.php" class="flex items-center justify-center px-4 py-3.5 bg-red-50 text-red-600 rounded-[20px] space-x-3 hover:bg-red-100 transition duration-200 shadow-sm border border-red-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span class="text-xs font-bold uppercase tracking-widest">Logout</span>
        </a>
    </div>
</aside>

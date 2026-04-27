<?php
// includes/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role_name'];
?>
<aside class="w-80 bg-white border-r border-gray-100 flex flex-col sticky top-0 h-screen shadow-sm">
    <div class="p-10 flex flex-col items-center border-b border-gray-50">
        <div class="relative mb-6">
            <div class="w-24 h-24 bg-white rounded-3xl flex items-center justify-center shadow-2xl shadow-blue-100 border border-gray-50 p-4">
                <img src="upload/logo/Logo.png" alt="Logo Kalam Kudus" class="w-full h-auto object-contain">
            </div>
            <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-red-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="text-center">
            <h1 class="text-xl font-black text-[#003366] tracking-tighter leading-none">KMS BANK SOAL</h1>
            <p class="text-[9px] text-gray-400 uppercase tracking-[0.3em] font-bold mt-2">SMA KRISTEN KALAM KUDUS</p>
        </div>
    </div>

    <div class="flex-1 py-8 space-y-1 overflow-y-auto px-4">
        <p class="px-6 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mb-4">Navigasi Utama</p>
        
        <a href="index.php" class="flex items-center px-6 py-3.5 space-x-3 rounded-2xl transition-all duration-300 <?php echo $current_page == 'index.php' ? 'bg-[#003366] text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-gray-50 hover:pl-8'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span class="text-sm font-bold">Beranda</span>
        </a>

        <?php if (hasRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK, ROLE_KEPSEK, ROLE_ADMIN_SISTEM])): ?>
        <a href="bank-soal.php" class="flex items-center px-6 py-3.5 space-x-3 rounded-2xl transition-all duration-300 relative <?php echo $current_page == 'bank-soal.php' ? 'bg-[#003366] text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-gray-50 hover:pl-8'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <span class="text-sm font-bold">Koleksi Bank Soal</span>
            <?php 
            if (hasRoleId([ROLE_ADMIN_AKADEMIK])) {
                $qCount = $pdo->query("SELECT COUNT(*) FROM questions WHERE status = 'Review'")->fetchColumn();
                if ($qCount > 0) echo '<span class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 bg-red-600 text-white text-[10px] flex items-center justify-center rounded-lg font-bold">'.$qCount.'</span>';
            }
            ?>
        </a>
        <?php endif; ?>

        <?php if (hasRoleId([ROLE_GURU, ROLE_ADMIN_AKADEMIK])): ?>
        <a href="tambah-soal.php" class="flex items-center px-6 py-3.5 space-x-3 rounded-2xl transition-all duration-300 <?php echo $current_page == 'tambah-soal.php' ? 'bg-[#003366] text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-gray-50 hover:pl-8'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-sm font-bold">Tambah Soal</span>
        </a>
        <?php endif; ?>

        <p class="px-6 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mt-8 mb-4">Pusat Pengetahuan</p>

        <a href="forum.php" class="flex items-center px-6 py-3.5 space-x-3 rounded-2xl transition-all duration-300 <?php echo $current_page == 'forum.php' ? 'bg-[#003366] text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-gray-50 hover:pl-8'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            <span class="text-sm font-bold">Forum Akademik</span>
        </a>

        <a href="templates.php" class="flex items-center px-6 py-3.5 space-x-3 rounded-2xl transition-all duration-300 <?php echo $current_page == 'templates.php' ? 'bg-[#003366] text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-gray-50 hover:pl-8'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <span class="text-sm font-bold">Template & SOP</span>
        </a>

        <?php if (hasRoleId([ROLE_ADMIN_AKADEMIK, ROLE_ADMIN_SISTEM])): ?>
        <p class="px-6 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] mt-8 mb-4">Administrasi</p>
        <a href="reports.php" class="flex items-center px-6 py-3.5 space-x-3 rounded-2xl transition-all duration-300 <?php echo $current_page == 'reports.php' ? 'bg-[#003366] text-white shadow-lg shadow-blue-100' : 'text-gray-500 hover:bg-gray-50 hover:pl-8'; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            <span class="text-sm font-bold">Laporan Performa</span>
        </a>
        <?php endif; ?>
    </div>

    <div class="p-6 border-t border-gray-50">
        <a href="logout.php" class="flex items-center justify-center px-6 py-4 bg-red-50 text-red-600 rounded-2xl space-x-3 hover:bg-red-600 hover:text-white transition-all duration-300 font-bold text-xs uppercase tracking-widest shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            <span>Keluar Sistem</span>
        </a>
    </div>
</aside>

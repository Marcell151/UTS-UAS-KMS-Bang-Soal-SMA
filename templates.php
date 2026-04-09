<?php
// templates.php
$pageTitle = 'Template & SOP';
require_once 'includes/header.php';

// Check Role
checkRole(['Guru', 'Admin Akademik', 'Kepala Sekolah', 'Administrator (TU)']);
?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Templates Section -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
        <div class="flex items-center space-x-4 mb-8">
            <div class="p-3 bg-blue-50 text-primary rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900">Template Dokumen Soal</h3>
                <p class="text-sm text-gray-500">Unduh format baku untuk pembuatan soal.</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Template Soal Pilihan Ganda</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">DOCX • 124 KB</p>
                    </div>
                </div>
                <a href="#" class="text-primary hover:text-blue-700 font-bold text-xs px-4 py-2 bg-blue-50 rounded-lg transition">Unduh</a>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Template Soal Essay</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">PDF • 89 KB</p>
                    </div>
                </div>
                <a href="#" class="text-primary hover:text-blue-700 font-bold text-xs px-4 py-2 bg-blue-50 rounded-lg transition">Unduh</a>
            </div>
        </div>
    </div>

    <!-- SOP Section -->
    <div class="bg-primary rounded-3xl p-8 text-white relative overflow-hidden">
        <div class="relative z-10">
            <div class="flex items-center space-x-4 mb-8">
                <div class="p-3 bg-white bg-opacity-10 rounded-2xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold">SOP Pembuatan Soal</h3>
                    <p class="text-blue-100 text-sm">Standar Operasional Prosedur Akademik.</p>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-start space-x-3">
                    <span class="w-6 h-6 flex-shrink-0 bg-white text-primary rounded-full flex items-center justify-center font-bold text-[10px]">1</span>
                    <p class="text-sm">Gunakan template resmi yang tersedia untuk keseragaman format.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <span class="w-6 h-6 flex-shrink-0 bg-white text-primary rounded-full flex items-center justify-center font-bold text-[10px]">2</span>
                    <p class="text-sm">Soal harus melalui tahap Peer Review (Diskusi) minimal oleh satu rekan guru sejawat.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <span class="w-6 h-6 flex-shrink-0 bg-white text-primary rounded-full flex items-center justify-center font-bold text-[10px]">3</span>
                    <p class="text-sm">Setelah direview, ajukan status ke "Review" untuk divalidasi oleh Administrator/Kepala Sekolah.</p>
                </div>
                <div class="flex items-start space-x-3">
                    <span class="w-6 h-6 flex-shrink-0 bg-white text-primary rounded-full flex items-center justify-center font-bold text-[10px]">4</span>
                    <p class="text-sm">Hanya soal dengan status "Verified" yang diizinkan untuk dicetak/digunakan.</p>
                </div>
            </div>
        </div>
        <!-- Decorative SVG -->
        <svg class="absolute bottom-0 right-0 w-48 h-48 text-white opacity-5 -mb-12 -mr-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

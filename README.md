# KMS Bank Soal - SMA Kristen Kalam Kudus Malang
Knowledge Management System (KMS) Bank Soal adalah aplikasi berbasis web yang dirancang khusus untuk memfasilitasi siklus manajemen pengetahuan (*Knowledge Management*) di lingkungan sekolah. Sistem ini mentransformasi proses penyusunan soal ujian yang awalnya terfragmentasi dan individual menjadi sebuah kekayaan intelektual organisasi yang terpusat, tervalidasi, dan kolaboratif.

Studi kasus pengembangan sistem ini dilakukan pada **SMA Kristen Kalam Kudus Malang**.

## Fitur Utama (Core Features)

Sistem ini memfasilitasi 4 pilar Manajemen Pengetahuan: *Knowledge Capture, Storage, Sharing,* dan *Application*.
*   **Strict Visibility Workflow:** Alur kerja dokumen yang ketat (*Draft* -> *Review* -> *Verified*). Soal yang belum divalidasi tidak akan bocor ke repositori publik.
*   **Explicit & Tacit Knowledge Integration:** Menangkap dokumen formal (file soal/SOP) dan mendokumentasikan wawasan abstrak guru melalui kewajiban penulisan "Pembahasan Soal" serta interaksi forum.
*   **Forum Akademik & Inline Edit:** Ruang diskusi antarguru (*Tacit Sharing*) yang mendukung perubahan komentar tanpa *reload* (*Seamless UX*).
*   **Smart Knowledge Retrieval:** Mesin pencari dan filter tingkat lanjut, dilengkapi algoritma rekomendasi "Pengetahuan/Soal Serupa" berdasarkan metadata kelas dan mata pelajaran.
*   **Analytics Dashboard:** Panel performa *real-time* untuk pimpinan guna melacak *Knowledge Gap* (kekurangan aset soal) dan kontribusi masing-masing pengajar.

## Role-Based Access Control (RBAC)

Aplikasi ini mendistribusikan wewenang secara spesifik kepada 4 aktor utama:
1.  **Guru (Knowledge Contributor):** Mengunggah soal, menulis pembahasan, dan berpartisipasi di forum diskusi. Masuk menggunakan PIN dan NIP.
2.  **Admin Akademik (Gatekeeper):** Memverifikasi draf soal. Dapat mengembalikan dokumen (*Reject*) dengan kewajiban memberikan "Catatan Revisi", atau mengesahkan soal menjadi *Verified*.
3.  **Kepala Sekolah (Decision Maker):** Memiliki akses *Read-Only* ke *Dashboard* pelaporan dan statistik performa guru.
4.  **Admin Sistem (Superadmin):** Mengelola master data (Mata Pelajaran, Kelas, Data Pengguna) dan memantau log keamanan sistem (*Audit Log*).

## Teknologi yang Digunakan

*   **Backend:** PHP Native (Arsitektur Modular/Prosedural).
*   **Database:** MySQL (Koneksi aman menggunakan ekstensi PDO dan *Prepared Statements* untuk mencegah *SQL Injection*).
*   **Frontend:** HTML5, Vanilla JavaScript, dan CSS dengan pendekatan utilitas (*Tailwind-like classes*).
*   **UI/UX Design:** Mengadopsi estetika *Bento-Grid Layout* dan efek *Glassmorphism* (blur, soft shadows, dark mode accents).
*   **Rich Text Editor:** Terintegrasi dengan editor (seperti CKEditor/TinyMCE) untuk penulisan penjelasan instrumen akademik yang rapi.

## Panduan Instalasi (Local Development)

Ikuti langkah-langkah berikut untuk menjalankan sistem ini di komputer lokal Anda (menggunakan XAMPP/MAMP/Laragon):

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/Marcell151/UTS-UAS-KMS-Bang-Soal-SMA.git](https://github.com/Marcell151/UTS-UAS-KMS-Bang-Soal-SMA.git)
    ```
2.  **Pindahkan Direktori**
    Pindahkan folder hasil *clone* ke dalam direktori server lokal Anda (contoh: `C:\xampp\htdocs\kms-bank-soal`).
3.  **Siapkan Database**
    *   Buka phpMyAdmin (biasanya di `http://localhost/phpmyadmin`).
    *   Buat database baru dengan nama `kms_bsoal`.
    *   Import file `database/kms_bsoal.sql` yang tersedia di dalam repositori ini.
4.  **Konfigurasi Koneksi**
    Buka file `config/database.php` (atau file koneksi terkait) dan sesuaikan kredensialnya:
    ```php
    $host = 'localhost';
    $dbname = 'kms_bsoal';
    $username = 'root';
    $password = ''; // Kosongkan jika default XAMPP
    ```
5.  **Jalankan Aplikasi**
    Buka *browser* dan akses: `http://localhost/kms-bank-soal` (atau sesuaikan dengan nama folder Anda).
    *Gunakan kredensial pengujian yang terdapat di dalam file `dummy_accounts.txt` untuk mencoba berbagai peran (Guru/Admin).*

## Direktori Upload (Penting)

Sistem menggunakan pengelolaan file terpusat. Pastikan folder `upload/` di dalam direktori *root* memiliki *permission* yang memadai untuk operasi tulis/baca (Write/Read) agar file PDF, Word, dan gambar dapat disimpan melalui fungsi `move_uploaded_file()`.

## Pengembang

Dikembangkan sebagai pemenuhan Proyek Sistem Manajemen Pengetahuan:
*   **Marcell Chandra Kenchana** 
*   **William Christopher Linardi**
*   *Universitas Ma Chung - Fakultas Teknologi dan Desain - Program Studi Sistem Informasi*

<?php
// alter.php
require_once 'config/database.php';

try {
    // Menambahkan kolom jenis_soal
    $pdo->exec("ALTER TABLE questions ADD COLUMN jenis_soal ENUM('Pilihan Ganda', 'Essay', 'Isian Singkat', 'Praktikum', 'Lainnya') DEFAULT 'Pilihan Ganda' AFTER tingkat_kognitif;");
    
    echo "<h2 style='color: green;'>Sukses! Kolom 'jenis_soal' berhasil ditambahkan ke tabel 'questions'.</h2>";
    echo "<p>Silakan kembali ke <a href='bank-soal.php'>Bank Soal</a>.</p>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Error Database:</h2>";
    echo "<p>Gagal meng-alter tabel atau kolom sudah ada. Pesan: " . $e->getMessage() . "</p>";
}
?>

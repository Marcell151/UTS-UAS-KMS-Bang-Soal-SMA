<?php
// alter.php
require_once 'config/database.php';

try {
    // Menambahkan kolom jenis_soal
    $pdo->exec("ALTER TABLE teachers ADD COLUMN is_archived TINYINT(1) DEFAULT 0 AFTER pin;");
    
    echo "<h2 style='color: green;'>Sukses! Kolom 'is_archived' berhasil ditambahkan ke tabel 'teachers'.</h2>";
    echo "<p>Silakan kembali ke <a href='teachers.php'>Master Guru</a>.</p>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Error Database:</h2>";
    echo "<p>Gagal meng-alter tabel atau kolom sudah ada. Pesan: " . $e->getMessage() . "</p>";
}
?>

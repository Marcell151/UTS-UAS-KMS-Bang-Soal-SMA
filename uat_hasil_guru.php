<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil UAT - KMS Bank Soal (Dina Sisilia)</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f4f7f6; 
            padding: 40px; 
            color: #333; 
        }
        .container { 
            max-width: 1000px; 
            margin: auto; 
            background: white; 
            padding: 40px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        h1 { 
            color: #000080; /* Navy Blue */
            border-bottom: 2px solid #3498db; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
            text-align: center; 
        }
        .summary-box { 
            background: #fcfcfc; 
            border: 1px solid #eee; 
            padding: 20px; 
            border-radius: 5px; 
            margin-bottom: 30px; 
        }
        .form-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 20px; 
            font-size: 0.95em; 
            margin-bottom: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0; /* Menandakan field readonly/terisi */
            color: #333;
        }
        .category-header { 
            background-color: #e8f4fd; 
            color: #000080; 
            padding: 12px 15px; 
            border-radius: 4px; 
            margin-top: 35px; 
            margin-bottom: 15px; 
            font-weight: bold; 
            display: flex; 
            align-items: center; 
            border-left: 5px solid #3498db; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
            background: white; 
        }
        th, td { 
            border: 1px solid #e0e0e0; 
            padding: 12px 15px; 
            text-align: left; 
            font-size: 0.9em; 
        }
        th { 
            background-color: #f8f9fa; 
            color: #555; 
            text-align: center;
        }
        .th-pertanyaan {
            text-align: left;
        }
        .no-col { 
            width: 40px; 
            text-align: center; 
            font-weight: bold;
        }
        .radio-col { 
            width: 60px; 
            text-align: center; 
        }
        .radio-col input[type="radio"] {
            transform: scale(1.3);
            cursor: pointer;
        }
        .radio-selected {
            background-color: #d4edda; /* Hijau muda untuk jawaban terpilih */
        }
        .legend-box {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.85em;
            text-align: center;
        }
        .legend-item {
            display: inline-block;
            margin: 0 10px;
        }
        .legend-item span {
            font-weight: bold;
            color: #000080;
        }
        .total-score {
            margin-top: 30px;
            padding: 20px;
            background-color: #e8f4fd;
            border: 1px solid #b8daff;
            border-radius: 5px;
            text-align: center;
            font-size: 1.2em;
            color: #004085;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Dokumen Hasil UAT - KMS Bank Soal SMA</h1>
    
    <div class="summary-box">
        <div style="font-size: 1.2em; font-weight: bold; margin-bottom: 15px; color: #2c3e50;">Identitas Responden UAT (Terisi)</div>
        <div class="form-grid">
            <div class="form-group">
                <label>Nama Penguji</label>
                <input type="text" class="form-control" value="Dina Sisilia, S.Pd" readonly>
            </div>
            <div class="form-group">
                <label>Role / Jabatan Pengujian</label>
                <input type="text" class="form-control" value="Guru Mata Pelajaran" readonly>
            </div>
        </div>
        <p style="font-size: 0.85em; color: #666; margin-top: 10px; margin-bottom: 0;">
            Status Dokumen: Telah diisi secara faktual dan objektif berdasarkan evaluasi terhadap fitur Knowledge Management System Bank Soal pada iterasi pengujian terakhir.
        </p>
    </div>

    <div class="legend-box">
        <strong style="display: block; margin-bottom: 10px; color: #555;">Keterangan Skala Penilaian:</strong>
        <div class="legend-item"><span>1</span> = Sangat Tidak Setuju</div>
        <div class="legend-item"><span>2</span> = Tidak Setuju</div>
        <div class="legend-item"><span>3</span> = Netral</div>
        <div class="legend-item"><span>4</span> = Setuju</div>
        <div class="legend-item"><span>5</span> = Sangat Setuju</div>
    </div>

    <?php
    $categories = [
        "1. Kemudahan Penggunaan & Antarmuka (Usability & UI)" => [
            "Navigasi antarmuka sistem secara keseluruhan mudah dipahami dan digunakan." => 5,
            "Pemisahan jalur login antara Staf/Pimpinan dan Guru mempermudah akses masuk." => 5,
            "Desain visual dan paduan warna sistem nyaman di mata untuk penggunaan durasi panjang." => 4,
            "Tata letak informasi pada setiap halaman disajikan dengan rapi dan tidak membingungkan." => 5,
            "Sistem dapat diakses dan merespons dengan cepat tanpa waktu muat (loading) yang lama." => 5
        ],
        "2. Penangkapan & Penyimpanan Pengetahuan (Knowledge Capture & Storage)" => [
            "Formulir \"Tambah Soal Baru\" terstruktur dengan baik dan mudah diisi." => 5,
            "Kolom metadata pendukung (seperti Tingkat Kognitif, Kesulitan, dan Jenis Soal) relevan dengan standar akademik." => 5,
            "Fasilitas pengunggahan dokumen lampiran (PDF/Word) berjalan lancar dan mudah digunakan." => 5,
            "Ruang untuk mengetikkan \"Pembahasan Soal\" berfungsi dengan baik untuk merekam proses berpikir." => 4,
            "Pusat Pengetahuan Eksplisit memudahkan pengunduhan template dan SOP sekolah secara instan." => 5
        ],
        "3. Alur Validasi Pengetahuan (Knowledge Workflow)" => [
            "Alur perubahan status soal dari Draft, Review, hingga Verified sangat logis dan terstruktur." => 5,
            "Label indikator status pada setiap dokumen soal terlihat sangat jelas dan informatif." => 5,
            "Kewajiban memberikan \"Catatan Revisi\" bagi Admin saat menolak soal sangat membantu guru dalam melakukan perbaikan." => 5,
            "Dokumen yang masih berstatus Draft atau Review terjaga privasinya dan tidak bocor ke ruang publik." => 5,
            "Notifikasi perubahan status soal memberikan informasi yang tepat waktu dan akurat." => 4
        ],
        "4. Pencarian & Penggunaan Ulang (Knowledge Retrieval & Reuse)" => [
            "Kolom pencarian kata kunci mampu menemukan judul atau topik soal dengan akurat." => 5,
            "Fungsi filter (berdasarkan Mata Pelajaran, Kelas, dan Level) sangat membantu dalam mempersempit hasil pencarian." => 5,
            "Fitur rekomendasi \"Soal Serupa\" di bagian bawah detail soal sangat relevan dan berguna." => 5,
            "Antarmuka untuk melihat detail dokumen dan pembahasan soal tertata dengan sangat jelas." => 5,
            "Sistem secara keseluruhan berhasil mempercepat proses pencarian referensi soal lama." => 5
        ],
        "5. Kolaborasi & Pengetahuan Tasit (Knowledge Sharing)" => [
            "Fitur pembuatan topik baru di Forum Akademik sangat mudah digunakan." => 5,
            "Klasifikasi forum berdasarkan kategori (Rumpun Sains, Sosial, dll.) membuat diskusi lebih terarah." => 5,
            "Fitur Inline Edit pada komentar mempermudah perbaikan pesan tanpa harus memuat ulang halaman." => 5,
            "Kolom komentar spesifik pada setiap butir soal memfasilitasi evaluasi instrumen akademik secara efektif." => 5,
            "Antarmuka ruang diskusi dirancang dengan baik sehingga interaksi antarguru mudah dibaca dan diikuti." => 4
        ],
        "6. Pelaporan & Analitik (Dashboard & Monitoring)" => [
            "Ringkasan total aset pengetahuan di Dashboard disajikan dengan angka yang akurat." => 5,
            "Daftar Antrean Review memudahkan Admin Akademik dalam memantau tugas validasi." => 5,
            "Papan Peringkat (Top Kontributor) dapat memotivasi guru untuk lebih aktif berbagi pengetahuan." => 4,
            "Grafik \"Distribusi Pengetahuan per Mata Pelajaran\" sangat membantu Kepala Sekolah mendeteksi kesenjangan aset (Knowledge Gap)." => 5,
            "Dashboard secara keseluruhan menyajikan informasi yang komprehensif untuk mendukung pengambilan keputusan." => 5
        ]
    ];

    $q_num = 1;
    $total_score = 0;
    
    foreach ($categories as $catTitle => $questions) {
        echo '<div class="category-header">' . $catTitle . '</div>';
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="no-col">No</th>';
        echo '<th class="th-pertanyaan">Pertanyaan Pengujian</th>';
        echo '<th class="radio-col">1</th>';
        echo '<th class="radio-col">2</th>';
        echo '<th class="radio-col">3</th>';
        echo '<th class="radio-col">4</th>';
        echo '<th class="radio-col">5</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($questions as $q => $score) {
            $total_score += $score;
            echo '<tr>';
            echo '<td class="no-col">' . $q_num . '</td>';
            echo '<td>' . $q . '</td>';
            
            for ($i = 1; $i <= 5; $i++) {
                $isChecked = ($i == $score) ? 'checked' : 'disabled';
                $bgClass = ($i == $score) ? 'radio-selected' : '';
                echo '<td class="radio-col ' . $bgClass . '"><input type="radio" ' . $isChecked . '></td>';
            }
            
            echo '</tr>';
            $q_num++;
        }
        
        echo '</tbody>';
        echo '</table>';
    }
    
    $max_score = 150; // 30 questions * 5
    $percentage = round(($total_score / $max_score) * 100, 2);
    ?>

    <div class="total-score">
        Total Skor Evaluasi UAT: <?php echo $total_score; ?> / 150 (<?php echo $percentage; ?>% - Sangat Memuaskan)
        <p style="font-size: 0.7em; color: #666; font-weight: normal; margin-top: 10px;">Sistem ini dinilai sangat layak pakai (Acceptable) dan memenuhi seluruh fungsionalitas manajemen pengetahuan dengan baik.</p>
    </div>

</div>

</body>
</html>

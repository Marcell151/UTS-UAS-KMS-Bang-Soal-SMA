-- seed_data.sql
USE kms_bsoal;

-- Disable constraints temporarily
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `identities`;
TRUNCATE TABLE `teachers`;
TRUNCATE TABLE `kms_explicit`;
TRUNCATE TABLE `questions`;
TRUNCATE TABLE `forum_topics`;
TRUNCATE TABLE `forum_replies`;
TRUNCATE TABLE `discussions`;
SET FOREIGN_KEY_CHECKS = 1;

-- Staff re-insert (because we truncated identities)
INSERT INTO `identities` (`id`, `actor_type`) VALUES
(1, 'STAFF'), (2, 'STAFF'), (3, 'STAFF');

-- Insert Teachers
INSERT INTO `identities` (`actor_type`) VALUES 
('TEACHER'), ('TEACHER'), ('TEACHER'), ('TEACHER'), ('TEACHER'), ('TEACHER'), ('TEACHER');

SET @id_dina = 4;
INSERT INTO `teachers` (`identity_id`, `nip`, `full_name`, `pin`) VALUES
(@id_dina, '19800101201001', 'Dina Sisilia, S.Pd', '123456'),
(@id_dina+1, '19810202201002', 'Rina Natalia, M.Pd', '123456'),
(@id_dina+2, '19820303201003', 'Adithya Kusuma, S.Si', '123456'),
(@id_dina+3, '19830404201004', 'Linda Lusiana, S.Pd', '123456'),
(@id_dina+4, '19840505201005', 'Cicilia Dewi Andriani, M.Pd', '123456'),
(@id_dina+5, '19850606201006', 'Mega Devinta, S.Pd', '123456'),
(@id_dina+6, '19860707201007', 'Indayan Budi, S.Pd', '123456');

-- Insert Explicit Knowledge (SOP & Templates)
INSERT INTO `kms_explicit` (`title`, `type`, `file_path`, `uploader_id`) VALUES
('Panduan Penulisan Soal Pilihan Ganda (SOP)', 'SOP', 'Panduan_Penulisan_Soal_Pilihan_Ganda.docx', 2),
('Template Kisi-Kisi Penulisan Soal', 'Template', 'Template_Kisi_Kisi_Soal.docx', 2);

-- Insert Questions (Bank Soal)
SET @cat_mtk = (SELECT id FROM categories WHERE code = 'MTK-U');
SET @cat_fis = (SELECT id FROM categories WHERE code = 'FIS');
SET @cat_bio = (SELECT id FROM categories WHERE code = 'BIO');
SET @cat_sej = (SELECT id FROM categories WHERE code = 'SEJ-I');
SET @cat_big = (SELECT id FROM categories WHERE code = 'BIG');
SET @cat_kim = (SELECT id FROM categories WHERE code = 'KIM');
SET @cat_sos = (SELECT id FROM categories WHERE code = 'SOS');
SET @cat_eko = (SELECT id FROM categories WHERE code = 'EKO');

INSERT INTO `questions` (`title`, `class_id`, `category_id`, `materi`, `file_path`, `original_name`, `file_type`, `explanation`, `tags`, `status`, `uploader_id`) VALUES
('Soal Matematika Umum X - Eksponen', 1, @cat_mtk, 'Eksponen dan Logaritma', 'Soal_Matematika_Umum_X.docx', 'Soal_Matematika_Umum_X.docx', 'Word', '<p><strong>Pembahasan Lengkap:</strong></p><p>Untuk menyelesaikan soal nomor 1, kita gunakan sifat dasar eksponen: a<sup>m</sup> / a<sup>n</sup> = a<sup>m-n</sup>. Jadi (a^3 b^-2 c) / (a b^-4 c^2) = a^(3-1) b^(-2-(-4)) c^(1-2) = a^2 b^2 c^-1.</p><p>Perhatikan bahwa banyak siswa terkecoh pada tanda negatif b, sehingga perlu penekanan ekstra saat mengajar.</p>', 'PTS,SemesterGanjil', 'Verified', @id_dina),
('Soal Fisika XI - Dinamika Partikel', 2, @cat_fis, 'Hukum Newton', 'Soal_Fisika_XI_Dinamika.docx', 'Soal_Fisika_XI_Dinamika.docx', 'Word', '<p><strong>Kunci Jawaban & Panduan Menilai:</strong></p><p>Pada soal gaya gesek kinetis, perhatikan gaya normalnya terlebih dahulu sebelum menghitung gesekan. N = W = m*g = 10 * 10 = 100 N.</p><p>F_gesek_max = 0.4 * 100 = 40 N. Karena F_tarik (50 N) > F_gesek_max (40 N), balok bergerak. Maka gaya gesek yang bekerja adalah gaya gesek kinetis = 0.2 * 100 = 20 N.</p><p>Jawaban yang benar adalah A.</p>', 'HOTS,Dinamika', 'Review', @id_dina+1),
('Soal Biologi XII - Metabolisme Sel', 3, @cat_bio, 'Anabolisme & Katabolisme', 'Soal_Biologi_XII_Sel.docx', 'Soal_Biologi_XII_Sel.docx', 'Word', '<p><strong>Analisis Soal:</strong></p><p>1. Faktor yang memengaruhi enzim: Suhu, pH, Konsentrasi, Inhibitor. Warna substrat tidak memengaruhi. Jawaban: D.</p><p>2. Reaksi terang terjadi di Grana. Reaksi gelap di Stroma. Jawaban: B.</p><p>3. Glikolisis menghasilkan 2 ATP, 2 Asam Piruvat, 2 NADH. Jawaban: A.</p>', 'UAS,Biologi', 'Verified', @id_dina+2),
('Soal Sejarah XI - Kemerdekaan', 2, @cat_sej, 'Proklamasi Kemerdekaan', 'Soal_Sejarah_XI_Kemerdekaan.docx', 'Soal_Sejarah_XI_Kemerdekaan.docx', 'Word', '<p><strong>Kunci Jawaban Esai:</strong></p><p>1. Rengasdengklok mendesak Soekarno-Hatta untuk segera memproklamasikan kemerdekaan agar tidak terpengaruh janji Jepang.</p><p>2. Perjanjian Linggarjati mengakui kekuasaan RI secara de facto atas Jawa, Madura, dan Sumatera.</p>', 'SejarahWajib,Kemerdekaan', 'Verified', @id_dina+3),
('Soal Bahasa Inggris X - Narrative', 1, @cat_big, 'Narrative Text', 'Soal_Bahasa_Inggris_X_Narrative.docx', 'Soal_Bahasa_Inggris_X_Narrative.docx', 'Word', '<p><strong>Pembahasan:</strong></p><p>Soal nomor 1 mencari <em>Moral Value</em>. Jawabannya tersirat di paragraf terakhir. Soal nomor 2 adalah mencari <em>Main Character</em>, yang dijelaskan di awal paragraf (Poor Widow).</p>', 'Reading,Narrative', 'Draft', @id_dina+4),
('Soal Kimia XI - Hidrokarbon', 2, @cat_kim, 'Senyawa Hidrokarbon', 'Soal_Kimia_XI_Hidrokarbon.docx', 'Soal_Kimia_XI_Hidrokarbon.docx', 'Word', '<p><strong>Pembahasan:</strong></p><p>Deret homolog alkana: CnH2n+2. Jika n=5, maka senyawanya adalah C5H12 yang disebut Pentana. Jawaban: D.</p>', 'Hidrokarbon,Kimia', 'Verified', @id_dina+5),
('Soal Sosiologi XII - Perubahan', 3, @cat_sos, 'Perubahan Sosial', 'Soal_Sosiologi_XII_Perubahan.docx', 'Soal_Sosiologi_XII_Perubahan.docx', 'Word', '<p><strong>Pembahasan:</strong></p><p>Faktor eksternal berasal dari luar masyarakat, contohnya pengaruh kebudayaan lain, peperangan, dan bencana alam. Penemuan baru dan konflik termasuk internal. Jawaban: C.</p>', 'Sosiologi,PerubahanSosial', 'Review', @id_dina+6),
('Soal Ekonomi XI - Pajak', 2, @cat_eko, 'Perpajakan', 'Soal_Ekonomi_XI_Pajak.docx', 'Soal_Ekonomi_XI_Pajak.docx', 'Word', '<p><strong>Pembahasan:</strong></p><p>Pajak yang dipungut oleh pemerintah daerah disebut Pajak Daerah (contoh: pajak kendaraan, restoran). Jawaban: B.</p>', 'Ekonomi,Pajak', 'Verified', @id_dina);

-- Insert Forum Topics and Replies
INSERT INTO `forum_topics` (`title`, `kategori_forum`, `actor_id`) VALUES
('Standar Pembuatan Soal HOTS untuk Ujian Akhir Sekolah', 'Umum', 2),
('Diskusi Evaluasi Penggunaan ChatGPT oleh Siswa', 'Umum', @id_dina+2),
('Penyelarasan Kisi-Kisi Lintas Mata Pelajaran', 'Umum', @id_dina+3);

SET @topic_id1 = (SELECT id FROM forum_topics WHERE title LIKE '%Standar Pembuatan Soal%');
SET @topic_id2 = (SELECT id FROM forum_topics WHERE title LIKE '%ChatGPT%');
SET @topic_id3 = (SELECT id FROM forum_topics WHERE title LIKE '%Penyelarasan%');

INSERT INTO `forum_replies` (`topic_id`, `actor_id`, `message`) VALUES
(@topic_id1, @id_dina, 'Menurut saya, kita harus sepakat dulu soal taksonomi bloom yang akan dipakai, apakah minimal C4 untuk semua jenjang kelas?'),
(@topic_id1, @id_dina+1, 'Setuju Ms. Dina, dan mungkin butuh simulasi atau bedah soal bersama minggu depan. Untuk kelas X mungkin komposisi HOTS nya 30% saja cukup.'),
(@topic_id1, 2, 'Baik, usulan Ms. Rina dan Ms. Dina akan diagendakan oleh bagian kurikulum pada rapat pleno minggu depan.'),
(@topic_id1, @id_dina+6, 'Terima kasih, saya tunggu jadwal simulasi bedah soalnya. Mr. Adithya mungkin bisa share template yang biasa dipakai di MGMP.'),

(@topic_id2, @id_dina+2, 'Rekan-rekan, belakangan ini saya melihat indikasi jawaban esai siswa sangat persis dengan pola AI. Bagaimana kita menyikapinya?'),
(@topic_id2, @id_dina+4, 'Benar Mr. Adithya. Mungkin kita perlu mengubah model asesmen menjadi lebih berbasis proyek atau presentasi lisan.'),
(@topic_id2, @id_dina+5, 'Saya setuju dengan Ms. Linda. Kita bisa kombinasikan dengan pertanyaan pemantik di kelas untuk validasi pemahaman mereka.'),

(@topic_id3, @id_dina+3, 'Bapak Ibu, mohon ketersediaannya untuk mengecek irisan materi antara Sejarah dan Sosiologi agar tidak terjadi repetisi dalam penyusunan instrumen soal UTS.'),
(@topic_id3, @id_dina+6, 'Siap Ms. Linda, saya akan cek kembali KD Sosiologi kelas XI yang beririsan dengan Sejarah.');

-- Insert Discussions on a Question
SET @question_id1 = (SELECT id FROM questions WHERE title = 'Soal Matematika Umum X - Eksponen');
INSERT INTO `discussions` (`question_id`, `actor_id`, `comment`) VALUES
(@question_id1, @id_dina+1, 'Mr., soal nomor 2 apakah tidak terlalu sulit untuk kelas 10 semester awal? Sepertinya angka logaritmanya terlalu besar.'),
(@question_id1, @id_dina, 'Bisa disederhanakan Ms. Rina, nanti saya revisi angka eksponennya agar tidak terlalu pecah dan sesuai dengan jam terbang siswa di minggu pertama.');

SET @question_id2 = (SELECT id FROM questions WHERE title = 'Soal Kimia XI - Hidrokarbon');
INSERT INTO `discussions` (`question_id`, `actor_id`, `comment`) VALUES
(@question_id2, @id_dina+2, 'Ms. Mega, mungkin bisa ditambahkan gambar struktur molekul untuk soal nomor 1 agar siswa lebih mudah memvisualisasikannya.'),
(@question_id2, @id_dina+5, 'Ide bagus Mr. Adithya. Saya akan tambahkan strukturnya di versi revisi nanti sore.');

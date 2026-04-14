-- database.sql
-- Refactored for Version B (Realism Update: NIP & Kode Mapel)
-- Version: 4.0 (Identity Hub Pattern + Realistic Metadata)

CREATE DATABASE IF NOT EXISTS kms_bsoal;
USE kms_bsoal;

-- 1. Identity Registry (The central hub for all actors)
CREATE TABLE IF NOT EXISTS identities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    actor_type ENUM('STAFF', 'TEACHER') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Roles Table (For Staff only)
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

INSERT IGNORE INTO roles (id, role_name) VALUES 
(1, 'Admin Sistem'), 
(2, 'Admin Akademik / Administrasi'), 
(4, 'Kepala Sekolah');

-- 3. Staff Table (Internal Accounts)
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identity_id INT NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Standard: admin123
    full_name VARCHAR(100) NOT NULL,
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (identity_id) REFERENCES identities(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

-- 4. Teachers Table (Master Guru Data with NIP)
CREATE TABLE IF NOT EXISTS teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identity_id INT NOT NULL,
    nip VARCHAR(20) NOT NULL UNIQUE, -- 18 Digits
    full_name VARCHAR(100) NOT NULL,
    pin VARCHAR(10) DEFAULT '123456',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (identity_id) REFERENCES identities(id) ON DELETE CASCADE
);

-- 5. Academic Structure
CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE -- X, XI, XII
);

INSERT IGNORE INTO classes (id, name) VALUES (1, 'X'), (2, 'XI'), (3, 'XII');

-- 6. Categories Table (SMA Subjects with Codes)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE, -- MTK, FIS, etc.
    name VARCHAR(100) NOT NULL,
    description TEXT
);

-- Clearing old categories to insert with codes
DELETE FROM categories;
INSERT INTO categories (code, name) VALUES 
('MTK-U', 'Matematika (Umum)'), 
('MTK-P', 'Matematika (Peminatan)'),
('FIS', 'Fisika'), 
('KIM', 'Kimia'), 
('BIO', 'Biologi'), 
('BIN', 'Bahasa Indonesia'), 
('BIG', 'Bahasa Inggris'), 
('MAN', 'Bahasa Mandarin'), 
('JPN', 'Bahasa Jepang'), 
('GER', 'Bahasa Jerman'), 
('ARB', 'Bahasa Arab'),
('SEJ-I', 'Sejarah Indonesia'), 
('SEJ-P', 'Sejarah (Peminatan)'), 
('GEO', 'Geografi'), 
('SOS', 'Sosiologi'), 
('EKO', 'Ekonomi'), 
('ANT', 'Antropologi'),
('KRISTEN', 'Pendidikan Agama Kristen'),
('PPKN', 'PPKn'), 
('SENI', 'Seni Budaya'), 
('PJOK', 'PJOK'), 
('PKWU', 'Prakarya dan Kewirausahaan'), 
('INF', 'Informatika'), 
('BK', 'Bimbingan Konseling (BK)');

-- 7. Questions Table
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    class_id INT,
    category_id INT,
    materi VARCHAR(255) NOT NULL,
    content LONGTEXT,
    file_path VARCHAR(255), 
    original_name VARCHAR(255),
    file_type VARCHAR(50),
    explanation LONGTEXT NOT NULL, 
    difficulty ENUM('Mudah', 'Sedang', 'Sulit') DEFAULT 'Sedang',
    status ENUM('Draft', 'Review', 'Verified') DEFAULT 'Draft',
    uploader_id INT, -- Refers to identities.id
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (uploader_id) REFERENCES identities(id) ON DELETE SET NULL
);

-- 8. Question Status Logs
CREATE TABLE IF NOT EXISTS question_status_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    actor_id INT,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES identities(id) ON DELETE SET NULL
);

-- 9. Discussions Table
CREATE TABLE IF NOT EXISTS discussions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    actor_id INT,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES identities(id) ON DELETE SET NULL
);

-- 10. KMS Explicit Knowledge (SOP & Templates)
CREATE TABLE IF NOT EXISTS kms_explicit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type ENUM('SOP', 'Template') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploader_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploader_id) REFERENCES identities(id) ON DELETE SET NULL
);

-- 11. Forum Topics
CREATE TABLE IF NOT EXISTS forum_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    actor_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (actor_id) REFERENCES identities(id) ON DELETE SET NULL
);

-- 12. Forum Replies
CREATE TABLE IF NOT EXISTS forum_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT,
    actor_id INT,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (actor_id) REFERENCES identities(id) ON DELETE SET NULL
);

-- 13. System Activity Logs
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    actor_id INT,
    action TEXT NOT NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (actor_id) REFERENCES identities(id) ON DELETE SET NULL
);

-- 14. Notifications System
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    actor_id INT, -- Recipient
    message TEXT NOT NULL,
    link VARCHAR(255) DEFAULT '#',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (actor_id) REFERENCES identities(id) ON DELETE CASCADE
);

-- ========================================================
-- SEEDING REALISTIC DATA
-- ========================================================

-- Reset Seeds
DELETE FROM staff;
DELETE FROM teachers;
DELETE FROM identities;

-- Identity hub seeding
INSERT INTO identities (id, actor_type) VALUES (1, 'STAFF'), (2, 'STAFF'), (3, 'STAFF'), (4, 'TEACHER'), (5, 'TEACHER');

-- Seeding Staff Accounts
INSERT INTO staff (identity_id, username, password, full_name, role_id) VALUES 
(1, 'admin', 'admin123', 'Superadmin Sistem', 1),
(2, 'administrasi', 'admin123', 'Admin Akademik (Administrasi)', 2),
(3, 'kepsek', 'admin123', 'Drs. H. M. Husain (Kepsek)', 4);

-- Seeding Master Guru (Realistic 18-digit NIP)
INSERT INTO teachers (identity_id, nip, full_name, pin) VALUES 
(4, '198501012010121001', 'Budi Santoso, S.Pd', '123456'),
(5, '199005122015042002', 'Siti Aminah, M.Pd', '123456');

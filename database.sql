CREATE DATABASE IF NOT EXISTS kms_bsoal;
USE kms_bsoal;

-- Roles Table (4 Roles)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO roles (role_name) VALUES 
('Admin Akademik'), 
('Administrator (TU)'), 
('Guru'), 
('Kepala Sekolah');

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
);

-- Categories Table (Mapel)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Documents Table (The Bank Soal - Explicit Knowledge)
CREATE TABLE documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    class_level ENUM('X', 'XI', 'XII') NOT NULL,
    materi VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL, -- Stored as MD5 hash
    original_name VARCHAR(255) NOT NULL, -- Original filename
    file_type VARCHAR(50),
    explanation TEXT NOT NULL, -- Pembahasan (Explicit Knowledge)
    uploader_id INT,
    category_id INT,
    difficulty ENUM('Mudah', 'Sedang', 'Sulit') DEFAULT 'Sedang',
    status ENUM('Draft', 'Review', 'Verified') DEFAULT 'Draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Discussions Table (Per-Document Tacit Knowledge)
CREATE TABLE discussions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    document_id INT,
    user_id INT,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (document_id) REFERENCES documents(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Forum Topics (General Tacit Knowledge)
CREATE TABLE forum_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Forum Replies
CREATE TABLE forum_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT,
    user_id INT,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Logs Table (Audit Trail)
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Initial Data
INSERT INTO categories (name) VALUES ('Matematika'), ('Fisika'), ('Kimia'), ('Biologi'), ('Bahasa Indonesia'), ('Bahasa Inggris');

-- Default Users (password: password123)
-- Hash: $2a$12$mOEgmBDZgiF7B/sEVbtT3.1LISa3fSSSjf71euZrpeA9EYu4B0h/m
INSERT INTO users (username, password, full_name, role_id) VALUES 
('admin', '$2a$12$mOEgmBDZgiF7B/sEVbtT3.1LISa3fSSSjf71euZrpeA9EYu4B0h/m', 'Admin Akademik', 1),
('tu_admin', '$2a$12$mOEgmBDZgiF7B/sEVbtT3.1LISa3fSSSjf71euZrpeA9EYu4B0h/m', 'Tata Usaha (IT)', 2),
('guru_test', '$2a$12$mOEgmBDZgiF7B/sEVbtT3.1LISa3fSSSjf71euZrpeA9EYu4B0h/m', 'Guru Pengajar', 3),
('kepsek', '$2a$12$mOEgmBDZgiF7B/sEVbtT3.1LISa3fSSSjf71euZrpeA9EYu4B0h/m', 'Kepala Sekolah', 4);

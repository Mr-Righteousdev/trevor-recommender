-- CBC Resource Recommender - Full Database Schema + Seed Data
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS cbc_recommender CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cbc_recommender;

-- Users
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('student', 'lecturer', 'admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses
CREATE TABLE courses (
    course_id INT PRIMARY KEY AUTO_INCREMENT,
    course_code VARCHAR(20) UNIQUE NOT NULL,
    course_name VARCHAR(150) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Competencies
CREATE TABLE competencies (
    competency_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    competency_code VARCHAR(30),
    competency_name VARCHAR(255) NOT NULL,
    description TEXT,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- Resources
CREATE TABLE resources (
    resource_id INT PRIMARY KEY AUTO_INCREMENT,
    competency_id INT NOT NULL,
    resource_title VARCHAR(255) NOT NULL,
    resource_type ENUM('link', 'pdf', 'video', 'document') NOT NULL,
    resource_url VARCHAR(500) NOT NULL,
    description TEXT,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'approved',
    uploaded_by INT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (competency_id) REFERENCES competencies(competency_id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Enrollments
CREATE TABLE enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- Lecturer ↔ Course assignments
CREATE TABLE lecturer_courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    UNIQUE KEY unique_lecturer_course (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- ===================== SEED DATA =====================
-- Default password for all demo users is: password
-- Hash generated with password_hash('password', PASSWORD_DEFAULT)

INSERT INTO users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@slu.ac.ug', 'admin'),
('lecturer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Jane Nabirye', 'jane@slu.ac.ug', 'lecturer'),
('lecturer2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mr. Peter Okello', 'peter@slu.ac.ug', 'lecturer'),
('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trevor Kiyombo', 'trevor@student.slu.ac.ug', 'student'),
('student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Nakato', 'sarah@student.slu.ac.ug', 'student');

INSERT INTO courses (course_code, course_name, description) VALUES
('BIT3101', 'Database Systems', 'Fundamentals of relational databases, normalization and SQL'),
('BIT3202', 'Web Application Development', 'Building dynamic web applications with PHP and MySQL'),
('BIT2103', 'Object Oriented Programming', 'OOP concepts and implementation');

INSERT INTO competencies (course_id, competency_code, competency_name, description) VALUES
(1, 'DB-01', 'Design a normalized relational database schema', 'Apply normalization up to 3NF'),
(1, 'DB-02', 'Write complex SQL queries', 'JOINs, subqueries, aggregation'),
(1, 'DB-03', 'Implement database security best practices', 'Users, privileges, prepared statements'),
(2, 'WEB-01', 'Build responsive front-end interfaces', 'HTML, CSS, Bootstrap'),
(2, 'WEB-02', 'Develop server-side logic with PHP', 'Sessions, forms, CRUD operations'),
(2, 'WEB-03', 'Connect PHP applications to MySQL securely', 'PDO and prepared statements'),
(3, 'OOP-01', 'Apply encapsulation and inheritance', 'Class design principles'),
(3, 'OOP-02', 'Implement polymorphism', 'Method overriding and interfaces');

INSERT INTO resources (competency_id, resource_title, resource_type, resource_url, description, uploaded_by) VALUES
(1, 'Database Normalization Explained', 'video', 'https://www.youtube.com/watch?v=UrYLYV7WSHM', 'Clear video covering 1NF, 2NF and 3NF', 2),
(1, 'Normalization Practice Exercises', 'pdf', 'https://example.com/normalization.pdf', 'Practice problems on database normalization', 2),
(2, 'SQL Joins Complete Guide', 'link', 'https://www.w3schools.com/sql/sql_join.asp', 'W3Schools SQL JOIN reference', 2),
(4, 'Bootstrap 5 Crash Course', 'video', 'https://www.youtube.com/watch?v=4sosXZsdy-s', 'Full Bootstrap 5 tutorial', 3),
(5, 'PHP Sessions & Authentication', 'document', 'https://www.php.net/manual/en/book.session.php', 'Official PHP session documentation', 3),
(6, 'PDO Tutorial for Beginners', 'link', 'https://phpdelusions.net/pdo', 'Excellent PDO guide', 3);

INSERT INTO enrollments (user_id, course_id) VALUES
(4, 1), (4, 2), (5, 1), (5, 3);

INSERT INTO lecturer_courses (user_id, course_id) VALUES
(2, 1), (2, 2), (3, 2), (3, 3);

-- Activity log for tracking all user actions (system monitoring)
CREATE TABLE activity_log (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE INDEX idx_activity_log_user ON activity_log(user_id);
CREATE INDEX idx_activity_log_action ON activity_log(action);
CREATE INDEX idx_activity_log_date ON activity_log(created_at);

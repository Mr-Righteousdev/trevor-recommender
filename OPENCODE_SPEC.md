# CBC Resource Recommender – Full Application Specification

**Project Title:** Database-Driven Personalized Learning Resource Recommendation System for Competency-Based Education  
**Institution:** St. Lawrence University Uganda  
**Case Study:** Faculty of Science and Technology  
**Tech Stack (STRICT):**  
- Backend: PHP 8.x (procedural + simple OOP where helpful)  
- Database: MySQL 8.x / MariaDB  
- Frontend: HTML5 + CSS3 + Bootstrap 5.3  
- No frameworks (no Laravel, no Composer packages beyond what is necessary)  
- No JavaScript frameworks (vanilla JS only for small interactions)  
- Rule-based recommendation only (NO machine learning / AI)

This document is a complete, production-ready specification. Feed this entire file to OpenCode (or any coding agent) and instruct it to generate the full working application following every section below.

---

## 1. Project Overview

Build a simple, secure, role-based web application that allows:

- **Lecturers** to upload learning resources and explicitly tag them to specific competencies of a course.
- **Students** to select a course → select a competency → receive a filtered list of all resources tagged to that competency.
- **Admins** to manage users, courses, and competencies.

The core value is the **explicit competency → resource linkage**. This is a rule-based system:  
`IF student selects Competency X THEN return all resources WHERE competency_id = X`.

---

## 2. User Roles & Permissions

| Role       | Permissions |
|------------|-------------|
| **Student**   | Register/Login, View list of courses, Select course → View competencies, Select competency → View recommended resources, Search resources by keyword |
| **Lecturer**  | Login, View courses they teach, Upload new resource + tag to competency, Edit/Delete own resources, View all resources they uploaded |
| **Admin**     | Login, Full CRUD on Users, Courses, Competencies, View all resources, System overview |

Passwords must be hashed with `password_hash()` / `password_verify()` (bcrypt).

---

## 3. Database Schema (MySQL)

Create a database named `cbc_recommender`.

```sql
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

-- Competencies (belong to a course)
CREATE TABLE competencies (
    competency_id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    competency_code VARCHAR(30),
    competency_name VARCHAR(255) NOT NULL,
    description TEXT,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- Resources (tagged to a competency)
CREATE TABLE resources (
    resource_id INT PRIMARY KEY AUTO_INCREMENT,
    competency_id INT NOT NULL,
    resource_title VARCHAR(255) NOT NULL,
    resource_type ENUM('link', 'pdf', 'video', 'document') NOT NULL,
    resource_url VARCHAR(500) NOT NULL,
    description TEXT,
    uploaded_by INT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (competency_id) REFERENCES competencies(competency_id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Optional: Simple enrollment (students ↔ courses)
CREATE TABLE enrollments (
    enrollment_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_enrollment (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- Optional: Lecturers assigned to courses
CREATE TABLE lecturer_courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    UNIQUE KEY unique_lecturer_course (user_id, course_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);
```

### Sample Seed Data (include this)

```sql
-- Admin
INSERT INTO users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Admin', 'admin@slu.ac.ug', 'admin');
-- password is "password"

-- Lecturers
INSERT INTO users (username, password, full_name, email, role) VALUES
('lecturer1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Jane Nabirye', 'jane@slu.ac.ug', 'lecturer'),
('lecturer2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mr. Peter Okello', 'peter@slu.ac.ug', 'lecturer');

-- Students
INSERT INTO users (username, password, full_name, email, role) VALUES
('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trevor Kiyombo', 'trevor@student.slu.ac.ug', 'student'),
('student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Nakato', 'sarah@student.slu.ac.ug', 'student');

-- Courses
INSERT INTO courses (course_code, course_name, description) VALUES
('BIT3101', 'Database Systems', 'Fundamentals of relational databases and SQL'),
('BIT3202', 'Web Application Development', 'Building dynamic web applications with PHP and MySQL'),
('BIT2103', 'Object Oriented Programming', 'OOP concepts using Java/PHP');

-- Competencies for BIT3101
INSERT INTO competencies (course_id, competency_code, competency_name, description) VALUES
(1, 'DB-01', 'Design a normalized relational database schema', 'Ability to apply normalization up to 3NF'),
(1, 'DB-02', 'Write complex SQL queries', 'SELECT with JOINs, subqueries, aggregation'),
(1, 'DB-03', 'Implement database security best practices', 'Users, privileges, prepared statements');

-- Competencies for BIT3202
INSERT INTO competencies (course_id, competency_code, competency_name, description) VALUES
(2, 'WEB-01', 'Build responsive front-end interfaces', 'HTML, CSS, Bootstrap'),
(2, 'WEB-02', 'Develop server-side logic with PHP', 'Sessions, forms, CRUD'),
(2, 'WEB-03', 'Connect PHP applications to MySQL', 'PDO / MySQLi securely');

-- Sample Resources
INSERT INTO resources (competency_id, resource_title, resource_type, resource_url, description, uploaded_by) VALUES
(1, 'Database Normalization Explained', 'video', 'https://www.youtube.com/watch?v=UrYLYV7WSHM', 'Clear video on 1NF, 2NF, 3NF', 2),
(1, 'Normalization Practice PDF', 'pdf', 'https://example.com/normalization.pdf', 'Exercises on normalization', 2),
(2, 'SQL Joins Masterclass', 'link', 'https://www.w3schools.com/sql/sql_join.asp', 'W3Schools reference', 2),
(4, 'Bootstrap 5 Crash Course', 'video', 'https://www.youtube.com/watch?v=4sosXZsdy-s', 'Full Bootstrap tutorial', 3),
(5, 'PHP Sessions & Authentication', 'document', 'https://www.php.net/manual/en/book.session.php', 'Official PHP docs', 3);

-- Enrollments
INSERT INTO enrollments (user_id, course_id) VALUES (4,1), (4,2), (5,1), (5,3);

-- Lecturer assignments
INSERT INTO lecturer_courses (user_id, course_id) VALUES (2,1), (2,2), (3,2), (3,3);
```

---

## 4. Recommended File Structure

```
cbc-resource-recommender/
├── config/
│   └── database.php          # DB connection (PDO preferred)
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── auth_check.php        # Session & role guards
│   └── functions.php         # Helper functions
├── assets/
│   ├── css/
│   │   └── style.css         # Custom styles on top of Bootstrap
│   └── js/
│       └── main.js           # Minimal vanilla JS
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── student/
│   ├── dashboard.php
│   ├── courses.php
│   ├── competencies.php      # ?course_id=X
│   ├── resources.php         # ?competency_id=X  ← CORE FEATURE
│   └── search.php
├── lecturer/
│   ├── dashboard.php
│   ├── upload_resource.php
│   ├── my_resources.php
│   └── edit_resource.php
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   ├── courses.php
│   ├── competencies.php
│   └── resources.php
├── index.php                 # Landing / redirect based on role
├── .htaccess                 # Basic security
└── README.md
```

---

## 5. Core Features to Implement

### 5.1 Authentication
- Login form (username + password)
- Registration (only for students by default; admin can create lecturers)
- Session-based authentication
- Role-based redirects after login
- Logout

### 5.2 Student Flow (Core Value Proposition)
1. Login → Student Dashboard
2. See list of enrolled courses (or all courses)
3. Click a course → list of competencies for that course
4. Click a competency → **Recommended Resources** page showing:
   - Resource title
   - Type (badge: link / pdf / video / document)
   - Description
   - Direct link / open button
   - Uploaded by + date
5. Simple keyword search across resources

### 5.3 Lecturer Flow
1. Dashboard showing assigned courses
2. Upload Resource form:
   - Select Course → dynamically load Competencies (or simple two dropdowns)
   - Title, Type, URL, Description
3. My Resources list with Edit / Delete

### 5.4 Admin Flow
- Manage Users (create lecturer accounts, change roles, delete)
- Manage Courses (CRUD)
- Manage Competencies (CRUD per course)
- View all resources

---

## 6. UI / UX Requirements

- Use **Bootstrap 5.3** (CDN is fine)
- Clean, academic look (blues/greens, good contrast)
- Mobile-responsive
- Clear navigation based on role (different navbar items)
- Success / error flash messages using Bootstrap alerts
- Cards for resources, tables for admin lists
- Loading states not required (keep simple)

---

## 7. Security Requirements (MUST implement)

- Prepared statements only (PDO)
- `password_hash()` / `password_verify()`
- Session regeneration on login
- Role checks on every protected page (`auth_check.php`)
- Escape all output (`htmlspecialchars`)
- CSRF token on forms (simple implementation)
- Basic `.htaccess` to prevent directory listing and protect config

---

## 8. Implementation Order (for the coding agent)

1. Create database + seed data
2. `config/database.php` (PDO connection)
3. `includes/` helpers + header/footer
4. Authentication (login / register / logout)
5. Student side (courses → competencies → resources) — **highest priority**
6. Lecturer upload + manage resources
7. Admin CRUD pages
8. Search functionality
9. Polish UI + flash messages
10. README with setup instructions

---

## 9. README.md Content (generate this too)

```markdown
# CBC Resource Recommender

Database-driven learning resource recommendation system for Competency-Based Education at St. Lawrence University.

## Tech Stack
- PHP 8+
- MySQL
- Bootstrap 5
- Vanilla JS

## Setup
1. Clone / copy files into your web root (XAMPP htdocs, Laragon, etc.)
2. Create database and import the SQL from the specification (or run the provided schema)
3. Update `config/database.php` with your DB credentials
4. Access via `http://localhost/cbc-resource-recommender`
5. Default logins:
   - Admin: admin / password
   - Lecturer: lecturer1 / password
   - Student: student1 / password

## Features
- Role-based access (Student / Lecturer / Admin)
- Competency-tagged resource recommendations
- Simple upload & management for lecturers
```

---

## 10. Important Constraints for the Coding Agent

- Keep the code simple and readable (this is a student project).
- Prefer PDO over MySQLi.
- Do **not** introduce Laravel, Composer dependencies, or React/Vue.
- Do **not** implement AI/ML recommendations.
- Focus first on making the student recommendation flow work perfectly.
- Use Bootstrap 5 via CDN.
- All pages must be protected according to role.
- Provide clear comments in the code.

---

**End of Specification**

When you receive this file, generate the complete working application following the structure and requirements above. Start with the database and authentication, then the student recommendation flow.

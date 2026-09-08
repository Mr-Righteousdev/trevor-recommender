# CBC Resource Recommender

**Database-Driven Personalized Learning Resource Recommendation System for Competency-Based Education**  
St. Lawrence University Uganda – Faculty of Science and Technology

## Tech Stack
- PHP 8+
- MySQL / MariaDB
- Bootstrap 5.3
- Vanilla JavaScript

## Features
- **Role-based access**: Student, Lecturer, Admin
- **Core feature**: Students select Course → Competency → receive filtered, lecturer-tagged resources
- Lecturers can upload and tag resources to specific competencies
- Simple keyword search
- Secure authentication (password hashing, prepared statements, CSRF tokens)

## Quick Setup (XAMPP / Laragon / similar)

1. Copy the entire `cbc-resource-recommender` folder into your web root  
   (e.g. `C:\xampp\htdocs\` or `C:\laragon\www\`)

2. Create the database:
   - Open phpMyAdmin → Import → select `database/schema.sql`
   - Or run the SQL file via MySQL command line

3. Update database credentials if needed:  
   Edit `config/database.php`  
   (default is `root` with empty password – typical for XAMPP)

4. Access the application:  
   `http://localhost/cbc-resource-recommender`

## Demo Accounts
| Role     | Username   | Password  |
|----------|------------|-----------|
| Admin    | admin      | password  |
| Lecturer | lecturer1  | password  |
| Student  | student1   | password  |

## Project Structure
```
cbc-resource-recommender/
├── config/database.php
├── includes/          (header, footer, helpers, auth)
├── auth/              (login, register, logout)
├── student/           (dashboard, competencies, resources, search)
├── lecturer/          (dashboard, upload, my_resources)
├── admin/             (dashboard + management pages)
├── assets/            (css, js)
├── database/schema.sql
├── OPENCODE_SPEC.md   ← Full specification for AI coding agents
└── README.md
```

## For OpenCode / AI Coding Agents
Use the file `OPENCODE_SPEC.md` as the complete prompt. It contains the full database schema, file structure, requirements, and implementation order.

## Notes
- This is a rule-based system (not AI/ML).
- The student recommendation flow is the highest priority feature and is fully implemented.
- Admin CRUD pages are scaffolded and can be expanded.
- All passwords in the seed data are hashed with `password_hash()`.

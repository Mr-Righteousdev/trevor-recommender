# System Testing Guide — CBC Resource Recommender

Complete manual testing checklist for verifying every feature in the system.

---

## Part 1: Local Setup

### Prerequisites

- **PHP 8.0+** with these extensions: `pdo_mysql`, `session`, `mbstring`
- **MySQL 5.7+** or **MariaDB 10.3+**
- **Apache** web server (XAMPP, Laragon, or similar)

### Installation Steps

1. Copy the project folder `trevor-recommender` into your web root:
   - XAMPP: `C:\xampp\htdocs\`
   - Laragon: `C:\laragon\www\`
   - Linux: `/var/www/html/`

2. Start Apache and MySQL from your control panel.

3. Create the database:
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
   - Click **Import** → choose `database/schema.sql` → click **Go**
   - This creates the `cbc_recommender` database with all tables, seed data, and activity log

4. Verify `config/database.php` credentials match your setup:
   ```
   DB_HOST = localhost
   DB_USER = root
   DB_PASS = (empty for XAMPP)
   ```

6. Open the application:
   ```
   http://localhost/trevor-recommender
   ```

### Demo Accounts

| Role     | Username  | Password  |
|----------|-----------|-----------|
| Admin    | admin     | password  |
| Lecturer | lecturer1 | password  |
| Student  | student1  | password  |

All demo passwords are `password`.

---

## Part 2: Feature Testing

Work through each section below. Check off each item as you verify it.

---

### Section A: Authentication

#### A1. Landing Page
- [ ] Open `http://localhost/trevor-recommender` — the home page loads
- [ ] Click **Login** button — navigates to login page
- [ ] Click **Register as Student** — navigates to registration page

#### A2. Student Registration
- [ ] Go to Register page
- [ ] Fill in: Full Name, Username, Email, Password, Confirm Password
- [ ] Click **Register** — redirects to login page with success message
- [ ] Try registering with the same username — error message shown
- [ ] Try registering with a short password (<6 chars) — validation error
- [ ] Try registering with mismatched passwords — error shown

#### A3. Login
- [ ] Log in as `student1` / `password` — redirects to Student Dashboard
- [ ] Log out (click your name → Logout)
- [ ] Log in as `lecturer1` / `password` — redirects to Lecturer Dashboard
- [ ] Log out
- [ ] Log in as `admin` / `password` — redirects to Admin Dashboard
- [ ] Log out
- [ ] Try logging in with wrong password — error shown
- [ ] Try logging in with non-existent username — error shown

#### A4. Role-Based Access
- [ ] Log in as `student1`
- [ ] Manually type `http://localhost/trevor-recommender/admin/dashboard.php` in the address bar — redirected to home with "no permission" message
- [ ] Manually type `http://localhost/trevor-recommender/lecturer/dashboard.php` — redirected to home
- [ ] Log out, log in as `lecturer1`
- [ ] Try accessing `http://localhost/trevor-recommender/admin/dashboard.php` — redirected
- [ ] Log out, log in as `admin`
- [ ] Try accessing `http://localhost/trevor-recommender/student/dashboard.php` — redirected

---

### Section B: Student Features

Log in as `student1`.

#### B1. Student Dashboard
- [ ] Dashboard shows "Welcome, Trevor Kiyombo"
- [ ] Enrolled courses are displayed as cards (BIT3101, BIT3202)
- [ ] Each card shows course code, name, description snippet
- [ ] "View Competencies" button is present on each card

#### B2. My Courses (Enrollment)
- [ ] Click **My Courses** in the nav bar
- [ ] All courses are listed
- [ ] Courses you're enrolled in show "Enrolled" badge
- [ ] Courses you're not enrolled in show an **Enroll** button
- [ ] Click **Enroll** on a course you're not enrolled in — success message, badge changes to "Enrolled"
- [ ] Click **Unenroll** (X button) on an enrolled course — confirmation dialog, then success message
- [ ] Click **View Competencies** on an enrolled course — navigates to competencies page

#### B3. Competencies View
- [ ] From dashboard or My Courses, click "View Competencies" on BIT3101
- [ ] Page shows "Database Systems" as the course name
- [ ] Breadcrumb shows: Dashboard > BIT3101
- [ ] List of competencies is displayed (DB-01, DB-02, DB-03)
- [ ] Each competency shows code badge, name, and description
- [ ] Click on a competency — navigates to resources page

#### B4. Resource Recommendation (Core Feature)
- [ ] Click on "DB-01: Design a normalized relational database schema"
- [ ] Breadcrumb shows: Dashboard > BIT3101 > Resources
- [ ] Competency name and code are displayed
- [ ] Resource count is shown (e.g., "2 recommended resources")
- [ ] Each resource card shows:
  - [ ] Type badge (LINK, PDF, VIDEO, DOCUMENT) with color
  - [ ] Title (clickable, opens in new tab)
  - [ ] Description
  - [ ] Uploader name
  - [ ] Upload date
  - [ ] "Open Resource" button (opens URL in new tab)
- [ ] Go back and click on DB-02 — different set of resources shown
- [ ] Go back and click on WEB-01 (BIT3202) — shows Bootstrap video resource
- [ ] Verify: DB-01 shows 2 resources, WEB-01 shows 1 resource, etc. (matches seed data)

#### B5. Student Search
- [ ] Click **Search** in the nav bar
- [ ] Search for "normalization" — shows the Database Normalization resource
- [ ] Search for "bootstrap" — shows the Bootstrap 5 Crash Course
- [ ] Search for "PDO" — shows the PDO Tutorial
- [ ] Search for a nonsense term (e.g., "xyz123") — shows "No results found"
- [ ] Clear the search — all results disappear or reset

#### B6. Only Approved Resources Visible to Students
- [ ] Resources with status "pending" or "rejected" should NOT appear in student views
- [ ] This will be verified after testing the approval workflow in Section D

---

### Section C: Lecturer Features

Log out, then log in as `lecturer1`.

#### C1. Lecturer Dashboard
- [ ] Dashboard shows "Welcome, Dr. Jane Nabirye"
- [ ] Shows "Assigned Courses" count (2 for lecturer1)
- [ ] Shows "Resources Uploaded" count
- [ ] Quick action buttons: Upload New Resource, My Resources, Manage Courses, Manage Competencies
- [ ] Lists assigned courses (BIT3101, BIT3202)

#### C2. Upload Resource
- [ ] Click **Upload Resource** in nav or dashboard
- [ ] Page shows "Upload Learning Resource" heading
- [ ] Competency dropdown is populated with competencies from your courses
- [ ] Dropdown groups by course code (e.g., "BIT3101 – DB-01: Design a...")
- [ ] Fill in: select competency, title, type (Link), URL, description
- [ ] Click **Upload & Tag Resource** — success message, redirected to My Resources
- [ ] The new resource appears in My Resources table with status "Pending"
- [ ] Try submitting with empty fields — validation errors shown
- [ ] Try submitting with an invalid URL — error shown
- [ ] Try submitting without selecting a competency — error shown

#### C3. My Resources
- [ ] Click **My Resources** in nav
- [ ] Table shows all resources you uploaded
- [ ] Columns: Title, Type, Status, Course/Competency, Uploaded, Actions
- [ ] New uploads show status "Pending" (yellow badge)
- [ ] Seed data resources show "Approved" (green badge)
- [ ] Click the pencil (edit) icon — navigates to edit page
- [ ] Click the trash (delete) icon — confirmation dialog, then resource is removed

#### C4. Edit Resource
- [ ] Click edit on a resource you uploaded
- [ ] Edit page shows with pre-filled form fields
- [ ] Breadcrumb: My Resources > Edit Resource
- [ ] Change the title and click **Save Changes** — success message
- [ ] Verify the title changed in My Resources table
- [ ] Click **Cancel** — returns to My Resources without changes

#### C5. Manage Courses (Lecturer)
- [ ] Click **Courses** in the nav bar
- [ ] Shows your assigned courses with competency counts
- [ ] **Assign a Course** section shows courses you're not yet assigned to
- [ ] Select a course from the dropdown and click **Assign** — success message
- [ ] The course now appears in your assigned list
- [ ] Click **Unassign** on a course — confirmation dialog, then course removed from your list
- [ ] Try unassigning all courses — verify you get redirected with a message

#### C6. Manage Competencies (Lecturer)
- [ ] Click **Competencies** in the nav bar
- [ ] Shows competencies for your assigned courses
- [ ] **Create New Competency** form is at the top
- [ ] Select your course, enter a code (e.g., "TEST-01"), name, and description
- [ ] Click **Create Competency** — success message, new competency appears in the table
- [ ] Click the delete icon on a competency you created — confirmation, then deleted
- [ ] Try creating a competency without a name — validation error

#### C7. Lecturer Resource Status Visibility
- [ ] Go to My Resources — your uploaded resources show their approval status
- [ ] Seed data resources (uploaded by lecturer1) should show as "Approved"
- [ ] Newly uploaded resources show as "Pending"

---

### Section D: Admin Features

Log out, then log in as `admin`.

#### D1. Admin Dashboard
- [ ] Dashboard shows stat cards: Users (5), Courses (3), Competencies (8), Resources (6+)
- [ ] If any resources are pending, a yellow warning banner is shown with "Review now" link
- [ ] Quick links: Review Resources, Manage Users, Manage Courses, Manage Competencies, System Activity Log, Database Backup

#### D2. Review & Approve Resources
- [ ] Click **Review** in nav or "Review Resources" on dashboard
- [ ] Pending Approval section shows resources with status "pending"
- [ ] Each pending resource shows: title, type, course/competency, uploader, date
- [ ] Click **Approve** — resource moves from pending to approved
- [ ] Click **Reject** — confirmation dialog, then resource status changes to rejected
- [ ] All Resources table at the bottom shows ALL resources with their status
- [ ] Approved = green badge, Pending = yellow badge, Rejected = red badge

#### D3. Verify Student Sees Only Approved Resources
- [ ] Log out, log in as `student1`
- [ ] Navigate to a competency that had a newly approved resource — it should appear
- [ ] Navigate to a competency with a pending resource — it should NOT appear
- [ ] Log out, log in as `admin`, reject a resource
- [ ] Log out, log in as `student1`, verify the rejected resource is gone

#### D4. Manage Users
- [ ] Click **Users** in nav
- [ ] Table shows all users with name, username, email, role, created date
- [ ] **Create New User** form at top
- [ ] Fill in: Full Name, Email, Username, Role (select Lecturer), Password
- [ ] Click **Create User** — success message, new user appears in table
- [ ] Click the edit icon on a user — form populates with their data
- [ ] Change the role and click **Update User** — success message
- [ ] Click delete on a user (not yourself) — confirmation, then user removed
- [ ] Try creating a user with an existing username — error shown
- [ ] Try creating a user with a short password — error shown
- [ ] Verify you cannot delete your own admin account (no delete button on your row)

#### D5. Manage Courses
- [ ] Click **Courses** in nav
- [ ] Table shows all courses with code, name, competency count, enrollment count
- [ ] **Create New Course** form at top
- [ ] Enter: Code (e.g., "BIT4101"), Name, Description
- [ ] Click **Create Course** — success message, course appears in table
- [ ] Edit a course — change the name, save — verify change
- [ ] Delete a course — confirmation dialog, course removed
- [ ] Try creating a course with a duplicate code — error shown

#### D6. Manage Competencies
- [ ] Click **Competencies** in nav
- [ ] Table shows all competencies grouped by course
- [ ] **Create New Competency** form at top with course dropdown
- [ ] Create a new competency — success message, appears in table
- [ ] Edit a competency — change name, save — verify change
- [ ] Delete a competency — confirmation, removed from table

#### D7. System Activity Log
- [ ] Click **Activity Log** in nav
- [ ] Shows a table of all logged activities
- [ ] Columns: Date/Time, User, Role, Action, Details, IP Address
- [ ] Filter by date range — change the "From" and "To" dates, click Filter
- [ ] Filter by action type — select "login" from dropdown, click Filter
- [ ] Verify your recent login appears in the log
- [ ] Verify resource uploads, approvals, and deletes are logged

#### D8. Database Backup
- [ ] Click **Database Backup** in nav (or link on dashboard)
- [ ] Shows database statistics (row counts per table)
- [ ] Shows system info (PHP version, MySQL version)
- [ ] Click **Download SQL Backup** — browser downloads a `.sql` file
- [ ] Open the downloaded file — verify it contains CREATE TABLE and INSERT statements
- [ ] Verify the backup contains all tables (users, courses, competencies, resources, enrollments, lecturer_courses, activity_log)

---

### Section E: Security Checks

Test these across all roles.

#### E1. CSRF Protection
- [ ] Open the Upload Resource form in a new tab
- [ ] View page source — verify a `csrf_token` hidden field exists
- [ ] Try submitting a form with a tampered/missing CSRF token — should fail

#### E2. SQL Injection
- [ ] In the student search, type: `' OR 1=1 --` — should return no results (not dump the database)
- [ ] In the login form, try username: `admin' OR '1'='1` — should fail login

#### E3. XSS Prevention
- [ ] Create a course with name: `<script>alert('XSS')</script>`
- [ ] Verify the name is displayed as plain text, not executed as a script
- [ ] Check all pages where that course name appears — same result

#### E4. Session Security
- [ ] Log in, then copy the session cookie
- [ ] Log out — the session cookie should be cleared
- [ ] Try using the old cookie — should not work (session was regenerated on login)

---

### Section F: UI/UX Checks

#### F1. Responsive Design
- [ ] Resize browser to mobile width (<768px) — navbar collapses to hamburger menu
- [ ] Click the hamburger icon — nav menu expands
- [ ] All pages display correctly on mobile width (no horizontal scroll)
- [ ] Cards stack vertically on small screens

#### F2. Navigation
- [ ] Every nav link goes to a working page (no 404s)
- [ ] Breadcrumb navigation works on student pages
- [ ] "Cancel" buttons return to the previous page
- [ ] Flash messages appear after actions and auto-dismiss after 5 seconds

#### F3. Data Consistency
- [ ] Seed data matches across pages (3 courses, 8 competencies, 6+ resources)
- [ ] Enrolled courses show correct competency/resource counts
- [ ] Resource counts on dashboards match the actual data

---

## Part 3: Test Results

After completing all sections, record your results here:

| Section | Tests | Passed | Failed | Notes |
|---------|-------|--------|--------|-------|
| A: Authentication | 20 | | | |
| B: Student Features | 22 | | | |
| C: Lecturer Features | 18 | | | |
| D: Admin Features | 24 | | | |
| E: Security | 6 | | | |
| F: UI/UX | 8 | | | |
| **Total** | **98** | | | |

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "Database connection failed" | Check `config/database.php` credentials match your MySQL setup |
| Blank page | Enable error reporting: add `error_reporting(E_ALL); ini_set('display_errors', 1);` at top of the file |
| 404 errors on pages | Make sure you ran `migrations.sql` and the folder name in the URL matches |
| "Table doesn't exist" | Run `database/schema.sql` — it contains all tables, seed data, and activity log |
| Session errors | Make sure PHP sessions are enabled in `php.ini` (`session.auto_start = 1` or manual start) |
| CSRF token errors | Clear your browser cookies and try again |

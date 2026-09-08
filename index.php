<?php
require_once __DIR__ . '/includes/auth_check.php';

// Redirect logged-in users to their role dashboard
if (is_logged_in()) {
    $role = current_role();
    if ($role === 'student') {
        redirect('/student/dashboard.php');
    } elseif ($role === 'lecturer') {
        redirect('/lecturer/dashboard.php');
    } elseif ($role === 'admin') {
        redirect('/admin/dashboard.php');
    }
}

$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';
?>

<div class="row align-items-center py-5">
    <div class="col-lg-7">
        <h1 class="display-5 fw-bold mb-3">
            Personalized Learning Resources<br>
            <span class="text-primary">for Competency-Based Education</span>
        </h1>
        <p class="lead text-muted">
            Find the exact learning materials you need for the specific competency you are studying.
            No more searching through bulk course folders.
        </p>
        <div class="d-flex gap-2 mt-4">
            <a href="/auth/login.php" class="btn btn-primary btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Login
            </a>
            <a href="/auth/register.php" class="btn btn-outline-primary btn-lg">
                Register as Student
            </a>
        </div>
    </div>
    <div class="col-lg-5 text-center">
        <i class="bi bi-journal-bookmark-fill display-1 text-primary opacity-75"></i>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-funnel-fill fs-1 text-primary mb-3"></i>
                <h5>Competency-Linked</h5>
                <p class="text-muted small mb-0">
                    Resources are explicitly tagged to competencies so you get only what is relevant.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-person-check-fill fs-1 text-success mb-3"></i>
                <h5>Lecturer-Approved</h5>
                <p class="text-muted small mb-0">
                    All materials are uploaded and vetted by your lecturers.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-lightning-charge-fill fs-1 text-warning mb-3"></i>
                <h5>Fast & Simple</h5>
                <p class="text-muted small mb-0">
                    Select course → select competency → get resources instantly.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

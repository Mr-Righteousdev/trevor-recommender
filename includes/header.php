<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title ?? 'CBC Resource Recommender') ?> | St. Lawrence University</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/index.php">
            <i class="bi bi-mortarboard-fill me-2"></i>CBC Recommender
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <?php if (is_logged_in()): ?>
                    <?php if (current_role() === 'student'): ?>
                        <li class="nav-item"><a class="nav-link" href="/student/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/student/courses.php">My Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="/student/search.php">Search</a></li>
                    <?php elseif (current_role() === 'lecturer'): ?>
                        <li class="nav-item"><a class="nav-link" href="/lecturer/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/lecturer/upload_resource.php">Upload Resource</a></li>
                        <li class="nav-item"><a class="nav-link" href="/lecturer/my_resources.php">My Resources</a></li>
                        <li class="nav-item"><a class="nav-link" href="/lecturer/manage_courses.php">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="/lecturer/manage_competencies.php">Competencies</a></li>
                    <?php elseif (current_role() === 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/approve_resources.php">Review</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/users.php">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/courses.php">Courses</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/competencies.php">Competencies</a></li>
                        <li class="nav-item"><a class="nav-link" href="/admin/activity_log.php">Activity Log</a></li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if (is_logged_in()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            <?= e($_SESSION['full_name'] ?? $_SESSION['username']) ?>
                            <span class="badge bg-light text-primary ms-1"><?= e(ucfirst(current_role())) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/auth/login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="/auth/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?php
    $flash = get_flash();
    if ($flash):
    ?>
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

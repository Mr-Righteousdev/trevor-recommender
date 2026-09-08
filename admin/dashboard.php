<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');

$pdo = getPDO();

$stats = [
    'users'        => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'courses'      => $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
    'competencies' => $pdo->query('SELECT COUNT(*) FROM competencies')->fetchColumn(),
    'resources'    => $pdo->query('SELECT COUNT(*) FROM resources')->fetchColumn(),
    'pending'      => $pdo->query("SELECT COUNT(*) FROM resources WHERE status = 'pending'")->fetchColumn(),
];

$page_title = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">Admin Dashboard</h2>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body text-center">
                <h3><?= (int)$stats['users'] ?></h3>
                <small>Users</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body text-center">
                <h3><?= (int)$stats['courses'] ?></h3>
                <small>Courses</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-info">
            <div class="card-body text-center">
                <h3><?= (int)$stats['competencies'] ?></h3>
                <small>Competencies</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body text-center">
                <h3><?= (int)$stats['resources'] ?></h3>
                <small>Resources</small>
            </div>
        </div>
    </div>
</div>

<?php if ((int)$stats['pending'] > 0): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>
            <strong><?= (int)$stats['pending'] ?> resource(s) pending approval.</strong>
            <a href="approve_resources.php" class="alert-link ms-2">Review now</a>
        </div>
    </div>
<?php endif; ?>

<div class="list-group mb-4">
    <a href="approve_resources.php" class="list-group-item list-group-item-action">
        <i class="bi bi-check2-square me-2"></i> Review Resources
        <?php if ((int)$stats['pending'] > 0): ?>
            <span class="badge bg-warning text-dark float-end"><?= (int)$stats['pending'] ?> pending</span>
        <?php endif; ?>
    </a>
    <a href="users.php" class="list-group-item list-group-item-action">
        <i class="bi bi-people me-2"></i> Manage Users
    </a>
    <a href="courses.php" class="list-group-item list-group-item-action">
        <i class="bi bi-journal-bookmark me-2"></i> Manage Courses
    </a>
    <a href="competencies.php" class="list-group-item list-group-item-action">
        <i class="bi bi-list-check me-2"></i> Manage Competencies
    </a>
    <a href="activity_log.php" class="list-group-item list-group-item-action">
        <i class="bi bi-clock-history me-2"></i> System Activity Log
    </a>
    <a href="backup.php" class="list-group-item list-group-item-action">
        <i class="bi bi-cloud-download me-2"></i> Database Backup
    </a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

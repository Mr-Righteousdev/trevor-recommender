<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('lecturer');

$pdo = getPDO();
$user_id = $_SESSION['user_id'];

// Courses assigned to this lecturer
$stmt = $pdo->prepare('
    SELECT c.course_id, c.course_code, c.course_name
    FROM courses c
    JOIN lecturer_courses lc ON lc.course_id = c.course_id
    WHERE lc.user_id = ?
    ORDER BY c.course_code
');
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

// Count resources uploaded by this lecturer
$stmt = $pdo->prepare('SELECT COUNT(*) FROM resources WHERE uploaded_by = ?');
$stmt->execute([$user_id]);
$resource_count = $stmt->fetchColumn();

$page_title = 'Lecturer Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-1">Welcome, <?= e($_SESSION['full_name']) ?></h2>
<p class="text-muted mb-4">Manage learning resources for your courses.</p>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body">
                <h3 class="mb-0"><?= count($courses) ?></h3>
                <small>Assigned Courses</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card bg-success text-white">
            <div class="card-body">
                <h3 class="mb-0"><?= (int)$resource_count ?></h3>
                <small>Resources Uploaded</small>
            </div>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mb-4">
    <a href="upload_resource.php" class="btn btn-primary">
        <i class="bi bi-cloud-upload me-1"></i> Upload New Resource
    </a>
    <a href="my_resources.php" class="btn btn-outline-primary">
        <i class="bi bi-collection me-1"></i> My Resources
    </a>
    <a href="manage_courses.php" class="btn btn-outline-primary">
        <i class="bi bi-journal-bookmark me-1"></i> Manage Courses
    </a>
    <a href="manage_competencies.php" class="btn btn-outline-primary">
        <i class="bi bi-list-check me-1"></i> Manage Competencies
    </a>
</div>

<h5>Your Courses</h5>
<?php if (empty($courses)): ?>
    <div class="alert alert-info">You have not been assigned to any courses yet.</div>
<?php else: ?>
    <ul class="list-group">
        <?php foreach ($courses as $c): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>
                    <strong><?= e($c['course_code']) ?></strong> – <?= e($c['course_name']) ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('student');

$course_id = (int)($_GET['course_id'] ?? 0);
if ($course_id <= 0) {
    set_flash('danger', 'Invalid course selected.');
    redirect('/student/dashboard.php');
}

$pdo = getPDO();

// Get course
$stmt = $pdo->prepare('SELECT course_id, course_code, course_name FROM courses WHERE course_id = ?');
$stmt->execute([$course_id]);
$course = $stmt->fetch();

if (!$course) {
    set_flash('danger', 'Course not found.');
    redirect('/student/dashboard.php');
}

// Get competencies
$stmt = $pdo->prepare('
    SELECT competency_id, competency_code, competency_name, description
    FROM competencies
    WHERE course_id = ?
    ORDER BY competency_code, competency_name
');
$stmt->execute([$course_id]);
$competencies = $stmt->fetchAll();

$page_title = 'Competencies – ' . $course['course_code'];
require_once __DIR__ . '/../includes/header.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active"><?= e($course['course_code']) ?></li>
    </ol>
</nav>

<h2 class="mb-1"><?= e($course['course_name']) ?></h2>
<p class="text-muted mb-4">Select a competency to see recommended learning resources.</p>

<?php if (empty($competencies)): ?>
    <div class="alert alert-warning">
        No competencies have been defined for this course yet.
    </div>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($competencies as $comp): ?>
            <a href="resources.php?competency_id=<?= (int)$comp['competency_id'] ?>"
               class="list-group-item list-group-item-action competency-card py-3">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">
                        <?php if ($comp['competency_code']): ?>
                            <span class="badge bg-secondary me-2"><?= e($comp['competency_code']) ?></span>
                        <?php endif; ?>
                        <?= e($comp['competency_name']) ?>
                    </h5>
                    <i class="bi bi-chevron-right"></i>
                </div>
                <?php if (!empty($comp['description'])): ?>
                    <p class="mb-0 text-muted small"><?= e($comp['description']) ?></p>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

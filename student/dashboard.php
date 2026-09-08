<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('student');

$pdo = getPDO();
$user_id = $_SESSION['user_id'];

// Get enrolled courses (or all courses if no enrollments)
$stmt = $pdo->prepare('
    SELECT c.course_id, c.course_code, c.course_name, c.description
    FROM courses c
    INNER JOIN enrollments e ON e.course_id = c.course_id
    WHERE e.user_id = ?
    ORDER BY c.course_code
');
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

// Fallback: show all courses if student has no enrollments
if (empty($courses)) {
    $courses = $pdo->query('SELECT course_id, course_code, course_name, description FROM courses ORDER BY course_code')->fetchAll();
}

$page_title = 'Student Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Welcome, <?= e($_SESSION['full_name']) ?></h2>
        <p class="text-muted mb-0">Select a course to view its competencies and recommended resources.</p>
    </div>
</div>

<?php if (empty($courses)): ?>
    <div class="alert alert-info">
        No courses available yet. Please contact your administrator.
    </div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($courses as $course): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <span class="badge bg-primary mb-2"><?= e($course['course_code']) ?></span>
                        <h5 class="card-title"><?= e($course['course_name']) ?></h5>
                        <p class="card-text text-muted small">
                            <?= e(mb_strimwidth($course['description'] ?? '', 0, 100, '...')) ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="competencies.php?course_id=<?= (int)$course['course_id'] ?>"
                           class="btn btn-outline-primary btn-sm w-100">
                            View Competencies <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

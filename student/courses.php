<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('student');

$pdo = getPDO();
$user_id = $_SESSION['user_id'];

// Handle enroll / unenroll actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Invalid request. Please try again.');
        redirect('/student/courses.php');
    }

    $action = $_POST['action'] ?? '';
    $course_id = (int)($_POST['course_id'] ?? 0);

    if ($course_id > 0) {
        if ($action === 'enroll') {
            $stmt = $pdo->prepare('INSERT IGNORE INTO enrollments (user_id, course_id) VALUES (?, ?)');
            $stmt->execute([$user_id, $course_id]);
            set_flash('success', 'You have enrolled in the course.');
        } elseif ($action === 'unenroll') {
            $stmt = $pdo->prepare('DELETE FROM enrollments WHERE user_id = ? AND course_id = ?');
            $stmt->execute([$user_id, $course_id]);
            set_flash('success', 'You have been unenrolled from the course.');
        }
    }
    redirect('/student/courses.php');
}

// Get enrolled course IDs
$stmt = $pdo->prepare('SELECT course_id FROM enrollments WHERE user_id = ?');
$stmt->execute([$user_id]);
$enrolled_ids = array_column($stmt->fetchAll(), 'course_id');

// Get all courses
$courses = $pdo->query('SELECT course_id, course_code, course_name, description FROM courses ORDER BY course_code')->fetchAll();

// Get resource count per course
$resource_counts = $pdo->query('
    SELECT c.course_id, COUNT(r.resource_id) AS res_count
    FROM courses c
    LEFT JOIN competencies comp ON comp.course_id = c.course_id
    LEFT JOIN resources r ON r.competency_id = comp.competency_id
    GROUP BY c.course_id
')->fetchAll(PDO::FETCH_KEY_PAIR);

// Get competency count per course
$comp_counts = $pdo->query('
    SELECT course_id, COUNT(*) AS comp_count
    FROM competencies
    GROUP BY course_id
')->fetchAll(PDO::FETCH_KEY_PAIR);

$page_title = 'My Courses';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-1">All Courses</h2>
<p class="text-muted mb-4">Enroll in courses to access their competencies and resources.</p>

<?php if (empty($courses)): ?>
    <div class="alert alert-info">No courses available yet.</div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($courses as $course): ?>
            <?php $is_enrolled = in_array($course['course_id'], $enrolled_ids); ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm <?= $is_enrolled ? 'border-success' : '' ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary"><?= e($course['course_code']) ?></span>
                            <?php if ($is_enrolled): ?>
                                <span class="badge bg-success">Enrolled</span>
                            <?php endif; ?>
                        </div>
                        <h5 class="card-title"><?= e($course['course_name']) ?></h5>
                        <p class="card-text text-muted small">
                            <?= e(mb_strimwidth($course['description'] ?? '', 0, 100, '...')) ?>
                        </p>
                        <div class="d-flex gap-3 text-muted small mb-3">
                            <span><i class="bi bi-list-check me-1"></i><?= (int)($comp_counts[$course['course_id']] ?? 0) ?> competencies</span>
                            <span><i class="bi bi-collection me-1"></i><?= (int)($resource_counts[$course['course_id']] ?? 0) ?> resources</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <?php if ($is_enrolled): ?>
                            <a href="competencies.php?course_id=<?= (int)$course['course_id'] ?>"
                               class="btn btn-primary btn-sm flex-fill">
                                View Competencies <i class="bi bi-arrow-right"></i>
                            </a>
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action" value="unenroll">
                                <input type="hidden" name="course_id" value="<?= (int)$course['course_id'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                        onclick="return confirm('Unenroll from this course?')">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" class="w-100">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action" value="enroll">
                                <input type="hidden" name="course_id" value="<?= (int)$course['course_id'] ?>">
                                <button type="submit" class="btn btn-outline-success btn-sm w-100">
                                    <i class="bi bi-plus-lg me-1"></i> Enroll
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

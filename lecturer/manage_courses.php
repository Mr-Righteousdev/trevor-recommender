<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('lecturer');

$pdo = getPDO();
$user_id = $_SESSION['user_id'];
$errors = [];

// Handle create / delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'assign') {
            $course_id = (int)($_POST['course_id'] ?? 0);
            if ($course_id <= 0) {
                $errors[] = 'Please select a course.';
            } else {
                $stmt = $pdo->prepare('INSERT IGNORE INTO lecturer_courses (user_id, course_id) VALUES (?, ?)');
                $stmt->execute([$user_id, $course_id]);
                log_activity('lecturer_assign_course', 'Assigned to course ID: ' . $course_id);
                set_flash('success', 'Course assigned successfully.');
                redirect('/lecturer/manage_courses.php');
            }
        } elseif ($action === 'unassign') {
            $course_id = (int)($_POST['course_id'] ?? 0);
            if ($course_id > 0) {
                $stmt = $pdo->prepare('DELETE FROM lecturer_courses WHERE user_id = ? AND course_id = ?');
                $stmt->execute([$user_id, $course_id]);
                log_activity('lecturer_unassign_course', 'Unassigned from course ID: ' . $course_id);
                set_flash('success', 'Course unassigned.');
                redirect('/lecturer/manage_courses.php');
            }
        }
    }
}

// Get assigned courses
$stmt = $pdo->prepare('
    SELECT c.course_id, c.course_code, c.course_name,
           (SELECT COUNT(*) FROM competencies WHERE course_id = c.course_id) AS comp_count
    FROM courses c
    JOIN lecturer_courses lc ON lc.course_id = c.course_id
    WHERE lc.user_id = ?
    ORDER BY c.course_code
');
$stmt->execute([$user_id]);
$assigned = $stmt->fetchAll();

// Get all courses (for assignment dropdown)
$all_courses = $pdo->query('SELECT course_id, course_code, course_name FROM courses ORDER BY course_code')->fetchAll();

// Filter out already assigned courses
$assigned_ids = array_column($assigned, 'course_id');
$available = array_filter($all_courses, fn($c) => !in_array($c['course_id'], $assigned_ids));

$page_title = 'Manage My Courses';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">Manage My Courses</h2>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Assign Course -->
<?php if (!empty($available)): ?>
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Assign a Course</h5>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3 align-items-end">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="assign">
            <div class="col-md-9">
                <label class="form-label">Select Course</label>
                <select name="course_id" class="form-select" required>
                    <option value="">-- Choose a course --</option>
                    <?php foreach ($available as $co): ?>
                        <option value="<?= (int)$co['course_id'] ?>">
                            <?= e($co['course_code']) ?> – <?= e($co['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-plus-lg me-1"></i> Assign
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Assigned Courses -->
<h5 class="mb-3">Your Assigned Courses</h5>
<?php if (empty($assigned)): ?>
    <div class="alert alert-info">You are not assigned to any courses yet.</div>
<?php else: ?>
    <div class="row g-3">
        <?php foreach ($assigned as $c): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <span class="badge bg-primary mb-2"><?= e($c['course_code']) ?></span>
                        <h5 class="card-title"><?= e($c['course_name']) ?></h5>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-list-check me-1"></i><?= (int)$c['comp_count'] ?> competencies
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <form method="POST" class="d-inline"
                              onsubmit="return confirm('Unassign from this course? You will lose access to manage its resources.')">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="unassign">
                            <input type="hidden" name="course_id" value="<?= (int)$c['course_id'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-x-lg me-1"></i> Unassign
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

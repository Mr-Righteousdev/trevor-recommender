<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');

$pdo = getPDO();
$errors = [];
$editing_course = null;

// Handle create / update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $action      = $_POST['action'] ?? '';
        $course_code = trim($_POST['course_code'] ?? '');
        $course_name = trim($_POST['course_name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($course_code === '') $errors[] = 'Course code is required.';
        if ($course_name === '') $errors[] = 'Course name is required.';

        if (empty($errors)) {
            if ($action === 'create') {
                $stmt = $pdo->prepare('SELECT course_id FROM courses WHERE course_code = ?');
                $stmt->execute([$course_code]);
                if ($stmt->fetch()) {
                    $errors[] = 'Course code already exists.';
                } else {
                    $stmt = $pdo->prepare('INSERT INTO courses (course_code, course_name, description) VALUES (?, ?, ?)');
                    $stmt->execute([$course_code, $course_name, $description]);
                    set_flash('success', 'Course created successfully.');
                    redirect('/admin/courses.php');
                }
            } elseif ($action === 'update') {
                $edit_id = (int)($_POST['edit_id'] ?? 0);
                if ($edit_id <= 0) {
                    $errors[] = 'Invalid course.';
                } else {
                    $stmt = $pdo->prepare('SELECT course_id FROM courses WHERE course_code = ? AND course_id != ?');
                    $stmt->execute([$course_code, $edit_id]);
                    if ($stmt->fetch()) {
                        $errors[] = 'Course code already exists.';
                    } else {
                        $stmt = $pdo->prepare('UPDATE courses SET course_code = ?, course_name = ?, description = ? WHERE course_id = ?');
                        $stmt->execute([$course_code, $course_name, $description, $edit_id]);
                        set_flash('success', 'Course updated successfully.');
                        redirect('/admin/courses.php');
                    }
                }
            }
        }
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Invalid request.');
        redirect('/admin/courses.php');
    }
    $del_id = (int)($_POST['course_id'] ?? 0);
    if ($del_id > 0) {
        $stmt = $pdo->prepare('DELETE FROM courses WHERE course_id = ?');
        $stmt->execute([$del_id]);
        set_flash('success', 'Course deleted.');
    }
    redirect('/admin/courses.php');
}

// Fetch course for edit form
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE course_id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing_course = $stmt->fetch();
}

$courses = $pdo->query('
    SELECT co.*,
           (SELECT COUNT(*) FROM competencies WHERE course_id = co.course_id) AS comp_count,
           (SELECT COUNT(*) FROM enrollments WHERE course_id = co.course_id) AS enroll_count
    FROM courses co
    ORDER BY co.course_code
')->fetchAll();

$page_title = 'Manage Courses';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Manage Courses</h2>
    <a href="#create-form" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add Course
    </a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Create / Edit Form -->
<div class="card shadow-sm mb-4" id="create-form">
    <div class="card-header">
        <h5 class="mb-0"><?= $editing_course ? 'Edit Course' : 'Create New Course' ?></h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="<?= $editing_course ? 'update' : 'create' ?>">
            <?php if ($editing_course): ?>
                <input type="hidden" name="edit_id" value="<?= (int)$editing_course['course_id'] ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Course Code <span class="text-danger">*</span></label>
                    <input type="text" name="course_code" class="form-control"
                           value="<?= e($_POST['course_code'] ?? $editing_course['course_code'] ?? '') ?>" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Course Name <span class="text-danger">*</span></label>
                    <input type="text" name="course_name" class="form-control"
                           value="<?= e($_POST['course_name'] ?? $editing_course['course_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control"
                           value="<?= e($_POST['description'] ?? $editing_course['description'] ?? '') ?>">
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> <?= $editing_course ? 'Update Course' : 'Create Course' ?>
                </button>
                <?php if ($editing_course): ?>
                    <a href="courses.php" class="btn btn-outline-secondary">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Courses Table -->
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Description</th>
                <th>Competencies</th>
                <th>Enrollments</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $c): ?>
                <tr>
                    <td><span class="badge bg-primary"><?= e($c['course_code']) ?></span></td>
                    <td><?= e($c['course_name']) ?></td>
                    <td class="text-muted small"><?= e(mb_strimwidth($c['description'] ?? '', 0, 60, '...')) ?></td>
                    <td class="text-center"><?= (int)$c['comp_count'] ?></td>
                    <td class="text-center"><?= (int)$c['enroll_count'] ?></td>
                    <td class="text-end">
                        <a href="?edit=<?= (int)$c['course_id'] ?>#create-form"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this course and all its competencies and resources?')">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="course_id" value="<?= (int)$c['course_id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');

$pdo = getPDO();
$errors = [];
$editing_comp = null;

// Get courses for dropdown
$all_courses = $pdo->query('SELECT course_id, course_code, course_name FROM courses ORDER BY course_code')->fetchAll();

// Handle create / update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $action           = $_POST['action'] ?? '';
        $course_id        = (int)($_POST['course_id'] ?? 0);
        $competency_code  = trim($_POST['competency_code'] ?? '');
        $competency_name  = trim($_POST['competency_name'] ?? '');
        $description      = trim($_POST['description'] ?? '');

        if ($course_id <= 0) $errors[] = 'Please select a course.';
        if ($competency_name === '') $errors[] = 'Competency name is required.';

        if (empty($errors)) {
            if ($action === 'create') {
                $stmt = $pdo->prepare('INSERT INTO competencies (course_id, competency_code, competency_name, description) VALUES (?, ?, ?, ?)');
                $stmt->execute([$course_id, $competency_code, $competency_name, $description]);
                set_flash('success', 'Competency created successfully.');
                redirect('/admin/competencies.php');
            } elseif ($action === 'update') {
                $edit_id = (int)($_POST['edit_id'] ?? 0);
                if ($edit_id <= 0) {
                    $errors[] = 'Invalid competency.';
                } else {
                    $stmt = $pdo->prepare('UPDATE competencies SET course_id = ?, competency_code = ?, competency_name = ?, description = ? WHERE competency_id = ?');
                    $stmt->execute([$course_id, $competency_code, $competency_name, $description, $edit_id]);
                    set_flash('success', 'Competency updated successfully.');
                    redirect('/admin/competencies.php');
                }
            }
        }
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Invalid request.');
        redirect('/admin/competencies.php');
    }
    $del_id = (int)($_POST['competency_id'] ?? 0);
    if ($del_id > 0) {
        $stmt = $pdo->prepare('DELETE FROM competencies WHERE competency_id = ?');
        $stmt->execute([$del_id]);
        set_flash('success', 'Competency deleted.');
    }
    redirect('/admin/competencies.php');
}

// Fetch competency for edit form
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM competencies WHERE competency_id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing_comp = $stmt->fetch();
}

$competencies = $pdo->query('
    SELECT comp.*, co.course_code, co.course_name,
           (SELECT COUNT(*) FROM resources WHERE competency_id = comp.competency_id) AS res_count
    FROM competencies comp
    JOIN courses co ON co.course_id = comp.course_id
    ORDER BY co.course_code, comp.competency_code
')->fetchAll();

$page_title = 'Manage Competencies';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Manage Competencies</h2>
    <a href="#create-form" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add Competency
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
        <h5 class="mb-0"><?= $editing_comp ? 'Edit Competency' : 'Create New Competency' ?></h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="<?= $editing_comp ? 'update' : 'create' ?>">
            <?php if ($editing_comp): ?>
                <input type="hidden" name="edit_id" value="<?= (int)$editing_comp['competency_id'] ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Course <span class="text-danger">*</span></label>
                    <select name="course_id" class="form-select" required>
                        <option value="">-- Select Course --</option>
                        <?php
                        $selected_course = $_POST['course_id'] ?? $editing_comp['course_id'] ?? '';
                        foreach ($all_courses as $co):
                        ?>
                            <option value="<?= (int)$co['course_id'] ?>"
                                <?= $selected_course == $co['course_id'] ? 'selected' : '' ?>>
                                <?= e($co['course_code']) ?> – <?= e($co['course_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Code</label>
                    <input type="text" name="competency_code" class="form-control"
                           placeholder="e.g. DB-01"
                           value="<?= e($_POST['competency_code'] ?? $editing_comp['competency_code'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Competency Name <span class="text-danger">*</span></label>
                    <input type="text" name="competency_name" class="form-control"
                           value="<?= e($_POST['competency_name'] ?? $editing_comp['competency_name'] ?? '') ?>" required>
                </div>
            </div>
            <div class="row g-3 mt-1">
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control"
                           value="<?= e($_POST['description'] ?? $editing_comp['description'] ?? '') ?>">
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> <?= $editing_comp ? 'Update Competency' : 'Create Competency' ?>
                </button>
                <?php if ($editing_comp): ?>
                    <a href="competencies.php" class="btn btn-outline-secondary">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Competencies Table -->
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Course</th>
                <th>Code</th>
                <th>Competency Name</th>
                <th>Description</th>
                <th>Resources</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($competencies as $comp): ?>
                <tr>
                    <td><span class="badge bg-primary"><?= e($comp['course_code']) ?></span></td>
                    <td><?= e($comp['competency_code'] ?: '–') ?></td>
                    <td><?= e($comp['competency_name']) ?></td>
                    <td class="text-muted small"><?= e(mb_strimwidth($comp['description'] ?? '', 0, 50, '...')) ?></td>
                    <td class="text-center"><?= (int)$comp['res_count'] ?></td>
                    <td class="text-end">
                        <a href="?edit=<?= (int)$comp['competency_id'] ?>#create-form"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this competency and all its resources?')">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="competency_id" value="<?= (int)$comp['competency_id'] ?>">
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

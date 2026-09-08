<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('lecturer');

$pdo = getPDO();
$user_id = $_SESSION['user_id'];
$errors = [];

$resource_id = (int)($_GET['resource_id'] ?? $_POST['resource_id'] ?? 0);
if ($resource_id <= 0) {
    set_flash('danger', 'Invalid resource.');
    redirect('/lecturer/my_resources.php');
}

// Verify ownership
$stmt = $pdo->prepare('SELECT * FROM resources WHERE resource_id = ? AND uploaded_by = ?');
$stmt->execute([$resource_id, $user_id]);
$resource = $stmt->fetch();

if (!$resource) {
    set_flash('danger', 'Resource not found or access denied.');
    redirect('/lecturer/my_resources.php');
}

// Get courses assigned to this lecturer
$stmt = $pdo->prepare('
    SELECT c.course_id, c.course_code, c.course_name
    FROM courses c
    JOIN lecturer_courses lc ON lc.course_id = c.course_id
    WHERE lc.user_id = ?
    ORDER BY c.course_code
');
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll();

// Get all competencies for the lecturer's courses
$course_ids = array_column($courses, 'course_id');
$competencies = [];
if (!empty($course_ids)) {
    $placeholders = implode(',', array_fill(0, count($course_ids), '?'));
    $stmt = $pdo->prepare("
        SELECT c.competency_id, c.competency_code, c.competency_name, co.course_code
        FROM competencies c
        JOIN courses co ON co.course_id = c.course_id
        WHERE c.course_id IN ($placeholders)
        ORDER BY co.course_code, c.competency_code
    ");
    $stmt->execute($course_ids);
    $competencies = $stmt->fetchAll();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $competency_id = (int)($_POST['competency_id'] ?? 0);
        $title         = trim($_POST['resource_title'] ?? '');
        $type          = $_POST['resource_type'] ?? '';
        $url           = trim($_POST['resource_url'] ?? '');
        $description   = trim($_POST['description'] ?? '');

        $allowed_types = ['link', 'pdf', 'video', 'document'];

        if ($competency_id <= 0) $errors[] = 'Please select a competency.';
        if ($title === '') $errors[] = 'Title is required.';
        if (!in_array($type, $allowed_types, true)) $errors[] = 'Invalid resource type.';
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) $errors[] = 'A valid URL is required.';

        if (empty($errors)) {
            $stmt = $pdo->prepare('
                UPDATE resources
                SET competency_id = ?, resource_title = ?, resource_type = ?, resource_url = ?, description = ?
                WHERE resource_id = ? AND uploaded_by = ?
            ');
            $stmt->execute([$competency_id, $title, $type, $url, $description, $resource_id, $user_id]);
            log_activity('resource_update', 'Updated resource ID: ' . $resource_id);
            set_flash('success', 'Resource updated successfully!');
            redirect('/lecturer/my_resources.php');
        }
    }
}

$page_title = 'Edit Resource';
require_once __DIR__ . '/../includes/header.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="my_resources.php">My Resources</a></li>
        <li class="breadcrumb-item active">Edit Resource</li>
    </ol>
</nav>

<h2 class="mb-4">Edit Learning Resource</h2>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="resource_id" value="<?= (int)$resource['resource_id'] ?>">

            <div class="mb-3">
                <label class="form-label">Competency <span class="text-danger">*</span></label>
                <select name="competency_id" class="form-select" required>
                    <option value="">-- Select Competency --</option>
                    <?php foreach ($competencies as $comp): ?>
                        <option value="<?= (int)$comp['competency_id'] ?>"
                            <?= ($resource['competency_id'] == $comp['competency_id']) ? 'selected' : '' ?>>
                            <?= e($comp['course_code']) ?> –
                            <?= e($comp['competency_code'] ? $comp['competency_code'] . ': ' : '') ?>
                            <?= e($comp['competency_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Resource Title <span class="text-danger">*</span></label>
                <input type="text" name="resource_title" class="form-control"
                       value="<?= e($_POST['resource_title'] ?? $resource['resource_title']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Resource Type <span class="text-danger">*</span></label>
                <select name="resource_type" class="form-select" required>
                    <?php
                    $current_type = $_POST['resource_type'] ?? $resource['resource_type'];
                    $types = ['link' => 'Link / Website', 'pdf' => 'PDF', 'video' => 'Video', 'document' => 'Document'];
                    foreach ($types as $val => $label):
                    ?>
                        <option value="<?= $val ?>" <?= $current_type === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Resource URL <span class="text-danger">*</span></label>
                <input type="url" name="resource_url" class="form-control"
                       placeholder="https://..."
                       value="<?= e($_POST['resource_url'] ?? $resource['resource_url']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description (optional)</label>
                <textarea name="description" class="form-control" rows="3"><?= e($_POST['description'] ?? $resource['description'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Save Changes
            </button>
            <a href="my_resources.php" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

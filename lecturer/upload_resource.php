<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('lecturer');

$pdo = getPDO();
$user_id = $_SESSION['user_id'];
$errors = [];

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

// Handle form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $competency_id  = (int)($_POST['competency_id'] ?? 0);
        $title          = trim($_POST['resource_title'] ?? '');
        $type           = $_POST['resource_type'] ?? '';
        $url            = trim($_POST['resource_url'] ?? '');
        $description    = trim($_POST['description'] ?? '');

        $allowed_types = ['link', 'pdf', 'video', 'document'];

        if ($competency_id <= 0) $errors[] = 'Please select a competency.';
        if ($title === '') $errors[] = 'Title is required.';
        if (!in_array($type, $allowed_types, true)) $errors[] = 'Invalid resource type.';
        if ($url === '' || !filter_var($url, FILTER_VALIDATE_URL)) $errors[] = 'A valid URL is required.';

        if (empty($errors)) {
            $stmt = $pdo->prepare('
                INSERT INTO resources (competency_id, resource_title, resource_type, resource_url, description, uploaded_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([$competency_id, $title, $type, $url, $description, $user_id, 'pending']);
            log_activity('resource_upload', 'Uploaded resource: ' . $title);
            set_flash('success', 'Resource uploaded and tagged successfully! It is pending approval.');
            redirect('/lecturer/my_resources.php');
        }
    }
}

// For the competency dropdown we need all competencies of the lecturer's courses
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

$page_title = 'Upload Resource';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">Upload Learning Resource</h2>

<?php if ($errors): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= e($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (empty($courses)): ?>
    <div class="alert alert-warning">
        You are not assigned to any courses. Contact the administrator.
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <div class="mb-3">
                    <label class="form-label">Competency <span class="text-danger">*</span></label>
                    <select name="competency_id" class="form-select" required>
                        <option value="">-- Select Competency --</option>
                        <?php foreach ($competencies as $comp): ?>
                            <option value="<?= (int)$comp['competency_id'] ?>"
                                <?= (isset($_POST['competency_id']) && $_POST['competency_id'] == $comp['competency_id']) ? 'selected' : '' ?>>
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
                           value="<?= e($_POST['resource_title'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Resource Type <span class="text-danger">*</span></label>
                    <select name="resource_type" class="form-select" required>
                        <option value="link"     <?= ($_POST['resource_type'] ?? '') === 'link' ? 'selected' : '' ?>>Link / Website</option>
                        <option value="pdf"      <?= ($_POST['resource_type'] ?? '') === 'pdf' ? 'selected' : '' ?>>PDF</option>
                        <option value="video"    <?= ($_POST['resource_type'] ?? '') === 'video' ? 'selected' : '' ?>>Video</option>
                        <option value="document" <?= ($_POST['resource_type'] ?? '') === 'document' ? 'selected' : '' ?>>Document</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Resource URL <span class="text-danger">*</span></label>
                    <input type="url" name="resource_url" class="form-control"
                           placeholder="https://..."
                           value="<?= e($_POST['resource_url'] ?? '') ?>" required>
                    <div class="form-text">Paste the full link to the resource (YouTube, Google Drive, website, etc.)</div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description (optional)</label>
                    <textarea name="description" class="form-control" rows="3"><?= e($_POST['description'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cloud-upload me-1"></i> Upload & Tag Resource
                </button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

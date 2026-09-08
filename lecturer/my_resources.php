<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('lecturer');

$pdo = getPDO();
$user_id = $_SESSION['user_id'];

// Handle delete via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Invalid request. Please try again.');
        redirect('/lecturer/my_resources.php');
    }
    $rid = (int)$_POST['delete_id'];
    $stmt = $pdo->prepare('DELETE FROM resources WHERE resource_id = ? AND uploaded_by = ?');
    $stmt->execute([$rid, $user_id]);
    log_activity('resource_delete', 'Deleted resource ID: ' . $rid);
    set_flash('success', 'Resource deleted.');
    redirect('/lecturer/my_resources.php');
}

$stmt = $pdo->prepare('
    SELECT r.resource_id, r.resource_title, r.resource_type, r.resource_url,
           r.upload_date, r.status, c.competency_name, co.course_code
    FROM resources r
    JOIN competencies c ON c.competency_id = r.competency_id
    JOIN courses co ON co.course_id = c.course_id
    WHERE r.uploaded_by = ?
    ORDER BY r.upload_date DESC
');
$stmt->execute([$user_id]);
$resources = $stmt->fetchAll();

$page_title = 'My Resources';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">My Uploaded Resources</h2>
    <a href="upload_resource.php" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Upload New
    </a>
</div>

<?php if (empty($resources)): ?>
    <div class="alert alert-info">You have not uploaded any resources yet.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Course / Competency</th>
                    <th>Uploaded</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resources as $r): ?>
                    <tr>
                        <td>
                            <a href="<?= e($r['resource_url']) ?>" target="_blank">
                                <?= e($r['resource_title']) ?>
                            </a>
                        </td>
                        <td><span class="badge bg-secondary"><?= e($r['resource_type']) ?></span></td>
                        <td>
                            <?php
                            $status_badges = [
                                'pending'  => 'bg-warning text-dark',
                                'approved' => 'bg-success',
                                'rejected' => 'bg-danger',
                            ];
                            ?>
                            <span class="badge <?= $status_badges[$r['status']] ?? 'bg-secondary' ?>">
                                <?= e(ucfirst($r['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <small><?= e($r['course_code']) ?></small><br>
                            <?= e($r['competency_name']) ?>
                        </td>
                        <td><?= date('d M Y', strtotime($r['upload_date'])) ?></td>
                        <td class="text-end">
                            <a href="edit_resource.php?resource_id=<?= (int)$r['resource_id'] ?>"
                               class="btn btn-sm btn-outline-secondary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this resource?')">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="delete_id" value="<?= (int)$r['resource_id'] ?>">
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
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

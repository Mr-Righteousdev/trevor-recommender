<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');

$pdo = getPDO();

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Invalid request.');
        redirect('/admin/approve_resources.php');
    }

    $action = $_POST['action'] ?? '';
    $resource_id = (int)($_POST['resource_id'] ?? 0);

    if ($resource_id > 0 && in_array($action, ['approve', 'reject'])) {
        $new_status = $action === 'approve' ? 'approved' : 'rejected';
        $stmt = $pdo->prepare('UPDATE resources SET status = ? WHERE resource_id = ?');
        $stmt->execute([$new_status, $resource_id]);
        log_activity('resource_' . $action, 'Resource ID ' . $resource_id . ' ' . $action . 'd by admin');
        set_flash('success', 'Resource ' . $action . 'd.');
    }
    redirect('/admin/approve_resources.php');
}

// Get pending resources
$stmt = $pdo->prepare('
    SELECT r.resource_id, r.resource_title, r.resource_type, r.resource_url,
           r.description, r.upload_date, r.status,
           u.full_name AS uploader_name, u.username,
           c.competency_name, co.course_code
    FROM resources r
    JOIN users u ON u.user_id = r.uploaded_by
    JOIN competencies c ON c.competency_id = r.competency_id
    JOIN courses co ON co.course_id = c.course_id
    WHERE r.status = ?
    ORDER BY r.upload_date DESC
');
$stmt->execute(['pending']);
$pending = $stmt->fetchAll();

// Get all resources for the status view
$all_resources = $pdo->query('
    SELECT r.resource_id, r.resource_title, r.resource_type, r.status,
           r.upload_date, u.full_name AS uploader_name,
           c.competency_name, co.course_code
    FROM resources r
    JOIN users u ON u.user_id = r.uploaded_by
    JOIN competencies c ON c.competency_id = r.competency_id
    JOIN courses co ON co.course_id = c.course_id
    ORDER BY r.status ASC, r.upload_date DESC
')->fetchAll();

$page_title = 'Review Resources';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">Review & Approve Resources</h2>

<!-- Pending Resources -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pending Approval (<?= count($pending) ?>)</h5>
    </div>
    <div class="card-body">
        <?php if (empty($pending)): ?>
            <p class="text-muted mb-0">No resources pending approval.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Course / Competency</th>
                            <th>Uploaded By</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending as $r): ?>
                            <tr>
                                <td>
                                    <a href="<?= e($r['resource_url']) ?>" target="_blank">
                                        <?= e($r['resource_title']) ?>
                                    </a>
                                </td>
                                <td><span class="badge bg-secondary"><?= e($r['resource_type']) ?></span></td>
                                <td>
                                    <small><?= e($r['course_code']) ?></small><br>
                                    <?= e($r['competency_name']) ?>
                                </td>
                                <td>
                                    <?= e($r['uploader_name']) ?>
                                    <br><small class="text-muted">@<?= e($r['username']) ?></small>
                                </td>
                                <td><?= date('d M Y', strtotime($r['upload_date'])) ?></td>
                                <td class="text-end">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="resource_id" value="<?= (int)$r['resource_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success me-1">
                                            <i class="bi bi-check-lg"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="resource_id" value="<?= (int)$r['resource_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Reject this resource?')">
                                            <i class="bi bi-x-lg"></i> Reject
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- All Resources by Status -->
<h5 class="mb-3">All Resources</h5>
<div class="table-responsive">
    <table class="table table-hover table-sm align-middle">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Type</th>
                <th>Status</th>
                <th>Course / Competency</th>
                <th>Uploaded By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_resources as $r): ?>
                <tr>
                    <td><?= e($r['resource_title']) ?></td>
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
                    <td><?= e($r['uploader_name']) ?></td>
                    <td><?= date('d M Y', strtotime($r['upload_date'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

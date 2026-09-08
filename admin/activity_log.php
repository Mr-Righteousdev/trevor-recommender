<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');

$pdo = getPDO();

// Handle date filtering
$date_from = $_GET['from'] ?? date('Y-m-d', strtotime('-7 days'));
$date_to   = $_GET['to'] ?? date('Y-m-d');
$action_filter = $_GET['action'] ?? '';

// Build query
$where = 'WHERE l.created_at BETWEEN ? AND ?';
$params = [$date_from . ' 00:00:00', $date_to . ' 23:59:59'];

if ($action_filter !== '') {
    $where .= ' AND l.action = ?';
    $params[] = $action_filter;
}

$stmt = $pdo->prepare("
    SELECT l.log_id, l.action, l.details, l.ip_address, l.created_at,
           u.full_name, u.username, u.role
    FROM activity_log l
    LEFT JOIN users u ON u.user_id = l.user_id
    $where
    ORDER BY l.created_at DESC
    LIMIT 200
");
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Get distinct actions for filter dropdown
$actions = $pdo->query('SELECT DISTINCT action FROM activity_log ORDER BY action')->fetchAll(PDO::FETCH_COLUMN);

$page_title = 'System Activity Log';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">System Activity Log</h2>

<!-- Filter form -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from" class="form-control" value="<?= e($date_from) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to" class="form-control" value="<?= e($date_to) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Action Type</label>
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    <?php foreach ($actions as $act): ?>
                        <option value="<?= e($act) ?>" <?= $action_filter === $act ? 'selected' : '' ?>>
                            <?= e($act) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (empty($logs)): ?>
    <div class="alert alert-info">No activity found for the selected filters.</div>
<?php else: ?>
    <p class="text-muted mb-3">Showing <?= count($logs) ?> most recent log entries</p>
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Date/Time</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="small"><?= date('d M Y H:i:s', strtotime($log['created_at'])) ?></td>
                        <td>
                            <?php if ($log['full_name']): ?>
                                <?= e($log['full_name']) ?>
                                <br><small class="text-muted">@<?= e($log['username']) ?></small>
                            <?php else: ?>
                                <span class="text-muted">System</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $role_badges = ['admin' => 'bg-danger', 'lecturer' => 'bg-warning text-dark', 'student' => 'bg-info'];
                            ?>
                            <?php if ($log['role']): ?>
                                <span class="badge <?= $role_badges[$log['role']] ?? 'bg-secondary' ?>">
                                    <?= e(ucfirst($log['role'])) ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $action_styles = [
                                'login'          => 'bg-success',
                                'login_failed'   => 'bg-danger',
                                'register'       => 'bg-info',
                                'resource_upload'=> 'bg-primary',
                                'resource_update'=> 'bg-warning text-dark',
                                'resource_delete'=> 'bg-danger',
                            ];
                            ?>
                            <span class="badge <?= $action_styles[$log['action']] ?? 'bg-secondary' ?>">
                                <?= e($log['action']) ?>
                            </span>
                        </td>
                        <td class="small text-muted"><?= e($log['details']) ?></td>
                        <td class="small text-muted"><?= e($log['ip_address']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

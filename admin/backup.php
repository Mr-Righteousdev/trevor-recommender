<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');

$pdo = getPDO();

// Handle backup request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'backup') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        set_flash('danger', 'Invalid request.');
        redirect('/admin/backup.php');
    }

    log_activity('database_backup', 'Database backup initiated');

    // Get all tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    $timestamp = date('Y-m-d_H-i-s');
    $filename = "cbc_recommender_backup_{$timestamp}.sql";

    // Build SQL dump
    $output = "-- CBC Resource Recommender Database Backup\n";
    $output .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Database: " . DB_NAME . "\n\n";
    $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    foreach ($tables as $table) {
        // Get create table statement
        $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch();
        $output .= "DROP TABLE IF EXISTS `$table`;\n";
        $output .= $create['Create Table'] . ";\n\n";

        // Get data
        $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll();
        if (!empty($rows)) {
            $columns = array_keys($rows[0]);
            foreach ($rows as $row) {
                $values = array_map(function ($v) use ($pdo) {
                    if ($v === null) return 'NULL';
                    return $pdo->quote($v);
                }, array_values($row));
                $col_list = implode('`, `', $columns);
                $output .= "INSERT INTO `$table` (`$col_list`) VALUES (" . implode(', ', $values) . ");\n";
            }
            $output .= "\n";
        }
    }

    $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    // Send as download
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($output));
    header('Cache-Control: no-cache, must-revalidate');
    echo $output;
    exit;
}

// Get database info for display
$stats = [
    'users'        => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'courses'      => $pdo->query('SELECT COUNT(*) FROM courses')->fetchColumn(),
    'competencies' => $pdo->query('SELECT COUNT(*) FROM competencies')->fetchColumn(),
    'resources'    => $pdo->query('SELECT COUNT(*) FROM resources')->fetchColumn(),
    'enrollments'  => $pdo->query('SELECT COUNT(*) FROM enrollments')->fetchColumn(),
    'activity'     => $pdo->query('SELECT COUNT(*) FROM activity_log')->fetchColumn(),
];

$page_title = 'Database Backup';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">Database Backup & Management</h2>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-hdd me-2"></i>Database Statistics</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tbody>
                        <?php foreach ($stats as $label => $count): ?>
                            <tr>
                                <td class="text-capitalize"><?= e(str_replace('_', ' ', $label)) ?></td>
                                <td class="text-end fw-bold"><?= (int)$count ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-light">
                            <td>Total Rows</td>
                            <td class="text-end fw-bold"><?= array_sum($stats) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-cloud-download me-2"></i>Export Database</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">
                    Download a complete SQL backup of the database. This file can be used to restore
                    the system to its current state.
                </p>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="backup">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i> Download SQL Backup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>System Requirements</h5>
    </div>
    <div class="card-body">
        <table class="table table-sm mb-0">
            <tbody>
                <tr><td>Web Server</td><td>Apache or Nginx</td></tr>
                <tr><td>Server-Side Language</td><td>PHP <?= phpversion() ?></td></tr>
                <tr><td>Database</td><td><?= $pdo->query('SELECT VERSION()')->fetchColumn() ?></td></tr>
                <tr><td>Charset</td><td>UTF-8 (utf8mb4)</td></tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

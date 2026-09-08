<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('student');

$pdo = getPDO();
$q = trim($_GET['q'] ?? '');
$results = [];

if ($q !== '') {
    $stmt = $pdo->prepare('
        SELECT r.resource_id, r.resource_title, r.resource_type, r.resource_url,
               r.description, c.competency_name, co.course_code
        FROM resources r
        JOIN competencies c ON c.competency_id = r.competency_id
        JOIN courses co ON co.course_id = c.course_id
        WHERE r.resource_title LIKE ? OR r.description LIKE ? OR c.competency_name LIKE ?
        ORDER BY r.resource_title
        LIMIT 50
    ');
    $like = '%' . $q . '%';
    $stmt->execute([$like, $like, $like]);
    $results = $stmt->fetchAll();
}

$page_title = 'Search Resources';
require_once __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">Search Learning Resources</h2>

<form method="GET" class="mb-4">
    <div class="input-group">
        <input type="text" name="q" class="form-control form-control-lg"
               placeholder="Search by title, description or competency..."
               value="<?= e($q) ?>" autofocus>
        <button class="btn btn-primary" type="submit">
            <i class="bi bi-search"></i> Search
        </button>
    </div>
</form>

<?php if ($q !== ''): ?>
    <p class="text-muted">Found <?= count($results) ?> result(s) for "<strong><?= e($q) ?></strong>"</p>

    <?php if (empty($results)): ?>
        <div class="alert alert-info">No resources matched your search.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($results as $res): ?>
                <a href="<?= e($res['resource_url']) ?>" target="_blank"
                   class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1"><?= e($res['resource_title']) ?></h6>
                        <span class="badge bg-secondary"><?= e($res['resource_type']) ?></span>
                    </div>
                    <small class="text-muted">
                        <?= e($res['course_code']) ?> &bull; <?= e($res['competency_name']) ?>
                    </small>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

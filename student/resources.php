<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('student');

$competency_id = (int)($_GET['competency_id'] ?? 0);
if ($competency_id <= 0) {
    set_flash('danger', 'Invalid competency selected.');
    redirect('/student/dashboard.php');
}

$pdo = getPDO();

// Get competency + course info
$stmt = $pdo->prepare('
    SELECT c.competency_id, c.competency_code, c.competency_name, c.description,
           co.course_id, co.course_code, co.course_name
    FROM competencies c
    JOIN courses co ON co.course_id = c.course_id
    WHERE c.competency_id = ?
');
$stmt->execute([$competency_id]);
$competency = $stmt->fetch();

if (!$competency) {
    set_flash('danger', 'Competency not found.');
    redirect('/student/dashboard.php');
}

// Get recommended resources — only show approved ones to students (THE CORE QUERY)
$stmt = $pdo->prepare('
    SELECT r.resource_id, r.resource_title, r.resource_type, r.resource_url,
           r.description, r.upload_date, r.status, u.full_name AS uploaded_by_name
    FROM resources r
    JOIN users u ON u.user_id = r.uploaded_by
    WHERE r.competency_id = ? AND r.status = ?
    ORDER BY r.upload_date DESC
');
$stmt->execute([$competency_id, 'approved']);
$resources = $stmt->fetchAll();

$page_title = 'Resources – ' . $competency['competency_name'];
require_once __DIR__ . '/../includes/header.php';
?>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item">
            <a href="competencies.php?course_id=<?= (int)$competency['course_id'] ?>">
                <?= e($competency['course_code']) ?>
            </a>
        </li>
        <li class="breadcrumb-item active">Resources</li>
    </ol>
</nav>

<div class="mb-4">
    <h2 class="mb-1">
        <?php if ($competency['competency_code']): ?>
            <span class="badge bg-primary me-2"><?= e($competency['competency_code']) ?></span>
        <?php endif; ?>
        <?= e($competency['competency_name']) ?>
    </h2>
    <p class="text-muted">
        Course: <strong><?= e($competency['course_name']) ?></strong>
        <?php if (!empty($competency['description'])): ?>
            <br><?= e($competency['description']) ?>
        <?php endif; ?>
    </p>
</div>

<?php if (empty($resources)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        No learning resources have been tagged to this competency yet.
        Please check back later or contact your lecturer.
    </div>
<?php else: ?>
    <p class="text-muted mb-3">
        <i class="bi bi-collection me-1"></i>
        <?= count($resources) ?> recommended resource<?= count($resources) === 1 ? '' : 's' ?>
    </p>

    <div class="row g-3">
        <?php foreach ($resources as $res): ?>
            <?php
            $typeClass = match ($res['resource_type']) {
                'link'     => 'badge-type-link',
                'pdf'      => 'badge-type-pdf',
                'video'    => 'badge-type-video',
                'document' => 'badge-type-document',
                default    => 'bg-secondary',
            };
            $typeIcon = match ($res['resource_type']) {
                'link'     => 'bi-link-45deg',
                'pdf'      => 'bi-file-earmark-pdf',
                'video'    => 'bi-play-circle',
                'document' => 'bi-file-earmark-text',
                default    => 'bi-file',
            };
            ?>
            <div class="col-md-6">
                <div class="card card-resource h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge <?= $typeClass ?>">
                                <i class="bi <?= $typeIcon ?> me-1"></i>
                                <?= e(strtoupper($res['resource_type'])) ?>
                            </span>
                            <small class="text-muted">
                                <?= date('d M Y', strtotime($res['upload_date'])) ?>
                            </small>
                        </div>
                        <h5 class="card-title"><?= e($res['resource_title']) ?></h5>
                        <?php if (!empty($res['description'])): ?>
                            <p class="card-text text-muted small">
                                <?= e($res['description']) ?>
                            </p>
                        <?php endif; ?>
                        <p class="small text-muted mb-3">
                            Uploaded by <?= e($res['uploaded_by_name']) ?>
                        </p>
                        <a href="<?= e($res['resource_url']) ?>" target="_blank" rel="noopener"
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Open Resource
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

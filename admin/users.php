<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_role('admin');

$pdo = getPDO();
$errors = [];
$editing_user = null;

// Handle create / update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';
        $full_name = trim($_POST['full_name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $username  = trim($_POST['username'] ?? '');
        $role      = $_POST['role'] ?? 'student';

        if ($full_name === '') $errors[] = 'Full name is required.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
        if ($username === '') $errors[] = 'Username is required.';
        if (!in_array($role, ['student', 'lecturer', 'admin'], true)) $errors[] = 'Invalid role.';

        if (empty($errors)) {
            if ($action === 'create') {
                $password = trim($_POST['password'] ?? '');
                if ($password === '') $errors[] = 'Password is required.';
                if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';

                if (empty($errors)) {
                    // Check unique constraints
                    $stmt = $pdo->prepare('SELECT user_id FROM users WHERE username = ? OR email = ?');
                    $stmt->execute([$username, $email]);
                    if ($stmt->fetch()) {
                        $errors[] = 'Username or email already exists.';
                    } else {
                        $stmt = $pdo->prepare('INSERT INTO users (username, password, full_name, email, role) VALUES (?, ?, ?, ?, ?)');
                        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $full_name, $email, $role]);
                        set_flash('success', 'User created successfully.');
                        redirect('/admin/users.php');
                    }
                }
            } elseif ($action === 'update') {
                $edit_id = (int)($_POST['edit_id'] ?? 0);
                if ($edit_id <= 0) {
                    $errors[] = 'Invalid user.';
                } else {
                    // Check unique constraints excluding this user
                    $stmt = $pdo->prepare('SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?');
                    $stmt->execute([$username, $email, $edit_id]);
                    if ($stmt->fetch()) {
                        $errors[] = 'Username or email already exists.';
                    } else {
                        $new_password = trim($_POST['new_password'] ?? '');
                        if ($new_password !== '') {
                            if (strlen($new_password) < 6) {
                                $errors[] = 'New password must be at least 6 characters.';
                            } else {
                                $stmt = $pdo->prepare('UPDATE users SET username = ?, full_name = ?, email = ?, role = ?, password = ? WHERE user_id = ?');
                                $stmt->execute([$username, $full_name, $email, $role, password_hash($new_password, PASSWORD_DEFAULT), $edit_id]);
                            }
                        } else {
                            $stmt = $pdo->prepare('UPDATE users SET username = ?, full_name = ?, email = ?, role = ? WHERE user_id = ?');
                            $stmt->execute([$username, $full_name, $email, $role, $edit_id]);
                        }
                        if (empty($errors)) {
                            set_flash('success', 'User updated successfully.');
                            redirect('/admin/users.php');
                        }
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
        redirect('/admin/users.php');
    }
    $del_id = (int)($_POST['user_id'] ?? 0);
    if ($del_id > 0 && $del_id !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare('DELETE FROM users WHERE user_id = ?');
        $stmt->execute([$del_id]);
        set_flash('success', 'User deleted.');
    }
    redirect('/admin/users.php');
}

// Fetch users for edit form if editing
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editing_user = $stmt->fetch();
}

$users = $pdo->query('SELECT user_id, username, full_name, email, role, created_at FROM users ORDER BY role, full_name')->fetchAll();

$page_title = 'Manage Users';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Manage Users</h2>
    <a href="#create-form" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Add User
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
        <h5 class="mb-0"><?= $editing_user ? 'Edit User' : 'Create New User' ?></h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="<?= $editing_user ? 'update' : 'create' ?>">
            <?php if ($editing_user): ?>
                <input type="hidden" name="edit_id" value="<?= (int)$editing_user['user_id'] ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control"
                           value="<?= e($_POST['full_name'] ?? $editing_user['full_name'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control"
                           value="<?= e($_POST['email'] ?? $editing_user['email'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control"
                           value="<?= e($_POST['username'] ?? $editing_user['username'] ?? '') ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <?php
                        $current_role = $_POST['role'] ?? $editing_user['role'] ?? 'student';
                        $roles = ['student' => 'Student', 'lecturer' => 'Lecturer', 'admin' => 'Admin'];
                        foreach ($roles as $val => $label):
                        ?>
                            <option value="<?= $val ?>" <?= $current_role === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        <?= $editing_user ? 'New Password (leave blank to keep)' : 'Password *' ?>
                    </label>
                    <input type="password" name="<?= $editing_user ? 'new_password' : 'password' ?>" class="form-control"
                           <?= $editing_user ? '' : 'required' ?> minlength="6">
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> <?= $editing_user ? 'Update User' : 'Create User' ?>
                </button>
                <?php if ($editing_user): ?>
                    <a href="users.php" class="btn btn-outline-secondary">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= e($u['full_name']) ?></td>
                    <td><?= e($u['username']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td>
                        <?php
                        $role_badges = ['admin' => 'bg-danger', 'lecturer' => 'bg-warning text-dark', 'student' => 'bg-info'];
                        ?>
                        <span class="badge <?= $role_badges[$u['role']] ?? 'bg-secondary' ?>">
                            <?= e(ucfirst($u['role'])) ?>
                        </span>
                    </td>
                    <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td class="text-end">
                        <a href="?edit=<?= (int)$u['user_id'] ?>#create-form"
                           class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($u['user_id'] !== $_SESSION['user_id']): ?>
                            <form method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this user permanently?')">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="user_id" value="<?= (int)$u['user_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

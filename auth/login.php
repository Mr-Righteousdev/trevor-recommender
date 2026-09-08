<?php
require_once __DIR__ . '/../includes/auth_check.php';

// Already logged in? Redirect
if (is_logged_in()) {
    redirect('/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $pdo = getPDO();
        $stmt = $pdo->prepare('SELECT user_id, username, password, full_name, role FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            log_activity('login', 'User logged in successfully');
            set_flash('success', 'Welcome back, ' . $user['full_name'] . '!');
            redirect('/index.php');
        } else {
            log_activity('login_failed', 'Failed login attempt for username: ' . $username);
            $error = 'Invalid username or password.';
        }
    }
}

$page_title = 'Login';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow login-card">
            <div class="card-body p-4">
                <h3 class="card-title text-center mb-4">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </h3>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </button>
                </form>

                <p class="text-center mt-3 mb-0">
                    Don't have an account? <a href="register.php">Register as Student</a>
                </p>
            </div>
        </div>

        <div class="card mt-3 border-0 bg-transparent">
            <div class="card-body text-center small text-muted">
                <strong>Demo accounts</strong><br>
                Admin: <code>admin</code> / <code>password</code><br>
                Lecturer: <code>lecturer1</code> / <code>password</code><br>
                Student: <code>student1</code> / <code>password</code>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

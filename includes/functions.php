<?php
/**
 * Helper Functions - CBC Resource Recommender
 *
 * Provides core utility functions for authentication, security,
 * session management, and activity logging across the application.
 */

/**
 * Escape HTML output to prevent Cross-Site Scripting (XSS).
 *
 * @param string|null $value The value to escape
 * @return string The HTML-safe escaped string
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Flash Message Helpers
 *
 * Flash messages are stored in the session and displayed once on the next page load.
 * Used to show success/error feedback after form submissions and redirects.
 *
 * @param string $type    The alert type (e.g., 'success', 'danger', 'info', 'warning')
 * @param string $message The message to display
 */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Retrieve and clear the current flash message from the session.
 *
 * @return array|null ['type' => string, 'message' => string] or null if no flash
 */
function get_flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Redirect to a URL and terminate the script.
 *
 * @param string $url The absolute or relative URL to redirect to
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Check if a user is currently logged in.
 *
 * @return bool True if the session contains a user_id
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Get the role of the currently logged-in user.
 *
 * @return string|null 'student', 'lecturer', 'admin', or null if not logged in
 */
function current_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

/**
 * Require the user to have one of the specified roles to access the page.
 * Redirects to login if not authenticated, or to the home page if unauthorized.
 *
 * @param string|array $roles One or more allowed roles (e.g., 'admin' or ['admin', 'lecturer'])
 */
function require_role(string|array $roles): void
{
    if (!is_logged_in()) {
        redirect('/auth/login.php');
    }

    $allowed = (array) $roles;
    if (!in_array(current_role(), $allowed, true)) {
        set_flash('danger', 'You do not have permission to access that page.');
        redirect('/index.php');
    }
}

/**
 * Generate or retrieve a CSRF token for the current session.
 * Uses 64-byte random hex string for security.
 *
 * @return string The CSRF token
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify that a submitted CSRF token matches the session token.
 * Uses timing-safe comparison via hash_equals().
 *
 * @param string|null $token The token submitted via form
 * @return bool True if the token is valid
 */
function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

/**
 * Log a user activity to the activity_log table.
 * Used for system monitoring and audit trail (proposal requirement 4.2.3).
 * Silently fails if the table doesn't exist or a DB error occurs.
 *
 * @param string    $action  The action type (e.g., 'login', 'resource_upload')
 * @param string|null $details Optional human-readable detail about the action
 * @param int|null  $user_id Optional user ID; defaults to current session user
 */
function log_activity(string $action, ?string $details = null, ?int $user_id = null): void
{
    try {
        $pdo = getPDO();
        $uid = $user_id ?? ($_SESSION['user_id'] ?? null);
        $ip  = $_SERVER['REMOTE_ADDR'] ?? null;

        $stmt = $pdo->prepare('INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)');
        $stmt->execute([$uid, $action, $details, $ip]);
    } catch (\Exception $e) {
        // Silently fail — logging should never break the application
    }
}

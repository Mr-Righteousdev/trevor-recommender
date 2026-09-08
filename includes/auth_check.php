<?php
/**
 * Authentication Bootstrap - CBC Resource Recommender
 *
 * This file must be included at the top of every protected page.
 * It initializes the session, loads the database connection, and
 * provides access to helper functions for authentication and security.
 */

// Start the session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load database connection and helper functions
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

// Individual pages call require_role() to enforce access control

<?php
/**
 * Session Management
 * 
 * Secure session handling for the application.
 */

session_start();

// Regenerate session ID periodically to prevent fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

/**
 * Check if user is authenticated
 */
function is_authenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

/**
 * Require authentication - redirect to login if not authenticated
 */
function require_auth() {
    if (!is_authenticated()) {
        header('Location: /MedVulnLab/auth/login.php');
        exit;
    }
}

/**
 * Require specific role
 */
function require_role($allowed_roles) {
    require_auth();
    
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles];
    }
    
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header('Location: /MedVulnLab/index.php?error=access_denied');
        exit;
    }
}

/**
 * Get current user ID
 */
function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function get_user_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current username
 */
function get_username() {
    return $_SESSION['username'] ?? null;
}

/**
 * Logout user
 */
function logout() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

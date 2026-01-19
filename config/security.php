<?php
/**
 * Security Configuration
 * 
 * This file contains secure defaults and lab module flags.
 * In a real application, these would be environment variables.
 */

// Lab module flags - Set to true to enable vulnerable code paths
define('LAB_A01_LOW_ENABLED', true);
define('LAB_A01_MEDIUM_ENABLED', true);
define('LAB_A01_HARD_ENABLED', true);
define('LAB_A01_IMPOSSIBLE_ENABLED', true);

// Security defaults
define('SESSION_TIMEOUT', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 300); // 5 minutes

// Application settings
define('APP_NAME', 'VulnHub');
define('APP_VERSION', '1.0.0');

// Ensure application only runs on localhost
function enforce_localhost() {
    $allowed_hosts = ['localhost', '127.0.0.1', '::1'];
    $host = $_SERVER['HTTP_HOST'] ?? '';
    
    // Remove port if present
    $host = preg_replace('/:\d+$/', '', $host);
    
    if (!in_array($host, $allowed_hosts)) {
        die('ERROR: This application must only run on localhost for security reasons.');
    }
}

// Call on every request
enforce_localhost();

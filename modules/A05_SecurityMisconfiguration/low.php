<?php
/**
 * A05 Security Misconfiguration - LOW Level
 * 
 * VULNERABILITY: Debug Mode Enabled / Verbose Error Messages
 * 
 * The application has debug mode enabled and displays verbose error messages
 * that reveal sensitive information about the system, file paths, and internal structure.
 * 
 * DESIGN MISTAKE:
 * - Debug mode enabled in production
 * - Detailed error messages exposed to users
 * - Stack traces reveal file paths and code structure
 * 
 * EXPLOITATION:
 * - Trigger errors to reveal system information
 * - Use error messages to understand application structure
 * - Discover file paths and directory structure
 */

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../assets/flag_system.php';

// VULNERABILITY: Debug mode enabled
define('DEBUG_MODE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_auth();

require_once __DIR__ . '/../../assets/warning_banner.php';
require_once __DIR__ . '/../../assets/sidebar.php';
require_once __DIR__ . '/../../assets/layout.php';
require_once __DIR__ . '/../../assets/module_template.php';

$action = $_GET['action'] ?? 'info';
$error_triggered = false;
$flag_triggered = false;

// VULNERABILITY: Verbose error handling
if ($action === 'trigger_error') {
    $error_triggered = true;
    // Intentionally trigger an error to show verbose output
    $undefined_var->someMethod();
} elseif ($action === 'file_info') {
    // Reveal file system information
    $file_info = [
        'script_path' => __FILE__,
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
        'php_version' => PHP_VERSION,
        'loaded_extensions' => get_loaded_extensions()
    ];
}

// Flag trigger: If user discovers sensitive information
if (isset($_GET['sensitive']) && $_GET['sensitive'] === 'config') {
    if (!has_flag('A05', 'low')) {
        set_flag('A05', 'low', 'A05_LOW_' . strtoupper(bin2hex(random_bytes(4))));
        $flag_triggered = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A05 - LOW: Security Misconfiguration</title>
    <?php echo get_dark_mode_styles(); ?>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A05', 'System Information', 'low', 'LOW'); ?>
            <?php render_level_switcher('A05', 'A05_SecurityMisconfiguration', 'low'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A05', 'low', get_flag_code('A05', 'low')); ?>
            <?php endif; ?>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Debug Mode & Verbose Errors</h3>
                <p><strong>Issue:</strong> Debug mode is enabled, revealing sensitive system information through error messages.</p>
                <p><strong>Design Mistake:</strong> Production application has debug mode enabled, detailed error messages exposed.</p>
                <p><strong>Challenge:</strong> Can you discover sensitive configuration information? Try triggering errors or exploring the system!</p>
            </div>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <h3 style="color: var(--text-primary); margin-bottom: 15px;">Actions</h3>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="?action=trigger_error" class="btn">Trigger Error</a>
                    <a href="?action=file_info" class="btn">View File Info</a>
                    <a href="?sensitive=config" class="btn" style="background: var(--danger);">Access Config</a>
                </div>
            </div>
            
            <?php if ($error_triggered): ?>
                <div class="error" style="margin-top: 20px;">
                    <h4 style="margin-bottom: 10px;">Error Details (DEBUG MODE ENABLED):</h4>
                    <?php
                    // This will trigger a fatal error and display stack trace
                    try {
                        $undefined_var->someMethod();
                    } catch (Throwable $e) {
                        echo '<pre style="background: var(--bg-primary); padding: 15px; border-radius: 5px; overflow-x: auto; color: var(--text-primary);">';
                        echo "Error: " . htmlspecialchars($e->getMessage()) . "\n\n";
                        echo "File: " . htmlspecialchars($e->getFile()) . "\n";
                        echo "Line: " . $e->getLine() . "\n\n";
                        echo "Stack Trace:\n" . htmlspecialchars($e->getTraceAsString());
                        echo '</pre>';
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($file_info)): ?>
                <div class="content-wrapper" style="margin-top: 20px;">
                    <h3 style="color: var(--text-primary); margin-bottom: 15px;">System Information</h3>
                    <pre style="background: var(--bg-primary); padding: 15px; border-radius: 5px; overflow-x: auto; color: var(--text-primary); border: 1px solid var(--border-color);"><?php 
                        echo "Script Path: " . htmlspecialchars($file_info['script_path']) . "\n";
                        echo "Document Root: " . htmlspecialchars($file_info['document_root']) . "\n";
                        echo "Server Software: " . htmlspecialchars($file_info['server_software']) . "\n";
                        echo "PHP Version: " . htmlspecialchars($file_info['php_version']) . "\n";
                        echo "\nLoaded Extensions:\n";
                        foreach (array_slice($file_info['loaded_extensions'], 0, 20) as $ext) {
                            echo "  - " . htmlspecialchars($ext) . "\n";
                        }
                    ?></pre>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['sensitive']) && $_GET['sensitive'] === 'config'): ?>
                <div class="message" style="margin-top: 20px;">
                    <h4 style="margin-bottom: 10px;">Configuration Access Granted!</h4>
                    <pre style="background: var(--bg-primary); padding: 15px; border-radius: 5px; overflow-x: auto; color: var(--text-primary);">
DEBUG_MODE: <?php echo DEBUG_MODE ? 'ENABLED' : 'DISABLED'; ?>

Session Configuration:
  - Session Save Path: <?php echo htmlspecialchars(session_save_path()); ?>

PHP Configuration:
  - Error Reporting: <?php echo error_reporting(); ?>

Application Paths:
  - Config Directory: <?php echo htmlspecialchars(__DIR__ . '/../../config/'); ?>
  - Data Directory: <?php echo htmlspecialchars(__DIR__ . '/../../data/'); ?>
                    </pre>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
/**
 * A03 Injection - MEDIUM Level
 * 
 * VULNERABILITY: Command Injection via File Operations
 * 
 * The application allows users to view file contents, but doesn't properly
 * sanitize the file path input, allowing directory traversal and command injection.
 * 
 * DESIGN MISTAKE:
 * - File paths are not validated or sanitized
 * - Direct use of user input in file operations
 * - No path normalization or whitelisting
 * 
 * EXPLOITATION:
 * - Use directory traversal (../) to access files outside intended directory
 * - Inject system commands using command chaining
 * - Access sensitive configuration files
 */

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../assets/flag_system.php';

require_auth();

require_once __DIR__ . '/../../assets/warning_banner.php';
require_once __DIR__ . '/../../assets/sidebar.php';
require_once __DIR__ . '/../../assets/layout.php';
require_once __DIR__ . '/../../assets/module_template.php';

$file_path = $_GET['file'] ?? 'reports/patient_report_001.txt';
$file_content = '';
$error = '';
$flag_triggered = false;

// Create a safe reports directory for demo
$reports_dir = __DIR__ . '/../../data/reports/';
if (!is_dir($reports_dir)) {
    mkdir($reports_dir, 0755, true);
    file_put_contents($reports_dir . 'patient_report_001.txt', 'Patient Report 001: Normal checkup results.');
    file_put_contents($reports_dir . 'patient_report_002.txt', 'Patient Report 002: Lab results pending.');
}

// VULNERABILITY: File path not properly sanitized
// Allows directory traversal and potential command injection
$full_path = __DIR__ . '/../../data/' . $file_path;

// Check if file exists and is readable
if (file_exists($full_path) && is_readable($full_path)) {
    $file_content = file_get_contents($full_path);
    
    // Flag trigger: If accessing files outside reports directory
    if (strpos($file_path, '../') !== false || strpos(realpath($full_path), realpath($reports_dir)) === false) {
        if (!has_flag('A03', 'medium')) {
            set_flag('A03', 'medium', 'A03_MED_' . strtoupper(bin2hex(random_bytes(4))));
            $flag_triggered = true;
        }
    }
} elseif (!empty($file_path)) {
    $error = "File not found or not accessible.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A03 - MEDIUM: Injection</title>
    <?php echo get_dark_mode_styles(); ?>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A03', 'View Patient Reports', 'medium', 'MEDIUM'); ?>
            <?php render_level_switcher('A03', 'A03_Injection', 'medium'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A03', 'medium', get_flag_code('A03', 'medium')); ?>
            <?php endif; ?>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Path Traversal / Command Injection</h3>
                <p><strong>Issue:</strong> File paths are not properly validated, allowing directory traversal attacks.</p>
                <p><strong>Design Mistake:</strong> No path normalization, no whitelisting, direct use of user input.</p>
                <p><strong>Challenge:</strong> Can you access files outside the reports directory? Try using directory traversal (../) patterns!</p>
            </div>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="file">File Path (relative to data directory):</label>
                        <input type="text" id="file" name="file" 
                               value="<?php echo htmlspecialchars($file_path); ?>" 
                               placeholder="reports/patient_report_001.txt" required>
                    </div>
                    <button type="submit">View File</button>
                </form>
            </div>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($file_content): ?>
                <div class="content-wrapper" style="margin-top: 20px;">
                    <h3 style="color: var(--text-primary); margin-bottom: 15px;">File Content:</h3>
                    <pre style="background: var(--bg-primary); padding: 15px; border-radius: 5px; color: var(--text-primary); overflow-x: auto; border: 1px solid var(--border-color);"><?php echo htmlspecialchars($file_content); ?></pre>
                </div>
            <?php endif; ?>
            
            <div class="info-box" style="margin-top: 20px;">
                <h4 style="color: var(--info); margin-bottom: 8px;">Available Files:</h4>
                <ul style="color: var(--text-secondary); margin-left: 20px;">
                    <li>reports/patient_report_001.txt</li>
                    <li>reports/patient_report_002.txt</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

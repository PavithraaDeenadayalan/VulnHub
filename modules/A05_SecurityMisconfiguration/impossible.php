<?php
/**
 * A05 Security Misconfiguration - IMPOSSIBLE Level
 * 
 * VULNERABILITY: Insecure File Permissions & Directory Listing
 * 
 * The application has insecure file permissions and allows directory listing,
 * exposing sensitive files, configuration files, and backup files that shouldn't
 * be accessible via the web.
 * 
 * DESIGN MISTAKE:
 * - Files stored in web-accessible directories
 * - Directory listing enabled
 * - Sensitive files not protected
 * - Backup files left in production
 * 
 * EXPLOITATION:
 * - Access configuration files directly
 * - List directory contents
 * - Download backup files
 * - Access sensitive data files
 */

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../assets/flag_system.php';

require_auth();

require_once __DIR__ . '/../../assets/warning_banner.php';
require_once __DIR__ . '/../../assets/sidebar.php';
require_once __DIR__ . '/../../assets/layout.php';
require_once __DIR__ . '/../../assets/module_template.php';

// Create some sensitive files for demonstration
$data_dir = __DIR__ . '/../../data/';
$backup_file = $data_dir . 'backup_config.php.bak';
$secret_file = $data_dir . '.secret_key.txt';

if (!file_exists($backup_file)) {
    file_put_contents($backup_file, "<?php\n// Backup configuration\n\$secret_key = 'FLAG_A05_IMP_" . strtoupper(bin2hex(random_bytes(4))) . "';\n");
}
if (!file_exists($secret_file)) {
    file_put_contents($secret_file, "SECRET_API_KEY=sk_live_" . bin2hex(random_bytes(16)) . "\nDATABASE_PASSWORD=admin123\n");
}

$requested_path = $_GET['path'] ?? 'data/';
$full_path = __DIR__ . '/../../' . $requested_path;
$content = '';
$is_directory = false;
$error = '';
$flag_triggered = false;

// VULNERABILITY: Directory listing and file access without proper restrictions
if (file_exists($full_path)) {
    if (is_dir($full_path)) {
        $is_directory = true;
        // VULNERABILITY: Directory listing enabled
        $files = scandir($full_path);
        $files = array_filter($files, function($file) {
            return $file !== '.' && $file !== '..';
        });
    } else {
        // VULNERABILITY: Direct file access without authorization
        if (is_readable($full_path)) {
            $content = file_get_contents($full_path);
            
            // Flag trigger: If accessing sensitive files
            if (strpos($requested_path, 'backup') !== false || 
                strpos($requested_path, '.secret') !== false ||
                strpos($requested_path, 'config') !== false) {
                if (!has_flag('A05', 'impossible')) {
                    set_flag('A05', 'impossible', 'A05_IMP_' . strtoupper(bin2hex(random_bytes(4))));
                    $flag_triggered = true;
                }
            }
        } else {
            $error = "File is not readable.";
        }
    }
} else {
    $error = "Path does not exist.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A05 - IMPOSSIBLE: Security Misconfiguration</title>
    <?php echo get_dark_mode_styles(); ?>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A05', 'File Browser', 'impossible', 'IMPOSSIBLE'); ?>
            <?php render_level_switcher('A05', 'A05_SecurityMisconfiguration', 'impossible'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A05', 'impossible', get_flag_code('A05', 'impossible')); ?>
            <?php endif; ?>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Insecure File Permissions & Directory Listing</h3>
                <p><strong>Issue:</strong> Directory listing is enabled and files are accessible without proper authorization, exposing sensitive data.</p>
                <p><strong>Design Mistake:</strong> Files stored in web-accessible directories, directory listing enabled, backup files left in production.</p>
                <p><strong>Challenge:</strong> Can you access sensitive configuration or backup files? Try exploring directories and looking for .bak, .old, or config files!</p>
            </div>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <h3 style="color: var(--text-primary); margin-bottom: 15px;">Browse Files</h3>
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="path">Path (relative to application root):</label>
                        <input type="text" id="path" name="path" 
                               value="<?php echo htmlspecialchars($requested_path); ?>" 
                               placeholder="data/" required>
                    </div>
                    <button type="submit">Browse</button>
                </form>
            </div>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($is_directory && isset($files)): ?>
                <div class="content-wrapper" style="margin-top: 20px;">
                    <h3 style="color: var(--text-primary); margin-bottom: 15px;">
                        Directory Contents: <?php echo htmlspecialchars($requested_path); ?>
                    </h3>
                    <div style="background: var(--bg-primary); padding: 15px; border-radius: 5px;">
                        <?php foreach ($files as $file): ?>
                            <?php
                            $file_path = rtrim($requested_path, '/') . '/' . $file;
                            $is_file = is_file(__DIR__ . '/../../' . $file_path);
                            ?>
                            <div style="padding: 8px; border-bottom: 1px solid var(--border-color);">
                                <a href="?path=<?php echo urlencode($file_path); ?>" 
                                   style="color: var(--accent-primary); text-decoration: none;">
                                    <?php echo $is_file ? '📄' : '📁'; ?> 
                                    <?php echo htmlspecialchars($file); ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($content): ?>
                <div class="content-wrapper" style="margin-top: 20px;">
                    <h3 style="color: var(--text-primary); margin-bottom: 15px;">
                        File Content: <?php echo htmlspecialchars($requested_path); ?>
                    </h3>
                    <pre style="background: var(--bg-primary); padding: 15px; border-radius: 5px; overflow-x: auto; color: var(--text-primary); border: 1px solid var(--border-color);"><?php echo htmlspecialchars($content); ?></pre>
                </div>
            <?php endif; ?>
            
            <div class="info-box" style="margin-top: 20px;">
                <h4 style="color: var(--info); margin-bottom: 8px;">💡 Common Sensitive Files:</h4>
                <ul style="color: var(--text-secondary); margin-left: 20px; font-size: 14px;">
                    <li>*.bak, *.backup, *.old (backup files)</li>
                    <li>.env, config.php (configuration files)</li>
                    <li>.secret, .key (secret files)</li>
                    <li>*.sql (database dumps)</li>
                    <li>.git/ (version control)</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

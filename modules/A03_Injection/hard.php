<?php
/**
 * A03 Injection - HARD Level
 * 
 * VULNERABILITY: Object Injection / Deserialization
 * 
 * The application deserializes user-controlled data without validation,
 * allowing object injection attacks that can lead to arbitrary code execution.
 * 
 * DESIGN MISTAKE:
 * - User input is deserialized without validation
 * - No type checking or whitelisting
 * - Dangerous PHP functions can be called through magic methods
 * 
 * EXPLOITATION:
 * - Craft malicious serialized objects
 * - Exploit PHP magic methods (__wakeup, __destruct)
 * - Execute arbitrary code through object injection
 */

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../assets/flag_system.php';

require_auth();

require_once __DIR__ . '/../../assets/warning_banner.php';
require_once __DIR__ . '/../../assets/sidebar.php';
require_once __DIR__ . '/../../assets/layout.php';
require_once __DIR__ . '/../../assets/module_template.php';

$message = '';
$error = '';
$flag_triggered = false;

// Simulated user preferences storage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $preferences_data = $_POST['preferences'] ?? '';
    
    // VULNERABILITY: Deserializing user input without validation
    if (!empty($preferences_data)) {
        try {
            // Dangerous: Unserializing user-controlled data
            $preferences = unserialize($preferences_data);
            
            if ($preferences !== false) {
                $message = "Preferences saved successfully!";
                
                // Flag trigger: If unserialize executes dangerous code
                // Check if file was created or modified (indicates code execution)
                $flag_file = __DIR__ . '/../../data/.flag_check';
                if (file_exists($flag_file)) {
                    if (!has_flag('A03', 'hard')) {
                        set_flag('A03', 'hard', 'A03_HARD_' . strtoupper(bin2hex(random_bytes(4))));
                        $flag_triggered = true;
                        unlink($flag_file); // Clean up
                    }
                }
            } else {
                $error = "Invalid preferences data.";
            }
        } catch (Exception $e) {
            $error = "Error processing preferences: " . htmlspecialchars($e->getMessage());
        }
    } else {
        $error = "Please provide preferences data.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A03 - HARD: Injection</title>
    <?php echo get_dark_mode_styles(); ?>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A03', 'User Preferences', 'hard', 'HARD'); ?>
            <?php render_level_switcher('A03', 'A03_Injection', 'hard'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A03', 'hard', get_flag_code('A03', 'hard')); ?>
            <?php endif; ?>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Object Injection / Deserialization</h3>
                <p><strong>Issue:</strong> User-controlled data is deserialized without validation, allowing object injection attacks.</p>
                <p><strong>Design Mistake:</strong> No type checking, no whitelisting, dangerous PHP magic methods can be exploited.</p>
                <p><strong>Challenge:</strong> Can you craft a serialized object that executes code? Research PHP object injection and magic methods!</p>
            </div>
            
            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="preferences">Preferences Data (Serialized PHP):</label>
                        <textarea id="preferences" name="preferences" rows="5" 
                                  placeholder='O:8:"stdClass":1:{s:4:"theme";s:5:"dark";}' required></textarea>
                        <small style="color: var(--text-secondary); font-size: 12px; margin-top: 5px; display: block;">
                            Enter serialized PHP object data
                        </small>
                    </div>
                    <button type="submit">Save Preferences</button>
                </form>
            </div>
            
            <div class="info-box" style="margin-top: 20px;">
                <h4 style="color: var(--info); margin-bottom: 8px;">💡 Hint:</h4>
                <p style="color: var(--text-secondary); font-size: 14px;">
                    Research PHP object injection vulnerabilities. Look for classes with magic methods like __wakeup() or __destruct() 
                    that might execute code. The flag is triggered when code execution is detected.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

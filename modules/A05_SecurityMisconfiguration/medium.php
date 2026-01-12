<?php
/**
 * A05 Security Misconfiguration - MEDIUM Level
 * 
 * VULNERABILITY: Default Credentials & Weak Authentication
 * 
 * The application uses default or weak credentials that are easily guessable.
 * Additionally, authentication mechanisms are misconfigured, allowing brute force
 * or credential stuffing attacks.
 * 
 * DESIGN MISTAKE:
 * - Default credentials not changed
 * - No account lockout mechanism
 * - Weak password policy
 * - Credentials stored in plaintext or weak hashing
 * 
 * EXPLOITATION:
 * - Use default credentials to gain access
 * - Brute force weak passwords
 * - Exploit lack of rate limiting
 */

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../data/seed.php';
require_once __DIR__ . '/../../assets/flag_system.php';

require_once __DIR__ . '/../../assets/warning_banner.php';
require_once __DIR__ . '/../../assets/sidebar.php';
require_once __DIR__ . '/../../assets/layout.php';
require_once __DIR__ . '/../../assets/module_template.php';

$message = '';
$error = '';
$flag_triggered = false;

// VULNERABILITY: Default admin credentials
$default_admin = [
    'username' => 'admin',
    'password' => 'admin123', // Weak default password
    'role' => 'administrator'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // VULNERABILITY: No rate limiting, weak password check
    if ($username === $default_admin['username'] && $password === $default_admin['password']) {
        // Flag trigger: Successfully logged in with default credentials
        if (!has_flag('A05', 'medium')) {
            set_flag('A05', 'medium', 'A05_MED_' . strtoupper(bin2hex(random_bytes(4))));
            $flag_triggered = true;
        }
        $message = "Login successful! Default credentials accepted.";
    } else {
        $error = "Invalid credentials. Try common default usernames and passwords!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A05 - MEDIUM: Security Misconfiguration</title>
    <?php echo get_dark_mode_styles(); ?>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A05', 'Admin Login', 'medium', 'MEDIUM'); ?>
            <?php render_level_switcher('A05', 'A05_SecurityMisconfiguration', 'medium'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A05', 'medium', get_flag_code('A05', 'medium')); ?>
            <?php endif; ?>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Default Credentials & Weak Authentication</h3>
                <p><strong>Issue:</strong> Application uses default credentials that haven't been changed, and has weak authentication mechanisms.</p>
                <p><strong>Design Mistake:</strong> No password policy enforcement, default credentials not changed, no account lockout.</p>
                <p><strong>Challenge:</strong> Can you guess the default admin credentials? Try common defaults like admin/admin, admin/password, etc.</p>
            </div>
            
            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <h3 style="color: var(--text-primary); margin-bottom: 15px;">Administrator Login</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
            </div>
            
            <div class="info-box" style="margin-top: 20px;">
                <h4 style="color: var(--info); margin-bottom: 8px;">💡 Common Default Credentials:</h4>
                <ul style="color: var(--text-secondary); margin-left: 20px; font-size: 14px;">
                    <li>admin / admin</li>
                    <li>admin / password</li>
                    <li>admin / admin123</li>
                    <li>administrator / administrator</li>
                    <li>root / root</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

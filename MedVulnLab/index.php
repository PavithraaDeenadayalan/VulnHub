<?php
/**
 * Main Dashboard
 * 
 * Entry point for the Healthcare Management System.
 */

require_once __DIR__ . '/config/security.php';
require_once __DIR__ . '/auth/session.php';
require_once __DIR__ . '/assets/flag_system.php';

require_auth();

require_once __DIR__ . '/assets/warning_banner.php';
require_once __DIR__ . '/assets/sidebar.php';
require_once __DIR__ . '/assets/layout.php';

$user_role = get_user_role();
$username = get_username();
$name = $_SESSION['name'] ?? 'User';
$total_flags = get_total_flags();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    <?php echo get_dark_mode_styles(); ?>
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--bg-tertiary);
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid var(--accent-primary);
        }
        
        .stat-card h3 {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .stat-card .stat-value {
            color: var(--accent-primary);
            font-size: 32px;
            font-weight: bold;
        }
        
        .welcome-section {
            background: var(--bg-tertiary);
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .welcome-section h2 {
            color: var(--text-primary);
            margin-bottom: 15px;
        }
        
        .welcome-section p {
            color: var(--text-secondary);
            line-height: 1.8;
        }
        
        .role-badge {
            display: inline-block;
            padding: 6px 12px;
            background: var(--accent-primary);
            color: white;
            border-radius: 5px;
            font-size: 13px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <div class="header">
                <h1>Healthcare Management System Dashboard</h1>
                <div style="color: var(--text-secondary); margin-top: 10px;">
                    Welcome, <?php echo htmlspecialchars($name); ?>
                    <span class="role-badge"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $user_role))); ?></span>
                </div>
            </div>
            
            <div class="dashboard-stats">
                <div class="stat-card">
                    <h3>Total Flags Captured</h3>
                    <div class="stat-value"><?php echo $total_flags; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Your Role</h3>
                    <div class="stat-value" style="font-size: 20px;"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $user_role))); ?></div>
                </div>
                <div class="stat-card">
                    <h3>Active Modules</h3>
                    <div class="stat-value">3</div>
                </div>
            </div>
            
            <div class="welcome-section">
                <h2>Welcome to MedVulnLab</h2>
                <p>
                    This is an educational lab designed to help you understand modern web security vulnerabilities.
                    Use the sidebar navigation to explore different OWASP Top 10 vulnerabilities.
                </p>
                <p style="margin-top: 15px;">
                    <strong>Your Mission:</strong> Exploit vulnerabilities across different modules and levels to capture flags.
                    Each successful exploit will reward you with a flag that proves you understand the vulnerability.
                </p>
            </div>
            
            <div class="info-box">
                <h3 style="color: var(--info); margin-bottom: 10px;"> Getting Started</h3>
                <ul style="color: var(--text-secondary); line-height: 2; margin-left: 20px;">
                    <li>Select a vulnerability from the left sidebar</li>
                    <li>Choose a difficulty level (LOW, MEDIUM, HARD, IMPOSSIBLE)</li>
                    <li>Read the vulnerability description carefully</li>
                    <li>Attempt to exploit the vulnerability</li>
                    <li>Capture the flag when successful!</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>

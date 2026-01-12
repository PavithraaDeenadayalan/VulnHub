<?php
/**
 * Sidebar Navigation Component
 * 
 * Displays all OWASP Top 10 vulnerabilities with level switching
 */

require_once __DIR__ . '/../auth/session.php';
require_once __DIR__ . '/flag_system.php';

// Get current module and level from URL
$current_path = $_SERVER['REQUEST_URI'] ?? '';
$current_module = '';
$current_level = '';

// Parse current module
if (strpos($current_path, 'A01_BrokenAccessControl') !== false) {
    $current_module = 'A01';
} elseif (strpos($current_path, 'A02_CryptographicFailures') !== false) {
    $current_module = 'A02';
} elseif (strpos($current_path, 'A03_Injection') !== false) {
    $current_module = 'A03';
} elseif (strpos($current_path, 'A04_InsecureDesign') !== false) {
    $current_module = 'A04';
} elseif (strpos($current_path, 'A05_SecurityMisconfiguration') !== false) {
    $current_module = 'A05';
} elseif (strpos($current_path, 'A06_VulnerableComponents') !== false) {
    $current_module = 'A06';
} elseif (strpos($current_path, 'A07_AuthenticationFailures') !== false) {
    $current_module = 'A07';
} elseif (strpos($current_path, 'A08_SoftwareDataIntegrity') !== false) {
    $current_module = 'A08';
} elseif (strpos($current_path, 'A09_LoggingMonitoring') !== false) {
    $current_module = 'A09';
} elseif (strpos($current_path, 'A10_SSRF') !== false) {
    $current_module = 'A10';
}

// Parse current level
if (strpos($current_path, '/low.php') !== false) {
    $current_level = 'low';
} elseif (strpos($current_path, '/medium.php') !== false) {
    $current_level = 'medium';
} elseif (strpos($current_path, '/hard.php') !== false) {
    $current_level = 'hard';
} elseif (strpos($current_path, '/impossible.php') !== false) {
    $current_level = 'impossible';
}

// OWASP Top 10 2021/2025 vulnerabilities
$vulnerabilities = [
    'A01' => [
        'name' => 'Broken Access Control',
        'path' => 'A01_BrokenAccessControl',
        'enabled' => true
    ],
    'A02' => [
        'name' => 'Cryptographic Failures',
        'path' => 'A02_CryptographicFailures',
        'enabled' => false
    ],
    'A03' => [
        'name' => 'Injection',
        'path' => 'A03_Injection',
        'enabled' => true
    ],
    'A04' => [
        'name' => 'Insecure Design',
        'path' => 'A04_InsecureDesign',
        'enabled' => false
    ],
    'A05' => [
        'name' => 'Security Misconfiguration',
        'path' => 'A05_SecurityMisconfiguration',
        'enabled' => true
    ],
    'A06' => [
        'name' => 'Vulnerable Components',
        'path' => 'A06_VulnerableComponents',
        'enabled' => false
    ],
    'A07' => [
        'name' => 'Authentication Failures',
        'path' => 'A07_AuthenticationFailures',
        'enabled' => false
    ],
    'A08' => [
        'name' => 'Software & Data Integrity',
        'path' => 'A08_SoftwareDataIntegrity',
        'enabled' => false
    ],
    'A09' => [
        'name' => 'Logging & Monitoring',
        'path' => 'A09_LoggingMonitoring',
        'enabled' => false
    ],
    'A10' => [
        'name' => 'Server-Side Request Forgery',
        'path' => 'A10_SSRF',
        'enabled' => false
    ]
];

$levels = ['low', 'medium', 'hard', 'impossible'];
$level_names = [
    'low' => 'LOW',
    'medium' => 'MEDIUM',
    'hard' => 'HARD',
    'impossible' => 'IMPOSSIBLE'
];
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>OWASP Top 10</h2>
        <div class="flag-counter">
             Flags: <strong><?php echo get_total_flags(); ?></strong>
        </div>
    </div>
    
    <nav class="vuln-nav">
        <?php foreach ($vulnerabilities as $code => $vuln): ?>
            <?php if ($vuln['enabled']): ?>
                <div class="vuln-item <?php echo $current_module === $code ? 'active' : ''; ?>">
                    <div class="vuln-header" onclick="toggleVuln('<?php echo $code; ?>')">
                        <span class="vuln-code"><?php echo $code; ?></span>
                        <span class="vuln-name"><?php echo htmlspecialchars($vuln['name']); ?></span>
                        <span class="toggle-icon">▼</span>
                    </div>
                    
                    <div class="vuln-levels" style="display: <?php echo $current_module === $code ? 'block' : 'none'; ?>;">
                        <?php foreach ($levels as $level): ?>
                            <?php
                            $level_path = "/MedVulnLab/modules/{$vuln['path']}/{$level}.php";
                            $is_active = ($current_module === $code && $current_level === $level);
                            $has_flag_badge = has_flag($code, $level);
                            ?>
                            <a href="<?php echo $level_path; ?>" 
                               class="level-link <?php echo $is_active ? 'active' : ''; ?>">
                                <span>Level <?php echo array_search($level, $levels) + 1; ?> - <?php echo $level_names[$level]; ?></span>
                                <?php if ($has_flag_badge): ?>
                                    <span class="flag-badge">✓</span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="vuln-item disabled">
                    <div class="vuln-header">
                        <span class="vuln-code"><?php echo $code; ?></span>
                        <span class="vuln-name"><?php echo htmlspecialchars($vuln['name']); ?></span>
                        <span class="coming-soon">Coming Soon</span>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    
    <div class="sidebar-footer">
        <a href="/MedVulnLab/index.php" class="dashboard-link"> Dashboard</a>
        <a href="/MedVulnLab/auth/logout.php" class="logout-link"> Logout</a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show levels for active module
    const activeItem = document.querySelector('.vuln-item.active');
    if (activeItem) {
        const levels = activeItem.querySelector('.vuln-levels');
        if (levels) {
            levels.style.display = 'block';
        }
    }
    
    // Hide levels for inactive modules
    document.querySelectorAll('.vuln-item:not(.active)').forEach(item => {
        const levels = item.querySelector('.vuln-levels');
        if (levels) {
            levels.style.display = 'none';
        }
    });
});

function toggleVuln(code) {
    const item = event.currentTarget.closest('.vuln-item');
    const levels = item.querySelector('.vuln-levels');
    
    if (levels) {
        // Toggle visibility
        if (levels.style.display === 'none' || !levels.style.display) {
            levels.style.display = 'block';
        } else {
            levels.style.display = 'none';
        }
    } else {
        // Navigate to first level
        const paths = {
            'A01': 'A01_BrokenAccessControl',
            'A03': 'A03_Injection',
            'A05': 'A05_SecurityMisconfiguration'
        };
        const path = paths[code] || '';
        if (path) {
            window.location.href = `/MedVulnLab/modules/${path}/low.php`;
        }
    }
}
</script>

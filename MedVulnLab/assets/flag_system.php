<?php
/**
 * Flag System for Tracking Successful Exploits
 * 
 * Manages flags/achievements when vulnerabilities are successfully exploited
 */

require_once __DIR__ . '/../auth/session.php';

// Initialize flags if not set
if (!isset($_SESSION['flags'])) {
    $_SESSION['flags'] = [];
}

/**
 * Set a flag for successfully exploiting a vulnerability
 */
function set_flag($module, $level, $flag_code = null) {
    if (!isset($_SESSION['flags'][$module])) {
        $_SESSION['flags'][$module] = [];
    }
    
    $_SESSION['flags'][$module][$level] = true;
    
    // Store flag code if provided
    if ($flag_code !== null) {
        if (!isset($_SESSION['flag_codes'])) {
            $_SESSION['flag_codes'] = [];
        }
        $_SESSION['flag_codes'][$module][$level] = $flag_code;
    }
    
    return true;
}

/**
 * Check if a flag has been set
 */
function has_flag($module, $level) {
    return isset($_SESSION['flags'][$module][$level]) && $_SESSION['flags'][$module][$level] === true;
}

/**
 * Get flag code for a module/level
 */
function get_flag_code($module, $level) {
    if (isset($_SESSION['flag_codes'][$module][$level])) {
        return $_SESSION['flag_codes'][$module][$level];
    }
    
    // Generate default flag code
    return strtoupper($module . '_' . strtoupper(substr($level, 0, 1)) . '_' . bin2hex(random_bytes(4)));
}

/**
 * Display flag success message
 */
function display_flag_success($module, $level, $flag_code = null) {
    if ($flag_code === null) {
        $flag_code = get_flag_code($module, $level);
    }
    
    ?>
    <div class="flag-success">
        <h3>🎉 Vulnerability Exploited Successfully!</h3>
        <p>You have successfully exploited this vulnerability!</p>
        <div class="flag-code">FLAG: <?php echo htmlspecialchars($flag_code); ?></div>
        <p style="font-size: 14px; color: var(--text-secondary);">
            This flag has been saved to your session. Check the sidebar to see your progress!
        </p>
    </div>
    <?php
}

/**
 * Get total flag count
 */
function get_total_flags() {
    $count = 0;
    if (isset($_SESSION['flags'])) {
        foreach ($_SESSION['flags'] as $module_flags) {
            if (is_array($module_flags)) {
                $count += count(array_filter($module_flags));
            }
        }
    }
    return $count;
}

/**
 * Get flags by module
 */
function get_module_flags($module) {
    if (isset($_SESSION['flags'][$module])) {
        return array_filter($_SESSION['flags'][$module]);
    }
    return [];
}

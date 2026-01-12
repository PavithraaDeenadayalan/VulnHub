<?php
/**
 * A03 Injection - LOW Level
 * 
 * VULNERABILITY: SQL Injection (simulated with array search)
 * 
 * The application uses user input directly in data queries without sanitization.
 * While we're using PHP arrays instead of SQL, the vulnerability pattern is the same:
 * user input is trusted and used directly in search/filter operations.
 * 
 * DESIGN MISTAKE:
 * - User input is not sanitized or validated
 * - Direct string matching without escaping
 * - No input validation or whitelisting
 * 
 * EXPLOITATION:
 * - Use special characters to manipulate search logic
 * - Inject array search patterns
 * - Bypass filters using wildcards or pattern matching
 */

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../data/seed.php';
require_once __DIR__ . '/../../assets/flag_system.php';

require_auth();

require_once __DIR__ . '/../../assets/warning_banner.php';
require_once __DIR__ . '/../../assets/sidebar.php';
require_once __DIR__ . '/../../assets/layout.php';
require_once __DIR__ . '/../../assets/module_template.php';

$patient_records = get_patient_records();
$search_term = $_GET['search'] ?? '';
$results = [];
$flag_triggered = false;

// VULNERABILITY: Direct use of user input without sanitization
// This simulates SQL injection - user input is used directly in search
if (!empty($search_term)) {
    // Simulated "SQL injection" - using array search with direct string matching
    foreach ($patient_records as $patient_id => $record) {
        // Vulnerable: Direct string matching without escaping
        if (stripos($record['name'], $search_term) !== false || 
            stripos($record['medical_history'], $search_term) !== false ||
            $patient_id === $search_term) {
            $results[$patient_id] = $record;
        }
    }
    
    // Flag trigger: If search returns all records (injection successful)
    // Try searching for empty string or wildcard pattern
    if (count($results) >= count($patient_records) && $search_term !== '') {
        if (!has_flag('A03', 'low')) {
            set_flag('A03', 'low', 'A03_LOW_' . strtoupper(bin2hex(random_bytes(4))));
            $flag_triggered = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A03 - LOW: Injection</title>
    <?php echo get_dark_mode_styles(); ?>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A03', 'Patient Search', 'low', 'LOW'); ?>
            <?php render_level_switcher('A03', 'A03_Injection', 'low'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A03', 'low', get_flag_code('A03', 'low')); ?>
            <?php endif; ?>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Injection (Array Search)</h3>
                <p><strong>Issue:</strong> User input is used directly in search operations without sanitization or validation.</p>
                <p><strong>Design Mistake:</strong> No input validation, no escaping, direct string matching.</p>
                <p><strong>Challenge:</strong> Can you manipulate the search to return all patient records? Try using special characters or patterns!</p>
            </div>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="search">Search Patients (Name, Medical History, or Patient ID):</label>
                        <input type="text" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search_term); ?>" 
                               placeholder="Enter search term..." required>
                    </div>
                    <button type="submit">Search</button>
                </form>
            </div>
            
            <?php if (!empty($search_term)): ?>
                <div class="content-wrapper" style="margin-top: 20px;">
                    <h3 style="color: var(--text-primary); margin-bottom: 15px;">
                        Search Results (<?php echo count($results); ?> found):
                    </h3>
                    
                    <?php if (empty($results)): ?>
                        <p style="color: var(--text-secondary);">No results found.</p>
                    <?php else: ?>
                        <?php foreach ($results as $patient_id => $record): ?>
                            <div style="background: var(--bg-tertiary); padding: 15px; border-radius: 5px; margin-bottom: 10px; border-left: 4px solid var(--accent-primary);">
                                <h4 style="color: var(--text-primary); margin-bottom: 8px;">
                                    <?php echo htmlspecialchars($record['name']); ?> (<?php echo $patient_id; ?>)
                                </h4>
                                <p style="color: var(--text-secondary); font-size: 14px;">
                                    <strong>Medical History:</strong> <?php echo htmlspecialchars($record['medical_history']); ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

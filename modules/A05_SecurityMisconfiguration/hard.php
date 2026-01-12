<?php
/**
 * A05 Security Misconfiguration - HARD Level
 * 
 * VULNERABILITY: Insecure Direct Object References (IDOR) via Predictable IDs
 * 
 * The application uses predictable or sequential IDs for resources, allowing
 * attackers to enumerate and access resources they shouldn't have access to.
 * Combined with missing authorization checks, this becomes a serious vulnerability.
 * 
 * DESIGN MISTAKE:
 * - Predictable resource identifiers
 * - Missing authorization checks
 * - No access control validation
 * - Sequential IDs easily guessable
 * 
 * EXPLOITATION:
 * - Enumerate resources by guessing IDs
 * - Access resources belonging to other users
 * - Bypass authorization through ID manipulation
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

$user_id = get_user_id();
$current_user = get_user_by_id($user_id);

// Simulated patient records with predictable IDs
$all_patients = get_patient_records();
$view_patient_id = $_GET['patient_id'] ?? null;
$viewed_patient = null;
$flag_triggered = false;

// VULNERABILITY: Predictable IDs and missing authorization
// Patient IDs are sequential (pat001, pat002, etc.) and easily guessable
if ($view_patient_id && isset($all_patients[$view_patient_id])) {
    $viewed_patient = $all_patients[$view_patient_id];
    
    // VULNERABILITY: No check if current user should have access
    // Any authenticated user can view any patient record by guessing the ID
    
    // Flag trigger: If viewing patient not assigned to current user (for doctors)
    // or viewing any patient as non-doctor
    if ($current_user['role'] === 'doctor') {
        if (!in_array($view_patient_id, $current_user['assigned_patients'])) {
            if (!has_flag('A05', 'hard')) {
                set_flag('A05', 'hard', 'A05_HARD_' . strtoupper(bin2hex(random_bytes(4))));
                $flag_triggered = true;
            }
        }
    } elseif ($current_user['role'] !== 'patient' || $view_patient_id !== $user_id) {
        // Non-patient users viewing any patient, or patients viewing other patients
        if (!has_flag('A05', 'hard')) {
            set_flag('A05', 'hard', 'A05_HARD_' . strtoupper(bin2hex(random_bytes(4))));
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
    <title>A05 - HARD: Security Misconfiguration</title>
    <?php echo get_dark_mode_styles(); ?>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A05', 'Patient Record Viewer', 'hard', 'HARD'); ?>
            <?php render_level_switcher('A05', 'A05_SecurityMisconfiguration', 'hard'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A05', 'hard', get_flag_code('A05', 'hard')); ?>
            <?php endif; ?>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Predictable IDs & Missing Authorization</h3>
                <p><strong>Issue:</strong> Resource IDs are predictable (sequential) and authorization checks are missing, allowing unauthorized access.</p>
                <p><strong>Design Mistake:</strong> Sequential IDs easily guessable, no access control validation, IDOR vulnerability.</p>
                <p><strong>Challenge:</strong> Can you access patient records you shouldn't have access to? Try guessing patient IDs (pat001, pat002, etc.)!</p>
            </div>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <h3 style="color: var(--text-primary); margin-bottom: 15px;">View Patient Record</h3>
                <form method="GET" action="">
                    <div class="form-group">
                        <label for="patient_id">Patient ID:</label>
                        <input type="text" id="patient_id" name="patient_id" 
                               value="<?php echo htmlspecialchars($view_patient_id ?? ''); ?>" 
                               placeholder="e.g., pat001" required>
                    </div>
                    <button type="submit">View Record</button>
                </form>
            </div>
            
            <?php if ($viewed_patient): ?>
                <div class="content-wrapper" style="margin-top: 20px; border: 2px solid var(--accent-primary);">
                    <h3 style="color: var(--text-primary); margin-bottom: 15px;">
                        Patient Record: <?php echo htmlspecialchars($viewed_patient['name']); ?>
                    </h3>
                    <div style="background: var(--bg-primary); padding: 20px; border-radius: 5px;">
                        <p style="color: var(--text-secondary); margin: 10px 0;">
                            <strong>Patient ID:</strong> <?php echo htmlspecialchars($viewed_patient['patient_id']); ?>
                        </p>
                        <p style="color: var(--text-secondary); margin: 10px 0;">
                            <strong>Date of Birth:</strong> <?php echo htmlspecialchars($viewed_patient['dob']); ?>
                        </p>
                        <p style="color: var(--text-secondary); margin: 10px 0;">
                            <strong>Medical History:</strong> <?php echo htmlspecialchars($viewed_patient['medical_history']); ?>
                        </p>
                        <p style="color: var(--text-secondary); margin: 10px 0;">
                            <strong>Current Medications:</strong> <?php echo htmlspecialchars(implode(', ', $viewed_patient['current_medications'])); ?>
                        </p>
                        <p style="color: var(--text-secondary); margin: 10px 0;">
                            <strong>Allergies:</strong> <?php echo empty($viewed_patient['allergies']) ? 'None' : htmlspecialchars(implode(', ', $viewed_patient['allergies'])); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="info-box" style="margin-top: 20px;">
                <h4 style="color: var(--info); margin-bottom: 8px;">💡 Hint:</h4>
                <p style="color: var(--text-secondary); font-size: 14px;">
                    Patient IDs follow a predictable pattern: pat001, pat002, pat003, etc. 
                    Try accessing different patient IDs to see if you can view records you shouldn't have access to.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

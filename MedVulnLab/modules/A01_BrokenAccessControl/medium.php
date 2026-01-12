<?php
/**
 * A01 Broken Access Control - MEDIUM Level
 * 
 * VULNERABILITY: Frontend-Only Access Control Enforcement
 * 
 * The application enforces access control restrictions in the frontend
 * (JavaScript/HTML), but the backend assumes these restrictions are respected.
 * An attacker can bypass the UI restrictions using browser DevTools or
 * by making direct POST requests.
 * 
 * DESIGN MISTAKE:
 * - Access control logic exists only in frontend JavaScript
 * - Backend does not re-validate permissions
 * - Trusts that UI restrictions cannot be bypassed
 * 
 * EXPLOITATION:
 * - Remove disabled attribute from form fields using DevTools
 * - Modify hidden form fields
 * - Send direct POST requests bypassing the UI
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

$user_role = get_user_role();
$user_id = get_user_id();
$patient_records = get_patient_records();
$message = '';
$error = '';
$flag_triggered = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $patient_id = $_POST['patient_id'] ?? '';
    $prescription_data = $_POST['prescription'] ?? '';
    
    // VULNERABILITY: Backend assumes frontend restrictions are enforced
    // No server-side validation of whether user should be able to perform this action
    if ($action === 'create_prescription' && !empty($patient_id) && !empty($prescription_data)) {
        if (isset($patient_records[$patient_id])) {
            $message = "Prescription created successfully for patient: " . htmlspecialchars($patient_records[$patient_id]['name']);
            
            // Flag trigger: If non-doctor successfully creates prescription
            if ($user_role !== 'doctor') {
                if (!has_flag('A01', 'medium')) {
                    set_flag('A01', 'medium', 'A01_MED_' . strtoupper(bin2hex(random_bytes(4))));
                    $flag_triggered = true;
                }
            }
        } else {
            $error = "Invalid patient ID.";
        }
    } else {
        $error = "Missing required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A01 - MEDIUM: Broken Access Control</title>
    <?php echo get_dark_mode_styles(); ?>
    <style>
    </style>
    <script>
        // VULNERABILITY: Access control enforced only in JavaScript
        // This can be bypassed by modifying the DOM or sending direct POST requests
        
        document.addEventListener('DOMContentLoaded', function() {
            const userRole = '<?php echo $user_role; ?>';
            const prescriptionForm = document.getElementById('prescriptionForm');
            const patientSelect = document.getElementById('patient_id');
            const prescriptionTextarea = document.getElementById('prescription');
            const submitButton = document.querySelector('button[type="submit"]');
            
            // Frontend restriction: Only doctors can create prescriptions
            if (userRole !== 'doctor') {
                // Disable form elements
                patientSelect.disabled = true;
                prescriptionTextarea.disabled = true;
                submitButton.disabled = true;
                
                // Show message
                const restrictionMsg = document.createElement('div');
                restrictionMsg.className = 'error';
                restrictionMsg.textContent = 'Access Denied: Only doctors can create prescriptions.';
                prescriptionForm.insertBefore(restrictionMsg, prescriptionForm.firstChild);
            }
        });
    </script>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A01', 'Create Prescription', 'medium', 'MEDIUM'); ?>
            <?php render_level_switcher('A01', 'A01_BrokenAccessControl', 'medium'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A01', 'medium', get_flag_code('A01', 'medium')); ?>
            <?php endif; ?>
        
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Frontend-Only Access Control</h3>
                <p><strong>Issue:</strong> Access control is enforced only in JavaScript. The backend trusts that UI restrictions cannot be bypassed.</p>
                <p><strong>Design Mistake:</strong> No server-side re-validation of permissions. Frontend restrictions can be removed using DevTools.</p>
                <p><strong>Challenge:</strong> Can you create a prescription even if you're not a doctor? Try using browser DevTools!</p>
            </div>
            
            <div class="info-box">
                <p><strong>Your Current Role:</strong> <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $user_role))); ?></p>
                <p><strong>Required Role:</strong> Doctor</p>
            </div>
            
            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="content-wrapper" style="margin-top: 20px;">
            <form id="prescriptionForm" method="POST" action="">
                <input type="hidden" name="action" value="create_prescription">
                
                <div class="form-group">
                    <label for="patient_id">Select Patient:</label>
                    <select name="patient_id" id="patient_id" required>
                        <option value="">-- Select Patient --</option>
                        <?php foreach ($patient_records as $pat_id => $record): ?>
                            <option value="<?php echo htmlspecialchars($pat_id); ?>">
                                <?php echo htmlspecialchars($record['name']); ?> (<?php echo $pat_id; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="prescription">Prescription Details:</label>
                    <textarea name="prescription" id="prescription" required placeholder="Enter medication, dosage, and instructions..."></textarea>
                </div>
                
                <button type="submit">Create Prescription</button>
            </form>
            </div>
        </div>
    </div>
</body>
</html>

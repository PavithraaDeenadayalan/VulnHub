<?php
/**
 * A01 Broken Access Control - LOW Level
 * 
 * VULNERABILITY: Missing Relationship/Context Validation
 * 
 * The application correctly validates that the user has the "doctor" role,
 * but it does NOT validate that the doctor is assigned to the patient
 * whose records they are trying to access.
 * 
 * DESIGN MISTAKE:
 * - Role-based access control is implemented
 * - Relationship-based access control is missing
 * - Assumes that if you're a doctor, you can access any patient record
 * 
 * EXPLOITATION:
 * - Doctor can view records of patients not assigned to them
 * - No URL manipulation needed - just normal app usage
 * - Exploitable through the patient selection dropdown
 */

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../data/seed.php';
require_once __DIR__ . '/../../assets/flag_system.php';

require_role('doctor');

require_once __DIR__ . '/../../assets/warning_banner.php';
require_once __DIR__ . '/../../assets/sidebar.php';
require_once __DIR__ . '/../../assets/layout.php';
require_once __DIR__ . '/../../assets/module_template.php';

$user_id = get_user_id();
$current_user = get_user_by_id($user_id);
$patient_records = get_patient_records();
$selected_patient_id = $_GET['patient_id'] ?? null;
$flag_triggered = false;

// VULNERABILITY: Only checks role, not assignment relationship
// A doctor can access ANY patient record, not just their assigned patients
if ($selected_patient_id && isset($patient_records[$selected_patient_id])) {
    $selected_record = $patient_records[$selected_patient_id];
    
    // Check if accessing unassigned patient (exploit successful)
    if (!in_array($selected_patient_id, $current_user['assigned_patients'])) {
        if (!has_flag('A01', 'low')) {
            set_flag('A01', 'low', 'A01_LOW_' . strtoupper(bin2hex(random_bytes(4))));
            $flag_triggered = true;
        }
    }
} else {
    $selected_record = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A01 - LOW: Broken Access Control</title>
    <?php echo get_dark_mode_styles(); ?>
    <style>
        .assigned-patients {
            background: var(--bg-tertiary);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid var(--info);
        }
        .assigned-patients h3 {
            color: var(--info);
            margin-bottom: 10px;
        }
        .assigned-patients ul {
            list-style: none;
            margin-left: 0;
            color: var(--text-secondary);
        }
        .assigned-patients li {
            padding: 5px 0;
        }
        .patient-selector {
            background: var(--bg-tertiary);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .patient-selector form {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        .patient-record {
            background: var(--bg-tertiary);
            padding: 25px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .patient-record h2 {
            color: var(--text-primary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-primary);
        }
        .record-section {
            margin-bottom: 20px;
        }
        .record-section h3 {
            color: var(--accent-primary);
            margin-bottom: 10px;
        }
        .record-section p {
            color: var(--text-secondary);
            line-height: 1.6;
        }
        .record-section ul {
            margin-left: 20px;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A01', 'View Patient Medical Records', 'low', 'LOW'); ?>
            <?php render_level_switcher('A01', 'A01_BrokenAccessControl', 'low'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A01', 'low', get_flag_code('A01', 'low')); ?>
            <?php endif; ?>
        
        <div class="vulnerability-info">
            <h3>⚠️ Vulnerability: Missing Relationship Validation</h3>
            <p><strong>Issue:</strong> The application validates your role (doctor) but does NOT validate that you are assigned to the patient whose records you're accessing.</p>
            <p><strong>Design Mistake:</strong> Role-based access control exists, but relationship-based access control is missing.</p>
            <p><strong>Challenge:</strong> Can you view medical records of patients NOT assigned to you?</p>
        </div>
        
        <div class="assigned-patients">
            <h3>Your Assigned Patients:</h3>
            <ul>
                <?php foreach ($current_user['assigned_patients'] as $pat_id): ?>
                    <li><?php echo htmlspecialchars($patient_records[$pat_id]['name'] ?? $pat_id); ?> (<?php echo $pat_id; ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="patient-selector">
            <form method="GET" action="">
                <div class="form-group">
                    <label for="patient_id">Select Patient to View Records:</label>
                    <select name="patient_id" id="patient_id" required>
                        <option value="">-- Select Patient --</option>
                        <?php foreach ($patient_records as $pat_id => $record): ?>
                            <option value="<?php echo htmlspecialchars($pat_id); ?>" 
                                    <?php echo ($selected_patient_id === $pat_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($record['name']); ?> (<?php echo $pat_id; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">View Records</button>
            </form>
        </div>
        
        <?php if ($selected_record): ?>
            <div class="patient-record">
                <h2>Medical Record: <?php echo htmlspecialchars($selected_record['name']); ?></h2>
                
                <div class="record-section">
                    <h3>Patient Information</h3>
                    <p><strong>Patient ID:</strong> <?php echo htmlspecialchars($selected_record['patient_id']); ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($selected_record['dob']); ?></p>
                    <p><strong>Assigned Doctor:</strong> <?php echo htmlspecialchars($selected_record['assigned_doctor']); ?></p>
                </div>
                
                <div class="record-section">
                    <h3>Medical History</h3>
                    <p><?php echo htmlspecialchars($selected_record['medical_history']); ?></p>
                </div>
                
                <div class="record-section">
                    <h3>Current Medications</h3>
                    <ul>
                        <?php foreach ($selected_record['current_medications'] as $med): ?>
                            <li><?php echo htmlspecialchars($med); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="record-section">
                    <h3>Allergies</h3>
                    <ul>
                        <?php if (empty($selected_record['allergies'])): ?>
                            <li>None reported</li>
                        <?php else: ?>
                            <?php foreach ($selected_record['allergies'] as $allergy): ?>
                                <li><?php echo htmlspecialchars($allergy); ?></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                
                <div class="record-section">
                    <h3>Last Visit</h3>
                    <p><?php echo htmlspecialchars($selected_record['last_visit']); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

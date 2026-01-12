<?php
/**
 * A01 Broken Access Control - HARD Level
 * 
 * VULNERABILITY: Workflow State Bypass
 * 
 * The application implements a multi-step workflow:
 * diagnosis → lab_report → billing_approval → completed
 * 
 * The backend checks the user's role correctly, but does NOT validate
 * that the workflow is in the correct state before allowing actions.
 * An attacker can replay or reorder requests to skip workflow steps.
 * 
 * DESIGN MISTAKE:
 * - Role-based authorization exists
 * - Workflow state validation is missing
 * - Assumes requests arrive in correct sequence
 * - No state machine enforcement
 * 
 * EXPLOITATION:
 * - Intercept requests at different workflow stages
 * - Replay requests out of sequence
 * - Skip required workflow steps
 * - Requires understanding of application flow
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
$lab_reports = get_lab_reports();
$message = '';
$error = '';
$flag_triggered = false;

// Handle workflow actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $report_id = $_POST['report_id'] ?? '';
    
    if (empty($report_id) || !isset($lab_reports[$report_id])) {
        $error = "Invalid lab report ID.";
    } else {
        $report = $lab_reports[$report_id];
        
        // VULNERABILITY: Checks role but NOT workflow state
        // A billing officer can approve billing even if workflow hasn't reached that stage
        // A lab technician can submit reports even if diagnosis wasn't completed
        
        switch ($action) {
            case 'submit_lab_report':
                // Should only be allowed if workflow_state is 'diagnosis'
                // But we don't check that!
                if ($user_role === 'lab_technician') {
                    $message = "Lab report submitted successfully for Report ID: " . htmlspecialchars($report_id);
                    // Flag: If workflow state is wrong but action succeeded
                    if ($report['workflow_state'] !== 'diagnosis') {
                        if (!has_flag('A01', 'hard')) {
                            set_flag('A01', 'hard', 'A01_HARD_' . strtoupper(bin2hex(random_bytes(4))));
                            $flag_triggered = true;
                        }
                    }
                } else {
                    $error = "Only lab technicians can submit lab reports.";
                }
                break;
                
            case 'approve_billing':
                // Should only be allowed if workflow_state is 'lab_report'
                // But we don't check that!
                if ($user_role === 'billing_officer') {
                    $message = "Billing approved successfully for Report ID: " . htmlspecialchars($report_id);
                    // Flag: If workflow state is wrong but action succeeded
                    if ($report['workflow_state'] !== 'lab_report') {
                        if (!has_flag('A01', 'hard')) {
                            set_flag('A01', 'hard', 'A01_HARD_' . strtoupper(bin2hex(random_bytes(4))));
                            $flag_triggered = true;
                        }
                    }
                } else {
                    $error = "Only billing officers can approve billing.";
                }
                break;
                
            case 'complete_workflow':
                // Should only be allowed if workflow_state is 'billing_approval'
                // But we don't check that!
                if ($user_role === 'doctor') {
                    $message = "Workflow completed successfully for Report ID: " . htmlspecialchars($report_id);
                    // Flag: If workflow state is wrong but action succeeded
                    if ($report['workflow_state'] !== 'billing_approval') {
                        if (!has_flag('A01', 'hard')) {
                            set_flag('A01', 'hard', 'A01_HARD_' . strtoupper(bin2hex(random_bytes(4))));
                            $flag_triggered = true;
                        }
                    }
                } else {
                    $error = "Only doctors can complete workflows.";
                }
                break;
                
            default:
                $error = "Invalid action.";
        }
    }
}

// Get reports based on role
$available_reports = [];
foreach ($lab_reports as $report_id => $report) {
    // Show reports that are relevant to the user's role
    if ($user_role === 'lab_technician' || $user_role === 'billing_officer' || $user_role === 'doctor') {
        $available_reports[$report_id] = $report;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A01 - HARD: Broken Access Control</title>
    <?php echo get_dark_mode_styles(); ?>
    <style>
        .workflow-diagram {
            background: var(--bg-tertiary);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .workflow-diagram h3 {
            color: var(--text-primary);
            margin-bottom: 15px;
        }
        .workflow-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .workflow-step {
            flex: 1;
            min-width: 150px;
            padding: 15px;
            background: var(--bg-primary);
            border: 2px solid var(--accent-primary);
            border-radius: 5px;
            text-align: center;
            color: var(--text-primary);
        }
        .workflow-arrow {
            font-size: 24px;
            color: var(--accent-primary);
        }
        .report-card {
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
            background: var(--bg-tertiary);
        }
        .report-card h4 {
            color: var(--text-primary);
            margin-bottom: 10px;
        }
        .report-info {
            color: var(--text-secondary);
            margin: 5px 0;
            font-size: 14px;
        }
        .workflow-state {
            display: inline-block;
            padding: 4px 8px;
            background: var(--accent-primary);
            color: white;
            border-radius: 3px;
            font-size: 12px;
            margin-left: 10px;
        }
        .action-buttons {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }
        .btn-lab {
            background: #17a2b8;
            color: white;
        }
        .btn-billing {
            background: #28a745;
            color: white;
        }
        .btn-complete {
            background: var(--accent-primary);
            color: white;
        }
        .action-btn:hover {
            opacity: 0.9;
        }
        form {
            display: inline;
        }
    </style>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A01', 'Lab Report Workflow Management', 'hard', 'HARD'); ?>
            <?php render_level_switcher('A01', 'A01_BrokenAccessControl', 'hard'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A01', 'hard', get_flag_code('A01', 'hard')); ?>
            <?php endif; ?>
        
        <div class="vulnerability-info">
            <h3>⚠️ Vulnerability: Workflow State Bypass</h3>
            <p><strong>Issue:</strong> The application checks your role but does NOT validate that the workflow is in the correct state before allowing actions.</p>
            <p><strong>Design Mistake:</strong> No state machine enforcement. Requests can be replayed or reordered to skip workflow steps.</p>
            <p><strong>Challenge:</strong> Can you complete the workflow out of sequence? Try intercepting and replaying requests!</p>
        </div>
        
            <div class="info-box">
                <p><strong>Your Current Role:</strong> <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $user_role))); ?></p>
                <p><strong>Expected Workflow:</strong> Diagnosis → Lab Report → Billing Approval → Completed</p>
            </div>
            
            <div class="workflow-diagram">
            <h3>Workflow Steps:</h3>
            <div class="workflow-steps">
                <div class="workflow-step">
                    <strong>1. Diagnosis</strong><br>
                    <small>Doctor creates diagnosis</small>
                </div>
                <span class="workflow-arrow">→</span>
                <div class="workflow-step">
                    <strong>2. Lab Report</strong><br>
                    <small>Lab Tech submits report</small>
                </div>
                <span class="workflow-arrow">→</span>
                <div class="workflow-step">
                    <strong>3. Billing Approval</strong><br>
                    <small>Billing Officer approves</small>
                </div>
                <span class="workflow-arrow">→</span>
                <div class="workflow-step">
                    <strong>4. Completed</strong><br>
                    <small>Workflow finished</small>
                </div>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
            <div class="content-wrapper" style="margin-top: 20px;">
                <h2 style="color: var(--text-primary); margin-bottom: 15px;">Available Lab Reports</h2>
            
            <?php if (empty($available_reports)): ?>
                <p>No reports available for your role.</p>
            <?php else: ?>
                <?php foreach ($available_reports as $report_id => $report): ?>
                    <div class="report-card">
                        <h4>
                            Report: <?php echo htmlspecialchars($report_id); ?>
                            <span class="workflow-state">State: <?php echo htmlspecialchars($report['workflow_state']); ?></span>
                        </h4>
                        <div class="report-info">
                            <strong>Patient ID:</strong> <?php echo htmlspecialchars($report['patient_id']); ?><br>
                            <strong>Test Type:</strong> <?php echo htmlspecialchars($report['test_type']); ?><br>
                            <strong>Result:</strong> <?php echo htmlspecialchars($report['result']); ?><br>
                            <strong>Created:</strong> <?php echo htmlspecialchars($report['created_at']); ?>
                        </div>
                        
                        <div class="action-buttons">
                            <?php if ($user_role === 'lab_technician'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="submit_lab_report">
                                    <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report_id); ?>">
                                    <button type="submit" class="action-btn btn-lab">Submit Lab Report</button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($user_role === 'billing_officer'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="approve_billing">
                                    <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report_id); ?>">
                                    <button type="submit" class="action-btn btn-billing">Approve Billing</button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($user_role === 'doctor'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="complete_workflow">
                                    <input type="hidden" name="report_id" value="<?php echo htmlspecialchars($report_id); ?>">
                                    <button type="submit" class="action-btn btn-complete">Complete Workflow</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

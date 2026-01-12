<?php
/**
 * A01 Broken Access Control - IMPOSSIBLE Level
 * 
 * VULNERABILITY: Session/Token Reuse Without Ownership Revalidation
 * 
 * The application generates approval tokens for billing operations.
 * These tokens are valid and properly signed, but the application
 * does NOT revalidate that the token belongs to the current user's
 * session or that the user owns the resource being accessed.
 * 
 * An attacker can:
 * - Reuse tokens from intercepted requests
 * - Use tokens from other users' sessions
 * - Exploit race conditions in token validation
 * 
 * DESIGN MISTAKE:
 * - Token validation exists but ownership check is missing
 * - Session is valid, but resource ownership is not revalidated
 * - Token can be reused across different contexts
 * - No binding between token, session, and resource
 * 
 * EXPLOITATION:
 * - Intercept approval tokens from network traffic
 * - Reuse tokens in different sessions
 * - Requires deep understanding of application flow
 */

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../data/seed.php';
require_once __DIR__ . '/../../assets/flag_system.php';

require_role('billing_officer');

require_once __DIR__ . '/../../assets/warning_banner.php';
require_once __DIR__ . '/../../assets/sidebar.php';
require_once __DIR__ . '/../../assets/layout.php';
require_once __DIR__ . '/../../assets/module_template.php';

$user_id = get_user_id();
$billing_records = get_billing_records();
$message = '';
$error = '';
$flag_triggered = false;

// Handle billing approval
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $billing_id = $_POST['billing_id'] ?? '';
    $approval_token = $_POST['approval_token'] ?? '';
    
    if ($action === 'approve_billing') {
        if (empty($billing_id) || !isset($billing_records[$billing_id])) {
            $error = "Invalid billing ID.";
        } elseif (empty($approval_token)) {
            $error = "Approval token is required.";
        } else {
            $billing = $billing_records[$billing_id];
            
            // VULNERABILITY: Token is validated but ownership is NOT revalidated
            // The token matches the billing record, but we don't check:
            // - If the current user should have access to this billing record
            // - If the token was generated for this specific session
            // - If the token has already been used
            
            $expected_token = $billing['approval_token'];
            
            if ($approval_token === $expected_token) {
                // Token is valid, but we don't check ownership!
                // Any billing officer can use any valid token
                $message = "Billing approved successfully! Billing ID: " . htmlspecialchars($billing_id) . 
                          " | Amount: $" . number_format($billing['amount'], 2);
                
                // Flag trigger: If using token from different billing record (token reuse)
                // Check if token was used for different billing_id than originally intended
                if (!has_flag('A01', 'impossible')) {
                    set_flag('A01', 'impossible', 'A01_IMP_' . strtoupper(bin2hex(random_bytes(4))));
                    $flag_triggered = true;
                }
            } else {
                $error = "Invalid approval token.";
            }
        }
    }
}

// Generate approval tokens (simulating what would happen when billing is created)
// In a real app, these would be generated server-side when billing records are created
function generate_approval_token($billing_id, $patient_id, $date) {
    return 'token_' . md5($billing_id . $patient_id . $date);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A01 - IMPOSSIBLE: Broken Access Control</title>
    <?php echo get_dark_mode_styles(); ?>
    <style>
        .billing-card {
            border: 1px solid var(--border-color);
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            background: var(--bg-tertiary);
        }
        .billing-card h3 {
            color: var(--text-primary);
            margin-bottom: 15px;
        }
        .billing-info {
            color: var(--text-secondary);
            margin: 8px 0;
            font-size: 14px;
        }
        .billing-info strong {
            color: var(--text-primary);
        }
        .approval-form {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid var(--border-color);
        }
        .token-hint {
            background: var(--bg-primary);
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 12px;
            color: var(--text-secondary);
        }
        .token-hint code {
            background: var(--bg-tertiary);
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
            color: var(--text-primary);
        }
        input[type="text"] {
            font-family: monospace;
        }
    </style>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A01', 'Billing Approval System', 'impossible', 'IMPOSSIBLE'); ?>
            <?php render_level_switcher('A01', 'A01_BrokenAccessControl', 'impossible'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A01', 'impossible', get_flag_code('A01', 'impossible')); ?>
            <?php endif; ?>
        
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Token Reuse Without Ownership Revalidation</h3>
                <p><strong>Issue:</strong> Approval tokens are validated, but the application does NOT revalidate that the token belongs to the current user's session or that the user owns the resource.</p>
                <p><strong>Design Mistake:</strong> Token validation exists but ownership check is missing. Tokens can be reused across different sessions.</p>
                <p><strong>Challenge:</strong> Can you approve billing records using tokens from intercepted requests? Try using browser DevTools to inspect network traffic!</p>
            </div>
            
            <div class="info-box">
                <h4 style="color: var(--info); margin-bottom: 8px;">💡 Hint for Exploitation:</h4>
                <p style="color: var(--text-secondary); font-size: 14px;">Approval tokens are generated when billing records are created. These tokens are sent to the server when approving billing.</p>
                <p style="color: var(--text-secondary); font-size: 14px;">Try using browser DevTools (Network tab) to intercept requests and capture approval tokens. Then reuse those tokens in different contexts.</p>
                <p style="color: var(--text-secondary); font-size: 14px;">Alternatively, tokens are visible in the HTML source - can you find and reuse them?</p>
            </div>
            
            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <h2 style="color: var(--text-primary); margin-bottom: 15px;">Pending Billing Approvals</h2>
            
            <?php if (empty($billing_records)): ?>
                <p>No billing records available.</p>
            <?php else: ?>
                <?php foreach ($billing_records as $billing_id => $billing): ?>
                    <div class="billing-card">
                        <h3>Billing Record: <?php echo htmlspecialchars($billing_id); ?></h3>
                        
                        <div class="billing-info">
                            <strong>Patient ID:</strong> <?php echo htmlspecialchars($billing['patient_id']); ?><br>
                            <strong>Appointment ID:</strong> <?php echo htmlspecialchars($billing['appointment_id']); ?><br>
                            <strong>Amount:</strong> $<?php echo number_format($billing['amount'], 2); ?><br>
                            <strong>Status:</strong> <?php echo htmlspecialchars($billing['status']); ?><br>
                            <strong>Created:</strong> <?php echo htmlspecialchars($billing['created_at']); ?>
                        </div>
                        
                        <div class="approval-form">
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="approve_billing">
                                <input type="hidden" name="billing_id" value="<?php echo htmlspecialchars($billing_id); ?>">
                                
                                <div class="form-group">
                                    <label for="approval_token_<?php echo htmlspecialchars($billing_id); ?>">
                                        Approval Token:
                                    </label>
                                    <input 
                                        type="text" 
                                        id="approval_token_<?php echo htmlspecialchars($billing_id); ?>" 
                                        name="approval_token" 
                                        value="<?php echo htmlspecialchars($billing['approval_token']); ?>"
                                        required
                                        placeholder="Enter approval token"
                                    >
                                </div>
                                
                                <button type="submit">Approve Billing</button>
                            </form>
                            
                            <div class="token-hint">
                                <strong>Token for this record:</strong> <code><?php echo htmlspecialchars($billing['approval_token']); ?></code><br>
                                <small>This token is pre-filled for convenience. In a real scenario, you'd need to intercept it from network traffic or extract it from another user's session.</small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <h2 style="color: var(--text-primary); margin-bottom: 15px;">Manual Token Entry</h2>
            <p style="color: #666; margin-bottom: 15px;">
                Try entering a token manually. Can you approve billing using tokens from other billing records?
            </p>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="approve_billing">
                
                <div class="form-group">
                    <label for="manual_billing_id">Billing ID:</label>
                    <input type="text" id="manual_billing_id" name="billing_id" required placeholder="e.g., bill001">
                </div>
                
                <div class="form-group">
                    <label for="manual_token">Approval Token:</label>
                    <input type="text" id="manual_token" name="approval_token" required placeholder="Enter any approval token">
                </div>
                
                <button type="submit">Approve Billing</button>
            </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
/**
 * A03 Injection - IMPOSSIBLE Level
 * 
 * VULNERABILITY: Second-Order Injection / Stored XSS + Code Execution
 * 
 * The application stores user input and later uses it in a vulnerable context.
 * This is a second-order injection where malicious data is stored first,
 * then executed when retrieved and used unsafely.
 * 
 * DESIGN MISTAKE:
 * - Data is stored without proper sanitization
 * - Stored data is later used in vulnerable contexts
 * - No output encoding or context-aware escaping
 * 
 * EXPLOITATION:
 * - Store malicious payload in one part of application
 * - Trigger execution when data is retrieved and used
 * - Requires understanding of data flow through the application
 */

require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../auth/session.php';
require_once __DIR__ . '/../../assets/flag_system.php';

require_auth();

require_once __DIR__ . '/../../assets/warning_banner.php';
require_once __DIR__ . '/../../assets/sidebar.php';
require_once __DIR__ . '/../../assets/layout.php';
require_once __DIR__ . '/../../assets/module_template.php';

// Simulated storage (in real app, this would be a database)
$storage_file = __DIR__ . '/../../data/user_notes.json';
$notes = [];
$message = '';
$error = '';
$flag_triggered = false;

// Load existing notes
if (file_exists($storage_file)) {
    $notes = json_decode(file_get_contents($storage_file), true) ?: [];
}

// Handle note submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_note') {
        $note_content = $_POST['note'] ?? '';
        $user_id = get_user_id();
        
        if (!empty($note_content)) {
            // VULNERABILITY: Storing user input without sanitization
            $notes[] = [
                'id' => count($notes) + 1,
                'user_id' => $user_id,
                'content' => $note_content, // Not sanitized!
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            file_put_contents($storage_file, json_encode($notes, JSON_PRETTY_PRINT));
            $message = "Note added successfully!";
        } else {
            $error = "Note content cannot be empty.";
        }
    }
}

// Handle note viewing/execution
$view_note_id = $_GET['view'] ?? null;
$viewed_note = null;

if ($view_note_id !== null) {
    foreach ($notes as $note) {
        if ($note['id'] == $view_note_id) {
            $viewed_note = $note;
            
            // VULNERABILITY: Using stored data in vulnerable context
            // If note contains PHP code, it might be executed
            $content = $note['content'];
            
            // Dangerous: Using stored content in eval-like context
            // This simulates second-order injection
            if (strpos($content, '<?php') !== false || strpos($content, '<?=') !== false) {
                // Flag trigger: Code execution detected
                if (!has_flag('A03', 'impossible')) {
                    set_flag('A03', 'impossible', 'A03_IMP_' . strtoupper(bin2hex(random_bytes(4))));
                    $flag_triggered = true;
                }
            }
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A03 - IMPOSSIBLE: Injection</title>
    <?php echo get_dark_mode_styles(); ?>
</head>
<body>
    <?php display_warning_banner(); ?>
    <?php include __DIR__ . '/../../assets/sidebar.php'; ?>
    
    <div class="main-container">
        <div class="content-wrapper">
            <?php render_module_header('A03', 'User Notes System', 'impossible', 'IMPOSSIBLE'); ?>
            <?php render_level_switcher('A03', 'A03_Injection', 'impossible'); ?>
            
            <?php if ($flag_triggered): ?>
                <?php display_flag_success('A03', 'impossible', get_flag_code('A03', 'impossible')); ?>
            <?php endif; ?>
            
            <div class="vulnerability-info">
                <h3>⚠️ Vulnerability: Second-Order Injection</h3>
                <p><strong>Issue:</strong> User input is stored without sanitization and later used in vulnerable contexts.</p>
                <p><strong>Design Mistake:</strong> No input validation on storage, no output encoding on retrieval, stored data trusted.</p>
                <p><strong>Challenge:</strong> Can you store a payload that executes when the note is viewed? This requires understanding the data flow!</p>
            </div>
            
            <?php if ($message): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <h3 style="color: var(--text-primary); margin-bottom: 15px;">Add New Note</h3>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_note">
                    <div class="form-group">
                        <label for="note">Note Content:</label>
                        <textarea id="note" name="note" rows="4" required placeholder="Enter your note..."></textarea>
                    </div>
                    <button type="submit">Save Note</button>
                </form>
            </div>
            
            <div class="content-wrapper" style="margin-top: 20px;">
                <h3 style="color: var(--text-primary); margin-bottom: 15px;">Your Notes</h3>
                <?php if (empty($notes)): ?>
                    <p style="color: var(--text-secondary);">No notes yet.</p>
                <?php else: ?>
                    <?php foreach ($notes as $note): ?>
                        <div style="background: var(--bg-tertiary); padding: 15px; border-radius: 5px; margin-bottom: 10px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="color: var(--text-secondary); font-size: 12px;">
                                    Note #<?php echo $note['id']; ?> - <?php echo htmlspecialchars($note['created_at']); ?>
                                </span>
                                <a href="?view=<?php echo $note['id']; ?>" class="btn" style="padding: 6px 12px; font-size: 14px;">View</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if ($viewed_note): ?>
                <div class="content-wrapper" style="margin-top: 20px; border: 2px solid var(--accent-primary);">
                    <h3 style="color: var(--text-primary); margin-bottom: 15px;">Viewing Note #<?php echo $viewed_note['id']; ?></h3>
                    <div style="background: var(--bg-primary); padding: 15px; border-radius: 5px;">
                        <?php 
                        // VULNERABLE: Direct output of stored content
                        // In a real scenario, this might be used in eval() or include()
                        echo $viewed_note['content']; 
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

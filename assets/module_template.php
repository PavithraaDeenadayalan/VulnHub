<?php
/**
 * Module Template Helper
 * 
 * Provides common functionality for vulnerability modules
 */

function render_module_header($module_code, $module_name, $level, $level_name) {
    $level_class = strtolower($level);
    ?>
    <div class="header">
        <span class="level-badge <?php echo $level_class; ?>">LEVEL <?php 
            $levels = ['low' => 1, 'medium' => 2, 'hard' => 3, 'impossible' => 4];
            echo $levels[$level] ?? 1; 
        ?> - <?php echo strtoupper($level_name); ?></span>
        <h1><?php echo htmlspecialchars($module_name); ?></h1>
    </div>
    <?php
}

function render_level_switcher($module_code, $module_path, $current_level) {
    $levels = [
        'low' => 'LOW',
        'medium' => 'MEDIUM', 
        'hard' => 'HARD',
        'impossible' => 'IMPOSSIBLE'
    ];
    ?>
    <div class="level-switcher" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <?php foreach ($levels as $level => $level_name): ?>
            <a href="/VulnHub/modules/<?php echo $module_path; ?>/<?php echo $level; ?>.php" 
               class="btn <?php echo $current_level === $level ? 'active' : ''; ?>"
               style="padding: 8px 16px; text-decoration: none; border-radius: 5px; font-size: 14px; <?php echo $current_level === $level ? 'background: var(--accent-primary); color: white;' : 'background: var(--bg-tertiary); color: var(--text-secondary); border: 1px solid var(--border-color);'; ?>">
                Level <?php echo array_search($level, array_keys($levels)) + 1; ?> - <?php echo $level_name; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
}

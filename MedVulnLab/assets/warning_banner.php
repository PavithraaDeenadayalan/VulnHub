<?php
/**
 * Warning Banner Component
 * 
 * Displays a warning banner on every page to remind users
 * that this is an educational lab and should not be deployed.
 */

function display_warning_banner() {
    ?>
    <div style="background: #ff4444; color: white; padding: 15px; text-align: center; font-weight: bold; position: fixed; top: 0; left: 0; right: 0; z-index: 10000; box-shadow: 0 2px 10px rgba(0,0,0,0.3);">
        ⚠️ EDUCATIONAL LAB - DO NOT DEPLOY - FOR LEARNING PURPOSES ONLY ⚠️
    </div>
    <div style="height: 50px;"></div>
    <?php
}

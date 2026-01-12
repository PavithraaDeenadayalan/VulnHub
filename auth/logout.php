<?php
/**
 * Logout Handler
 */

require_once __DIR__ . '/session.php';

logout();
header('Location: /MedVulnLab/auth/login.php');
exit;

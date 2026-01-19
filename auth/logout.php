<?php
/**
 * Logout Handler
 */

require_once __DIR__ . '/session.php';

logout();
header('Location: /VulnHub/auth/login.php');
exit;

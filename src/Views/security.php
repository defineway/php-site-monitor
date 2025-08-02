<?php
/**
 * Security check to prevent direct access to view files
 * This file should be included at the top of each view template
 */

// Check if the view is being included from the main application
if (!defined('APP_RUNNING')) {
    http_response_code(403);
    die('Direct access to this file is not allowed.');
}

// Additional security: Check if user session is valid for authenticated pages
if (!isset($_COOKIE['session_id']) && !in_array($action ?? '', ['login', 'register'])) {
    header('Location: index.php?action=login');
    exit;
}
?>

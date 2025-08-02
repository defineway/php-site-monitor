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

// Additional security: Validate proper session state for authenticated pages
// Note: This is a basic check - full authentication is handled in controllers
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
$isPublicPage = in_array($currentScript, ['login.php', 'register.php']);

if (!$isPublicPage && session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic session validation for non-public pages
if (!$isPublicPage && !isset($_SESSION['user_id'])) {
    // Only redirect if we're not already on a public page
    if (!in_array($currentScript, ['login.php', 'register.php'])) {
        header('Location: /index.php?action=login');
        exit;
    }
}
?>

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

// Note: Authentication is handled at the controller level
// Views should not handle authentication redirects to avoid redirect loops
?>

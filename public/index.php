<?php
// Security: Define constant to prevent direct access to view files
define('APP_RUNNING', true);

require_once '../vendor/autoload.php';

use App\Controllers\Router;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Start session handling
session_start();

// Get the action from URL parameter
$action = $_GET['action'] ?? '';

// Initialize router and route the request
$router = new Router();
$router->route($action);

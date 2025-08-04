<?php
/**
 * PHP Site Monitor
 *
 * @author Sushovan Mukherjee
 * @copyright 2025 Defineway Technologies Private Limited
 * @link https://defineway.com
 * @contact sushovan@defineway.com
 *
 * Licensed under the MIT License with Attribution Clause.
 * You must retain visible credit to the company ("Powered by Defineway Technologies Private Limited")
 * in the user interface and documentation of any derivative works or public deployments.
 */

// Set the application running flag before any includes
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

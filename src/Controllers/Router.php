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
namespace App\Controllers;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\SiteController;
use App\Controllers\UserController;

class Router {
    
    private $routes = [];
    
    public function __construct() {
        $this->defineRoutes();
    }
    
    /**
     * Define all application routes
     */
    private function defineRoutes(): void {
        // Authentication routes
        $this->routes['login'] = [AuthController::class, 'login'];
        $this->routes['register'] = [AuthController::class, 'register'];
        $this->routes['logout'] = [AuthController::class, 'logout'];
        $this->routes['change_password'] = [AuthController::class, 'changePassword'];
        
        // Dashboard routes
        $this->routes['dashboard'] = [DashboardController::class, 'index'];
        $this->routes[''] = [DashboardController::class, 'index']; // Default route
        
        // Site management routes
        $this->routes['add_site'] = [SiteController::class, 'add'];
        $this->routes['edit_site'] = [SiteController::class, 'edit'];
        $this->routes['site_details'] = [SiteController::class, 'details'];
        $this->routes['delete_site'] = [SiteController::class, 'delete'];
        
        // User management routes
        $this->routes['profile'] = [UserController::class, 'profile'];
        $this->routes['users'] = [UserController::class, 'list'];
        $this->routes['add_user'] = [UserController::class, 'add'];
        $this->routes['edit_user'] = [UserController::class, 'edit'];
        $this->routes['delete_user'] = [UserController::class, 'delete'];
        $this->routes['activate_user'] = [UserController::class, 'activate'];
        $this->routes['deactivate_user'] = [UserController::class, 'deactivate'];

        // PhpInfo route
        $this->routes['phpinfo'] = [PhpInfoController::class, 'index'];
    }
    
    /**
     * Route the request to the appropriate controller and method
     */
    public function route(string $action = ''): void {
        // Get the route configuration
        if (!isset($this->routes[$action])) {
            $this->handleNotFound();
            return;
        }
        
        [$controllerClass, $method] = $this->routes[$action];
        
        // Instantiate the controller and call the method
        try {
            $controller = new $controllerClass();
            $controller->$method();
        } catch (\Exception $e) {
            $this->handleError($e);
        }
    }
    
    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void {
        http_response_code(404);
        echo '404 Not Found';
    }
    
    /**
     * Handle application errors
     */
    private function handleError(\Exception $e): void {
        // Log detailed error information for debugging
        $errorDetails = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
            'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        error_log('Application Error: ' . json_encode($errorDetails));
        
        // In development, you might want to show the actual error
        // In production, always show generic error message
        $isDevelopment = ($_ENV['APP_ENV'] ?? 'production') === 'development';
        
        http_response_code(500);
        
        if ($isDevelopment) {
            echo "<h1>500 Internal Server Error</h1>";
            echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            echo '500 Internal Server Error';
        }
    }
}

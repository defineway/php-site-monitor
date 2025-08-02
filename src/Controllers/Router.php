<?php
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
        // Log the error (you might want to implement proper logging)
        error_log('Application Error: ' . $e->getMessage());
        
        // Show generic error page in production
        http_response_code(500);
        echo '500 Internal Server Error';
    }
}

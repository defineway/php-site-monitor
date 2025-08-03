<?php
namespace App\Controllers;

use App\Services\AuthService;

abstract class BaseController {
    protected $authService;
    protected $currentUser;
    
    public function __construct() {
        $this->authService = new AuthService();
    }
    
    /**
     * Render a view with data
     */
    protected function render(string $view, array $data = []): void {
        // Validate view name to prevent directory traversal
        if (preg_match('/[^a-zA-Z0-9_\-]/', $view)) {
            throw new \Exception("Invalid view name: {$view}");
        }
        
        // Extract data to variables
        extract($data);
        
        // Construct and validate the view file path
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        $realViewPath = realpath($viewPath);
        $viewsDirectory = realpath(__DIR__ . '/../Views');
        
        // Ensure the view file exists and is within the Views directory
        if (!$realViewPath || !$viewsDirectory || strpos($realViewPath, $viewsDirectory) !== 0) {
            throw new \Exception("View not found or path traversal detected: {$view}");
        }
        
        // Use require instead of include to ensure critical views are loaded
        require $realViewPath;
    }
    
    /**
     * Redirect to a URL
     */
    protected function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Redirect with success message
     */
    protected function redirectWithSuccess(string $url, string $message): void {
        $this->redirect($url . (strpos($url, '?') !== false ? '&' : '?') . 'success=' . urlencode($message));
    }
    
    /**
     * Redirect with error message
     */
    protected function redirectWithError(string $url, string $message): void {
        $this->redirect($url . (strpos($url, '?') !== false ? '&' : '?') . 'error=' . urlencode($message));
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth(): array {
        $this->currentUser = $this->authService->requireAuth();
        return $this->currentUser;
    }
    
    /**
     * Require admin access
     */
    protected function requireAdmin(): array {
        $this->currentUser = $this->authService->requireAdmin();
        return $this->currentUser;
    }
    
    /**
     * Get current user (if logged in)
     */
    protected function getCurrentUser(): ?array {
        $this->currentUser = $this->authService->getCurrentUser();
        return $this->currentUser;
    }
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn(): bool {
        return $this->authService->isLoggedIn();
    }
}

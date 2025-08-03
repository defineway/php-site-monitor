<?php
namespace App\Services;

use App\Models\User;
use App\Models\Session;

class AuthService {
    private $userModel;
    private $sessionModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->sessionModel = new Session();
    }

    /**
     * Change password for a user (self-service)
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): array {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        if (!$this->userModel->verifyPassword($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'New password must be at least 8 characters'];
        }
        try {
            $this->userModel->update($userId, ['password' => $newPassword]);
            return ['success' => true, 'message' => 'Password changed successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to change password: ' . $e->getMessage()];
        }
    }
    
    public function login(string $username, string $password): array {
        $user = $this->userModel->findByUsername($username);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        if (!$this->userModel->verifyPassword($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Create session
        $sessionId = $this->sessionModel->create(
            $user['id'],
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        );
        
        // Set session cookie
        $this->setSessionCookie($sessionId);
        
        // Also set PHP session for compatibility
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        // Update last login
        $this->userModel->updateLastLogin($user['id']);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];
    }
    
    public function logout(): bool {
        $sessionId = $_COOKIE['session_id'] ?? null;
        
        if ($sessionId) {
            $this->sessionModel->delete($sessionId);
        }
        
        // Clear PHP session
        $_SESSION = [];
        
        // Clear session cookie
        setcookie('session_id', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        return true;
    }
    
    public function getCurrentUser(): ?array {
        $sessionId = $_COOKIE['session_id'] ?? null;
        
        if (!$sessionId) {
            return null;
        }
        
        $session = $this->sessionModel->findBySessionId($sessionId);
        
        if (!$session) {
            // Invalid or expired session
            $this->logout();
            return null;
        }
        
        // Extend session
        $this->sessionModel->extend($sessionId);
        
        return [
            'id' => $session['user_id'],
            'username' => $session['username'],
            'email' => $session['email'],
            'role' => $session['role']
        ];
    }
    
    public function isLoggedIn(): bool {
        return $this->getCurrentUser() !== null;
    }
    
    public function requireAuth(): ?array {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            header('Location: ?action=login');
            exit;
        }
        
        return $user;
    }
    
    public function requireAdmin(): ?array {
        $user = $this->requireAuth();
        
        if ($user['role'] !== 'admin') {
            header('Location: ?action=dashboard&error=access_denied');
            exit;
        }
        
        return $user;
    }
    
    public function register(array $data): array {
        // Check if username exists
        if ($this->userModel->findByUsername($data['username'])) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Check if email exists
        if ($this->userModel->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Validate password strength
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        try {
            $userId = $this->userModel->create($data);
            return [
                'success' => true,
                'message' => 'Registration successful',
                'user_id' => $userId
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    private function setSessionCookie(string $sessionId): void {
        setcookie('session_id', $sessionId, [
            'expires' => time() + (30 * 24 * 60 * 60), // 30 days
            'path' => '/',
            'domain' => '',
            'secure' => false, // Set to true in production with HTTPS
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    public function cleanupSessions(): int {
        return $this->sessionModel->cleanup();
    }
}

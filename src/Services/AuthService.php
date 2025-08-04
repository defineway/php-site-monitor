<?php
namespace App\Services;

use App\Models\User;
use App\Services\UserService;
use App\Models\Session;

class AuthService {

    private UserService $userService;
    private Session $sessionModel;

    public function __construct() {
        $this->userService = new UserService();
        $this->sessionModel = new Session();
    }

    /**
     * Change password for a user (self-service)
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): array {
        $user = $this->userService->findById($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }
        if (!$this->userService->verifyPassword($currentPassword, $user->getPasswordHash())) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'New password must be at least 8 characters'];
        }
        try {
            $this->userService->update($userId, ['password' => $newPassword]);
            return ['success' => true, 'message' => 'Password changed successfully'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Failed to change password: ' . $e->getMessage()];
        }
    }
    
    public function login(string $username, string $password): array {
        $user = $this->userService->findByUsername($username);

        if (!is_object($user)) {
            error_log("Login attempt failed: User not found for username - {$username}");
            return ['success' => false, 'message' => 'Invalid username or password'];
        }

        $verificationResult = $this->userService->verifyPassword($password, $user->getPasswordHash());

        if (!$verificationResult) {
            return ['success' => false, 'message' => 'Invalid username or password'];
        }
        
        // Create session
        $sessionId = $this->sessionModel->create(
            $user->getId(),
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        );
        
        // Set session cookie
        $this->setSessionCookie($sessionId);
        
        // Also set PHP session for compatibility
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];
        
        // Update last login
        $this->userService->updateLastLogin($user->getId());
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'role' => $user->getRole()
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
    
    public function getCurrentUser(): ?User {
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
        
        return new User( [
            'id' => $session['user_id'],
            'username' => $session['username'],
            'email' => $session['email'],
            'role' => $session['role'],
            'is_active' => $session['is_active'] ?? true
        ] );
    }
    
    public function isLoggedIn(): bool {
        return $this->getCurrentUser() !== null;
    }
    
    public function requireAuth(): ?User {
        $user = $this->getCurrentUser();
        
        if (!$user || !$user->isActive()) {
            header('Location: ?action=login');
            exit;
        }
        
        return $user;
    }
    
    public function requireAdmin(): ?User {
        $user = $this->requireAuth();
        
        if ($user->getRole() !== 'admin') {
            header('Location: ?action=dashboard&error=access_denied');
            exit;
        }
        
        return $user;
    }
    
    public function register(array $data): array {
        // Check if username exists
        if ($this->userService->findByUsername($data['username'])) {
            return ['success' => false, 'message' => 'Username already exists'];
        }
        
        // Check if email exists
        if ($this->userService->findByEmail($data['email'])) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Validate password strength
        if (strlen($data['password']) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }
        
        try {
            $userId = $this->userService->create($data);
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

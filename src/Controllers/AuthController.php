<?php
namespace App\Controllers;

use App\Models\User;
use Exception;

class AuthController extends BaseController {
    
    /**
     * Show login form
     */
    public function login(): void {
        if ($this->isLoggedIn()) {
            $this->redirect('?action=dashboard');
        }
        
        $error = null;
        $username = '';
        
        if ($_POST) {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $result = $this->authService->login($username, $password);
            if ($result['success']) {
                $this->redirect('?action=dashboard');
            } else {
                $error = $result['message'];
            }
        }
        
        $this->render('login', [
            'error' => $error,
            'username' => $username
        ]);
    }
    
    /**
     * Show registration form
     */
    public function register(): void {
        if ($this->isLoggedIn()) {
            $this->redirect('?action=dashboard');
        }
        
        $error = null;
        $formData = [];
        
        if ($_POST) {
            $formData = $_POST;
            
            try {
                $userModel = new User();
                $userModel->create([
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => $_POST['password'],
                    'role' => 'user'
                ]);
                
                $this->redirectWithSuccess('index.php?action=login', 'registration_success');
            } catch (Exception $e) {
                $error = 'Registration failed: ' . $e->getMessage();
            }
        }
        
        $this->render('register', [
            'error' => $error,
            'formData' => $formData
        ]);
    }
    
    /**
     * Logout user
     */
    public function logout(): void {
        $this->authService->logout();
        $this->redirectWithSuccess('index.php?action=login', 'logout_success');
    }
    
    /**
     * Show change password form
     */
    public function changePassword(): void {
        $this->requireAuth();
        
        $error = null;
        
        if ($_POST) {
            if (empty($_POST['current_password']) || empty($_POST['new_password'])) {
                $error = 'Current and new password required.';
            } else {
                $result = $this->authService->changePassword(
                    $this->currentUser->getId(), 
                    $_POST['current_password'], 
                    $_POST['new_password']
                );
                
                if ($result['success']) {
                    $this->redirectWithSuccess('index.php?action=profile', 'password_changed');
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        $this->render('change_password', [
            'error' => $error,
            'currentUser' => $this->currentUser
        ]);
    }
}

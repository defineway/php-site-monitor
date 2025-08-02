<?php
namespace App\Controllers;

use App\Models\User;
use Exception;

class UserController extends BaseController {
    
    /**
     * Show user profile
     */
    public function profile(): void {
        $this->requireAuth();
        
        $userModel = new User();
        $user = $userModel->findById($this->currentUser['id']);
        
        if (!$user) {
            $error = 'User not found.';
        }
        
        $error = null;
        
        if ($_POST) {
            $updateData = [];
            if (!empty($_POST['email'])) {
                $updateData['email'] = $_POST['email'];
            }
            if (!empty($_POST['password'])) {
                $updateData['password'] = $_POST['password'];
            }
            
            if (!empty($updateData)) {
                try {
                    $userModel->update($this->currentUser['id'], $updateData);
                    $this->redirectWithSuccess('index.php?action=profile', 'profile_updated');
                } catch (Exception $e) {
                    $error = 'Failed to update profile: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('profile', [
            'currentUser' => $this->currentUser,
            'user' => $user ?? null,
            'error' => $error
        ]);
    }
    
    /**
     * List all users (admin only)
     */
    public function list(): void {
        $this->requireAdmin();
        
        $userModel = new User();
        $users = $userModel->findAll();
        
        $this->render('users', [
            'currentUser' => $this->currentUser,
            'users' => $users
        ]);
    }
    
    /**
     * Show add user form (admin only)
     */
    public function add(): void {
        $this->requireAdmin();
        
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
                    'role' => $_POST['role'] ?? 'user'
                ]);
                
                $this->redirectWithSuccess('index.php?action=users', 'user_added');
            } catch (Exception $e) {
                $error = 'Failed to add user: ' . $e->getMessage();
            }
        }
        
        $this->render('add_user', [
            'currentUser' => $this->currentUser,
            'error' => $error,
            'formData' => $formData
        ]);
    }
    
    /**
     * Show edit user form (admin only)
     */
    public function edit(): void {
        $this->requireAdmin();
        
        $userId = (int)($_GET['id'] ?? 0);
        $userModel = new User();
        $user = $userModel->findById($userId);
        
        if (!$user) {
            $this->redirectWithError('index.php?action=users', 'user_not_found');
        }
        
        $error = null;
        
        if ($_POST) {
            $updateData = [];
            if (!empty($_POST['username'])) {
                $updateData['username'] = $_POST['username'];
            }
            if (!empty($_POST['email'])) {
                $updateData['email'] = $_POST['email'];
            }
            if (!empty($_POST['role'])) {
                $updateData['role'] = $_POST['role'];
            }
            if (isset($_POST['status'])) {
                $updateData['status'] = $_POST['status'];
            }
            if (!empty($_POST['password'])) {
                $updateData['password'] = $_POST['password'];
            }
            
            if (!empty($updateData)) {
                try {
                    $userModel->update($userId, $updateData);
                    $this->redirectWithSuccess('index.php?action=users', 'user_updated');
                } catch (Exception $e) {
                    $error = 'Failed to update user: ' . $e->getMessage();
                }
            }
        }
        
        $this->render('edit_user', [
            'currentUser' => $this->currentUser,
            'user' => $user,
            'error' => $error
        ]);
    }
    
    /**
     * Delete user (admin only)
     */
    public function delete(): void {
        $this->requireAdmin();
        
        $userId = (int)($_GET['id'] ?? 0);
        $hardDelete = isset($_GET['hard']) && $_GET['hard'] === 'true';
        
        if ($userId === $this->currentUser['id']) {
            $this->redirectWithError('index.php?action=users', 'cannot_delete_self');
        }
        
        try {
            $userModel = new User();
            
            if ($hardDelete) {
                $userModel->hardDelete($userId);
                $this->redirectWithSuccess('index.php?action=users', 'user_permanently_deleted');
            } else {
                $userModel->delete($userId);
                $this->redirectWithSuccess('index.php?action=users', 'user_deactivated');
            }
        } catch (Exception $e) {
            $this->redirectWithError('index.php?action=users', 'delete_failed');
        }
    }
    
    /**
     * Activate user (admin only)
     */
    public function activate(): void {
        $this->requireAdmin();
        
        $userId = (int)($_GET['id'] ?? 0);
        
        if ($userId === $this->currentUser['id']) {
            $this->redirectWithError('index.php?action=users', 'cannot_modify_self');
        }
        
        try {
            $userModel = new User();
            $userModel->update($userId, ['status' => 'active']);
            $this->redirectWithSuccess('index.php?action=users', 'user_activated');
        } catch (Exception $e) {
            $this->redirectWithError('index.php?action=users', 'activation_failed');
        }
    }
    
    /**
     * Deactivate user (admin only)
     */
    public function deactivate(): void {
        $this->requireAdmin();
        
        $userId = (int)($_GET['id'] ?? 0);
        
        if ($userId === $this->currentUser['id']) {
            $this->redirectWithError('index.php?action=users', 'cannot_modify_self');
        }
        
        try {
            $userModel = new User();
            $userModel->update($userId, ['status' => 'inactive']);
            $this->redirectWithSuccess('index.php?action=users', 'user_deactivated');
        } catch (Exception $e) {
            $this->redirectWithError('index.php?action=users', 'deactivation_failed');
        }
    }
}

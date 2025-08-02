<?php
require_once '../vendor/autoload.php';

use App\Models\Site;
use App\Models\MonitoringResult;
use App\Models\User;
use App\Services\AuthService;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Start session handling
session_start();

$siteModel = new Site();
$resultModel = new MonitoringResult();
$userModel = new User();
$authService = new AuthService();

$action = $_GET['action'] ?? 'dashboard';
$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

// Public actions that don't require authentication
$publicActions = ['login', 'register'];

// Check if user is logged in for protected routes
if (!in_array($action, $publicActions)) {
    $currentUser = $authService->requireAuth();
}

switch ($action) {
    case 'edit_user':
        // Admin only
        $authService->requireAdmin();
        $userId = (int)($_GET['id'] ?? 0);
        $user = $userModel->findById($userId);
        if (!$user) {
            header('Location: index.php?action=users&error=user_not_found');
            exit;
        }
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
                    header('Location: index.php?action=users&success=user_updated');
                    exit;
                } catch (Exception $e) {
                    $error = 'Failed to update user: ' . $e->getMessage();
                }
            }
        }
        include 'views/edit_user.php';
        break;

    case 'delete_user':
        // Admin only
        $authService->requireAdmin();
        $userId = (int)($_GET['id'] ?? 0);
        if ($userId === $currentUser['id']) {
            header('Location: index.php?action=users&error=cannot_delete_self');
            exit;
        }
        try {
            $userModel->hardDelete($userId);
            header('Location: index.php?action=users&success=user_deleted');
            exit;
        } catch (Exception $e) {
            header('Location: index.php?action=users&error=delete_failed');
            exit;
        }

    case 'activate_user':
        // Admin only
        $authService->requireAdmin();
        $userId = (int)($_GET['id'] ?? 0);
        if ($userId === $currentUser['id']) {
            header('Location: index.php?action=users&error=cannot_modify_self');
            exit;
        }
        try {
            $userModel->update($userId, ['status' => 'active']);
            header('Location: index.php?action=users&success=user_activated');
            exit;
        } catch (Exception $e) {
            header('Location: index.php?action=users&error=activation_failed');
            exit;
        }

    case 'deactivate_user':
        // Admin only
        $authService->requireAdmin();
        $userId = (int)($_GET['id'] ?? 0);
        if ($userId === $currentUser['id']) {
            header('Location: index.php?action=users&error=cannot_modify_self');
            exit;
        }
        try {
            $userModel->update($userId, ['status' => 'inactive']);
            header('Location: index.php?action=users&success=user_deactivated');
            exit;
        } catch (Exception $e) {
            header('Location: index.php?action=users&error=deactivation_failed');
            exit;
        }

    case 'change_password':
        // Self-service password change
        if ($_POST) {
            if (empty($_POST['current_password']) || empty($_POST['new_password'])) {
                $error = 'Current and new password required.';
            } else {
                $result = $authService->changePassword($currentUser['id'], $_POST['current_password'], $_POST['new_password']);
                if ($result['success']) {
                    header('Location: index.php?action=profile&success=password_changed');
                    exit;
                } else {
                    $error = $result['message'];
                }
            }
        }
        include 'views/change_password.php';
        break;
    case 'login':
        if ($_POST) {
            $result = $authService->login($_POST['username'], $_POST['password']);
            if ($result['success']) {
                header('Location: index.php?success=login');
                exit;
            } else {
                $error = $result['message'];
            }
        }
        include 'views/login.php';
        break;
    
    case 'register':
        if ($_POST) {
            $result = $authService->register($_POST);
            if ($result['success']) {
                header('Location: index.php?action=login&success=registration');
                exit;
            } else {
                $error = $result['message'];
            }
        }
        include 'views/register.php';
        break;
    
    case 'logout':
        $authService->logout();
        header('Location: index.php?action=login&success=logout');
        exit;
    
    case 'dashboard':
        try {
            $sites = $siteModel->findAll();
            $latestResults = [];
            foreach ($sites as $site) {
                $latestResults[$site['id']] = $resultModel->getLatestStatus($site['id']);
            }
            include 'views/dashboard.php';
        } catch (Exception $e) {
            $error = "Database connection error. Please ensure the database is running and configured correctly.";
            include 'views/dashboard.php';
        }
        break;
    
    case 'add_site':
        if ($_POST) {
            try {
                $siteModel->create($_POST);
                header('Location: index.php?success=site_added');
                exit;
            } catch (Exception $e) {
                $error = 'Failed to add site: ' . $e->getMessage();
            }
        }
        include 'views/add_site.php';
        break;
    
    case 'edit_site':
        $siteId = (int)$_GET['id'];
        $site = $siteModel->findById($siteId);
        
        if (!$site) {
            header('Location: index.php?error=site_not_found');
            exit;
        }
        
        if ($_POST) {
            try {
                $siteModel->update($siteId, $_POST);
                header('Location: index.php?success=site_updated');
                exit;
            } catch (Exception $e) {
                $error = 'Failed to update site: ' . $e->getMessage();
            }
        }
        include 'views/edit_site.php';
        break;
    
    case 'site_details':
        $siteId = (int)$_GET['id'];
        $site = $siteModel->findById($siteId);
        $results = $resultModel->findBySiteId($siteId);
        include 'views/site_details.php';
        break;
    
    case 'delete_site':
        $siteId = (int)$_GET['id'];
        try {
            $siteModel->delete($siteId);
            header('Location: index.php?success=site_deleted');
            exit;
        } catch (Exception $e) {
            header('Location: index.php?error=delete_failed');
            exit;
        }
    
    case 'users':
        // Admin only
        $authService->requireAdmin();
        $users = $userModel->findAll();
        include 'views/users.php';
        break;
    
    case 'add_user':
        // Admin only
        $authService->requireAdmin();
        if ($_POST) {
            $result = $authService->register($_POST);
            if ($result['success']) {
                header('Location: index.php?action=users&success=user_added');
                exit;
            } else {
                $error = $result['message'];
            }
        }
        include 'views/add_user.php';
        break;
    
    case 'profile':
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
                    $userModel->update($currentUser['id'], $updateData);
                    header('Location: index.php?action=profile&success=profile_updated');
                    exit;
                } catch (Exception $e) {
                    $error = 'Failed to update profile: ' . $e->getMessage();
                }
            }
        }
        include 'views/profile.php';
        break;
    
    default:
        http_response_code(404);
        echo '404 Not Found';
}

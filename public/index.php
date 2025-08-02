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

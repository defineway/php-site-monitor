<?php
namespace App\Controllers;

use App\Models\Site;
use App\Models\MonitoringResult;
use Exception;

class DashboardController extends BaseController {
    
    /**
     * Show dashboard
     */
    public function index(): void {
        $this->requireAuth();
        
        $error = null;
        $sites = [];
        $latestResults = [];
        
        try {
            $siteModel = new Site();
            $resultModel = new MonitoringResult();
            
            $sites = $siteModel->findAll();
            
            foreach ($sites as $site) {
                $latestResults[$site['id']] = $resultModel->getLatestStatus($site['id']);
            }
        } catch (Exception $e) {
            $error = "Database connection error. Please ensure the database is running and configured correctly.";
        }
        
        $this->render('dashboard', [
            'currentUser' => $this->currentUser,
            'sites' => $sites,
            'latestResults' => $latestResults,
            'error' => $error
        ]);
    }
}

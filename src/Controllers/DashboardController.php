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

use App\Models\Site;
use App\Services\SiteService;
use App\Services\MonitoringResultService;
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
            $siteService = new SiteService();
            $resultService = new MonitoringResultService();

            $sites = $siteService->findAllByUser($this->currentUser);
            
            foreach ($sites as $site) {
                $latestResults[$site->getId()] = $resultService->getLatestStatus($site->getId());
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

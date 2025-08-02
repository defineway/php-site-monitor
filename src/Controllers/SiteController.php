<?php
namespace App\Controllers;

use App\Models\Site;
use App\Models\MonitoringResult;
use Exception;

class SiteController extends BaseController {
    
    /**
     * Show add site form
     */
    public function add(): void {
        $this->requireAuth();
        
        $error = null;
        $formData = [];
        
        if ($_POST) {
            $formData = $_POST;
            
            try {
                $siteModel = new Site();
                $siteModel->create($_POST);
                $this->redirectWithSuccess('index.php', 'site_added');
            } catch (Exception $e) {
                $error = 'Failed to add site: ' . $e->getMessage();
            }
        }
        
        $this->render('add_site', [
            'currentUser' => $this->currentUser,
            'error' => $error,
            'formData' => $formData
        ]);
    }
    
    /**
     * Show edit site form
     */
    public function edit(): void {
        $this->requireAuth();
        
        $siteId = (int)($_GET['id'] ?? 0);
        $siteModel = new Site();
        $site = $siteModel->findById($siteId);
        
        if (!$site) {
            $this->redirectWithError('index.php', 'site_not_found');
        }
        
        $error = null;
        
        if ($_POST) {
            try {
                $siteModel->update($siteId, $_POST);
                $this->redirectWithSuccess('index.php', 'site_updated');
            } catch (Exception $e) {
                $error = 'Failed to update site: ' . $e->getMessage();
            }
        }
        
        $this->render('edit_site', [
            'currentUser' => $this->currentUser,
            'site' => $site,
            'error' => $error
        ]);
    }
    
    /**
     * Show site details
     */
    public function details(): void {
        $this->requireAuth();
        
        $siteId = (int)($_GET['id'] ?? 0);
        $siteModel = new Site();
        $resultModel = new MonitoringResult();
        
        $site = $siteModel->findById($siteId);
        if (!$site) {
            $this->redirectWithError('index.php', 'site_not_found');
        }
        
        $results = $resultModel->findBySiteId($siteId, 50); // Last 50 results
        
        $this->render('site_details', [
            'currentUser' => $this->currentUser,
            'site' => $site,
            'results' => $results
        ]);
    }
    
    /**
     * Delete site
     */
    public function delete(): void {
        $this->requireAuth();
        
        $siteId = (int)($_GET['id'] ?? 0);
        
        try {
            $siteModel = new Site();
            $siteModel->delete($siteId);
            $this->redirectWithSuccess('index.php', 'site_deleted');
        } catch (Exception $e) {
            $this->redirectWithError('index.php', 'delete_failed');
        }
    }
}

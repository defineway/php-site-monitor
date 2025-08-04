<?php

namespace App\Controllers;

class PhpInfoController extends BaseController {
	
	/**
	 * Show PHP info
	 */
	public function index(): void {
		$this->requireAuth();
		
		// Check if the user has permission to view PHP info
		if (!$this->currentUser->isAdmin()) {
			$this->redirectWithError('index.php', 'access_denied');
			return;
		}

		// Output PHP info
		phpinfo(); // This will output the PHP info directly to the browser
	}
}
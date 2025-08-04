<?php
// At the top of dashboard.php - security check
require_once __DIR__ . '/security.php';

// Set current page for navigation highlighting
$currentPage = 'dashboard';
// $currentUser is provided by the controller
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Site Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="container mt-4">
        <!-- Alerts -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                switch ($_GET['success']) {
                    case 'login': echo 'Welcome back! You have been logged in successfully.'; break;
                    case 'site_added': echo 'Site has been added successfully.'; break;
                    case 'site_updated': echo 'Site has been updated successfully.'; break;
                    case 'site_deleted': echo 'Site has been deleted successfully.'; break;
                    default: echo 'Operation completed successfully.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="mb-1"><i class="fas fa-chart-line text-primary me-2"></i>Dashboard</h1>
                        <p class="text-muted mb-0">Monitor your websites and SSL certificates</p>
                    </div>
                    <a href="?action=add_site" class="btn btn-primary btn-lg shadow-sm">
                        <i class="fas fa-plus me-2"></i>Add New Site
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($sites)): 
            // Calculate statistics
            $totalSites = count($sites);
            $upSites = 0;
            $downSites = 0;
            $sslEnabledSites = 0;
            $avgResponseTime = 0;
            $totalResponseTime = 0;
            $responseTimeCount = 0;

            foreach ($sites as $site) {
                if ($site->isSslCheckEnabled()) {
                    $sslEnabledSites++;
                }
                
                $latest = $latestResults[$site->getId()] ?? null;
                if ($latest) {
                    if ($latest->getStatus() === 'up') {
                        $upSites++;
                    } elseif ($latest->getStatus() === 'down') {
                        $downSites++;
                    }
                    
                    if ($latest->getResponseTime()) {
                        $totalResponseTime += $latest->getResponseTime();
                        $responseTimeCount++;
                    }
                }
            }
            
            if ($responseTimeCount > 0) {
                $avgResponseTime = round($totalResponseTime / $responseTimeCount);
            }
            
            $uptimePercentage = $totalSites > 0 ? round(($upSites / $totalSites) * 100) : 0;
        ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="card-subtitle text-muted mb-2">Total Sites</h6>
                                <h2 class="mb-0 text-primary"><?= $totalSites ?></h2>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-globe text-primary fa-lg"></i>
                            </div>
                        </div>
                        <small class="text-muted">Being monitored</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="card-subtitle text-muted mb-2">Uptime</h6>
                                <h2 class="mb-0 text-success"><?= $uptimePercentage ?>%</h2>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle text-success fa-lg"></i>
                            </div>
                        </div>
                        <small class="text-muted"><?= $upSites ?> of <?= $totalSites ?> sites up</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="card-subtitle text-muted mb-2">Avg Response</h6>
                                <h2 class="mb-0 <?= $avgResponseTime > 2000 ? 'text-danger' : ($avgResponseTime > 1000 ? 'text-warning' : 'text-success') ?>">
                                    <?= $avgResponseTime ?>ms
                                </h2>
                            </div>
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-stopwatch text-info fa-lg"></i>
                            </div>
                        </div>
                        <small class="text-muted">Average response time</small>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <h6 class="card-subtitle text-muted mb-2">SSL Monitoring</h6>
                                <h2 class="mb-0 text-warning"><?= $sslEnabledSites ?></h2>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-shield-alt text-warning fa-lg"></i>
                            </div>
                        </div>
                        <small class="text-muted">Sites with SSL checks</small>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                
                <?php if (empty($sites)): ?>
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-globe fa-4x text-muted mb-3"></i>
                        </div>
                        <h3 class="text-muted mb-3">No Sites Being Monitored</h3>
                        <p class="text-muted mb-4">Start monitoring your websites by adding your first site.</p>
                        <a href="?action=add_site" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Add Your First Site
                        </a>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-list-alt text-primary me-2"></i>
                                    Your Monitored Sites
                                </h5>
                                <small class="text-muted"><?= count($sites) ?> sites total</small>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 py-3"><i class="fas fa-tag me-1"></i>Name</th>
                                        <th class="border-0 py-3"><i class="fas fa-link me-1"></i>URL</th>
                                        <th class="border-0 py-3"><i class="fas fa-heartbeat me-1"></i>Status</th>
                                        <th class="border-0 py-3"><i class="fas fa-stopwatch me-1"></i>Response</th>
                                        <th class="border-0 py-3"><i class="fas fa-clock me-1"></i>Last Check</th>
                                        <th class="border-0 py-3"><i class="fas fa-cogs me-1"></i>Actions</th>
                                        <?php if ($currentUser->isAdmin()): ?>
                                            <th class="border-0 py-3"><i class="fas fa-user me-1"></i>Owner</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sites as $site): 
                                        $latest = $latestResults[$site->getId()] ?? null;
                                        $statusClass = $latest ? 'status-' . $latest->getStatus() : 'text-muted';
                                        $statusIcon = '';
                                        $statusBadge = 'secondary';
                                        
                                        if($latest) {
                                            // Determine status icon and badge based on latest result
                                            switch ($latest->getStatus() ?? 'unknown') {
                                                case 'up': 
                                                    $statusIcon = 'fas fa-check-circle'; 
                                                    $statusBadge = 'success';
                                                    break;
                                                case 'down': 
                                                    $statusIcon = 'fas fa-times-circle'; 
                                                    $statusBadge = 'danger';
                                                    break;
                                                case 'warning': 
                                                    $statusIcon = 'fas fa-exclamation-triangle'; 
                                                    $statusBadge = 'warning';
                                                    break;
                                                default: 
                                                    $statusIcon = 'fas fa-question-circle'; 
                                                    $statusBadge = 'secondary';
                                            }
                                        }
                                        
                                        $responseTime = $latest ? $latest->getResponseTime() : null;
                                        $responseClass = '';
                                        if ($responseTime) {
                                            if ($responseTime > 2000) {
                                                $responseClass = 'text-danger';
                                            } elseif ($responseTime > 1000) {
                                                $responseClass = 'text-warning';
                                            } else {
                                                $responseClass = 'text-success';
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td class="py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-globe text-primary"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($site->getName()) ?></div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            Check every <?= $site->getCheckInterval() ?> min
                                                            <?php if ($site->isSslCheckEnabled()): ?>
                                                                <span class="badge bg-warning bg-opacity-25 text-warning ms-1">
                                                                    <i class="fas fa-shield-alt me-1"></i>SSL
                                                                </span>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                <a href="<?= htmlspecialchars($site->getUrl()) ?>" target="_blank" class="text-decoration-none">
                                                    <?= htmlspecialchars($site->getUrl()) ?>
                                                    <i class="fas fa-external-link-alt ms-1 small text-muted"></i>
                                                </a>
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-<?= $statusBadge ?> px-3 py-2">
                                                    <i class="<?= $statusIcon ?> me-1"></i>
                                                    <?= $latest ? ucfirst($latest->getStatus()) : 'Unknown' ?>
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <?php if ($latest && $latest->getResponseTime()): ?>
                                                    <span class="fw-bold <?= $responseClass ?>">
                                                        <?= $latest->getResponseTime() ?>ms
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3">
                                                <?php if ($latest): ?>
                                                    <div>
                                                        <div class="fw-bold"><?= date('M j, H:i', strtotime($latest->getCheckedAt())) ?></div>
                                                        <small class="text-muted"><?= date('Y', strtotime($latest->getCheckedAt())) ?></small>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Never</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3">
                                                <div class="btn-group" role="group">
                                                    <a href="?action=site_details&id=<?= $site->getId() ?>" 
                                                       class="btn btn-outline-info btn-sm" 
                                                       data-bs-toggle="tooltip" title="View Details">
                                                        <i class="fas fa-chart-line"></i>
                                                    </a>
                                                    <a href="?action=edit_site&id=<?= $site->getId() ?>" 
                                                       class="btn btn-outline-primary btn-sm"
                                                       data-bs-toggle="tooltip" title="Edit Site">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?action=delete_site&id=<?= $site->getId() ?>" 
                                                       class="btn btn-outline-danger btn-sm" 
                                                       onclick="return confirm('Are you sure you want to delete this site?')"
                                                       data-bs-toggle="tooltip" title="Delete Site">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <?php if ($currentUser->isAdmin()): ?>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                                <i class="fas fa-user text-secondary small"></i>
                                                            </div>
                                                        </div>
                                                        <span><?= htmlspecialchars($site->getUser()->getUsername() ?? 'N/A') ?></span>
                                                    </div>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-refresh functionality (optional)
        let refreshInterval = 60000; // 60 seconds
        let countdown = refreshInterval / 1000;
        
        function updateCountdown() {
            countdown--;
            if (countdown <= 0) {
                location.reload();
            }
        }
        
        setInterval(updateCountdown, 1000);
    </script>
</body>
</html>

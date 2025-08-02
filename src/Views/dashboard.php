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

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-dashboard"></i> Dashboard</h1>
                    <a href="?action=add_site" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Site
                    </a>
                </div>
                
                <?php if (empty($sites)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        No sites are currently being monitored. <a href="?action=add_site">Add your first site</a>.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($sites as $site): 
                            $latest = $latestResults[$site['id']] ?? null;
                            $statusClass = $latest ? 'status-' . $latest['status'] : 'text-muted';
                            $statusIcon = '';
                            switch ($latest['status'] ?? 'unknown') {
                                case 'up': $statusIcon = 'fas fa-check-circle'; break;
                                case 'down': $statusIcon = 'fas fa-times-circle'; break;
                                case 'warning': $statusIcon = 'fas fa-exclamation-triangle'; break;
                                default: $statusIcon = 'fas fa-question-circle';
                            }
                        ?>
                            <div class="col-md-4 col-lg-3 mb-4">
                                <div class="card site-card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($site['name']) ?></h5>
                                        <p class="card-text">
                                            <small class="text-muted"><?= htmlspecialchars($site['url']) ?></small>
                                        </p>
                                        <p class="card-text">
                                            <strong>Status:</strong> 
                                            <span class="<?= $statusClass ?>">
                                                <i class="<?= $statusIcon ?>"></i>
                                                <?= $latest ? ucfirst($latest['status']) : 'Unknown' ?>
                                            </span>
                                        </p>
                                        <?php if ($latest && $latest['response_time']): ?>
                                            <p class="card-text">
                                                <strong>Response:</strong> <?= $latest['response_time'] ?>ms
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($latest && $latest['checked_at']): ?>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    Last checked: <?= date('M j, H:i', strtotime($latest['checked_at'])) ?>
                                                </small>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <a href="?action=site_details&id=<?= $site['id'] ?>" class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-chart-line"></i> Details
                                            </a>
                                            <a href="?action=edit_site&id=<?= $site['id'] ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?action=delete_site&id=<?= $site['id'] ?>" class="btn btn-outline-danger btn-sm" 
                                               onclick="return confirm('Are you sure you want to delete this site?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

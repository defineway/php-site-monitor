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
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>URL</th>
                                    <th>Status</th>
                                    <th>Response Time</th>
                                    <th>Last Checked</th>
                                    <th>Actions</th>
                                    <?php if ($currentUser->isAdmin()): ?>
                                        <th>Owner</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sites as $site): 
                                    $latest = $latestResults[$site->getId()] ?? null;
                                    $statusClass = $latest ? 'status-' . $latest['status'] : 'text-muted';
                                    $statusIcon = '';
                                    switch ($latest['status'] ?? 'unknown') {
                                        case 'up': $statusIcon = 'fas fa-check-circle text-success'; break;
                                        case 'down': $statusIcon = 'fas fa-times-circle text-danger'; break;
                                        case 'warning': $statusIcon = 'fas fa-exclamation-triangle text-warning'; break;
                                        default: $statusIcon = 'fas fa-question-circle text-muted';
                                    }
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($site->getName()) ?></td>
                                        <td><a href="<?= htmlspecialchars($site->getUrl()) ?>" target="_blank"><?= htmlspecialchars($site->getUrl()) ?></a></td>
                                        <td>
                                            <span class="<?= $statusClass ?>">
                                                <i class="<?= $statusIcon ?>"></i>
                                                <?= $latest ? ucfirst($latest['status']) : 'Unknown' ?>
                                            </span>
                                        </td>
                                        <td><?= $latest ? $latest['response_time'] . 'ms' : 'N/A' ?></td>
                                        <td><?= $latest ? date('M j, H:i', strtotime($latest['checked_at'])) : 'Never' ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="?action=site_details&id=<?= $site->getId() ?>" class="btn btn-sm btn-info">Details</a>
                                                <a href="?action=edit_site&id=<?= $site->getId() ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <a href="?action=delete_site&id=<?= $site->getId() ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this site?')">Delete</a>
                                            </div>
                                        </td>
                                        <?php if ($currentUser->isAdmin()): ?>
                                            <td><?= htmlspecialchars($site->getUser()->getUsername() ?? 'N/A') ?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

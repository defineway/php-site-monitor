<?php 
require_once __DIR__ . '/security.php'; 

// Set current page for navigation highlighting
$currentPage = 'site_details';
// $currentUser is provided by the controller
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Details - PHP Site Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="container mt-4">
        <h1>Site Details</h1>
        
        <?php if (isset($site)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5><?= htmlspecialchars($site['name'] ?? 'N/A') ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>URL:</strong> <a href="<?= htmlspecialchars($site['url'] ?? '#') ?>" target="_blank"><?= htmlspecialchars($site['url'] ?? 'N/A') ?></a></p>
                    <p><strong>Check Interval:</strong> <?= htmlspecialchars($site['check_interval'] ?? 'N/A') ?> minutes</p>
                    <p><strong>Created:</strong> <?= htmlspecialchars($site['created_at'] ?? 'N/A') ?></p>
                </div>
            </div>
            
            <h3>Monitoring History</h3>
            <?php if (!empty($results)): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Response Time</th>
                                <th>HTTP Code</th>
                                <th>Checked At</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $result): ?>
                                <tr>
                                    <td>
                                        <?php
                                            $status = $result['status'] ?? 'unknown';
                                            $badgeClass = 'warning'; // Default
                                            if ($status === 'up') {
                                                $badgeClass = 'success';
                                            } elseif ($status === 'down') {
                                                $badgeClass = 'danger';
                                            }
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>">
                                            <?= htmlspecialchars($status) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($result['response_time'] ?? 'N/A') ?>ms</td>
                                    <td><?= htmlspecialchars($result['http_status_code'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($result['checked_at'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($result['message'] ?? '') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No monitoring results available yet.</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-danger">Site not found.</div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            <?php if (isset($site)): ?>
                <a href="?action=edit_site&id=<?= $site['id'] ?? '' ?>" class="btn btn-primary">Edit Site</a>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

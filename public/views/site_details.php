<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Details - <?= htmlspecialchars($site['name'] ?? 'Unknown') ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-up { color: #28a745; }
        .status-down { color: #dc3545; }
        .status-warning { color: #ffc107; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Site Details: <?= htmlspecialchars($site['name'] ?? 'Unknown') ?></h1>
                    <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
                
                <?php if (!$site): ?>
                    <div class="alert alert-danger">Site not found.</div>
                <?php else: ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Site Information</h5>
                            <p><strong>Name:</strong> <?= htmlspecialchars($site['name']) ?></p>
                            <p><strong>URL:</strong> <a href="<?= htmlspecialchars($site['url']) ?>" target="_blank"><?= htmlspecialchars($site['url']) ?></a></p>
                            <p><strong>Check Interval:</strong> <?= $site['check_interval'] ?> seconds</p>
                            <p><strong>SSL Monitoring:</strong> <?= $site['ssl_check_enabled'] ? 'Enabled' : 'Disabled' ?></p>
                            <?php if ($site['ssl_check_enabled']): ?>
                                <p><strong>SSL Check Interval:</strong> <?= $site['ssl_check_interval'] ?> seconds</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h3>Monitoring History</h3>
                    <?php if (empty($results)): ?>
                        <div class="alert alert-info">No monitoring data available yet.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Response Time</th>
                                        <th>Status Code</th>
                                        <th>SSL Expiry</th>
                                        <th>Error</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $result): ?>
                                        <tr>
                                            <td><?= date('Y-m-d H:i:s', strtotime($result['checked_at'])) ?></td>
                                            <td><?= ucfirst($result['check_type']) ?></td>
                                            <td><span class="status-<?= $result['status'] ?>"><?= ucfirst($result['status']) ?></span></td>
                                            <td><?= $result['response_time'] ? $result['response_time'] . 'ms' : '-' ?></td>
                                            <td><?= $result['status_code'] ?: '-' ?></td>
                                            <td><?= $result['ssl_expiry_date'] ?: '-' ?></td>
                                            <td><?= $result['error_message'] ? htmlspecialchars($result['error_message']) : '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

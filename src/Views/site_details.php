<?php require_once __DIR__ . '/security.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Details - PHP Site Monitor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Site Details</h1>
        
        <?php if (isset($site)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5><?= htmlspecialchars($site['name']) ?></h5>
                </div>
                <div class="card-body">
                    <p><strong>URL:</strong> <a href="<?= htmlspecialchars($site['url']) ?>" target="_blank"><?= htmlspecialchars($site['url']) ?></a></p>
                    <p><strong>Check Interval:</strong> <?= htmlspecialchars($site['check_interval']) ?> minutes</p>
                    <p><strong>Created:</strong> <?= htmlspecialchars($site['created_at']) ?></p>
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
                                        <span class="badge bg-<?= $result['status'] === 'up' ? 'success' : ($result['status'] === 'down' ? 'danger' : 'warning') ?>">
                                            <?= htmlspecialchars($result['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($result['response_time']) ?>ms</td>
                                    <td><?= htmlspecialchars($result['http_status_code']) ?></td>
                                    <td><?= htmlspecialchars($result['checked_at']) ?></td>
                                    <td><?= htmlspecialchars($result['message']) ?></td>
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
                <a href="?action=edit_site&id=<?= $site['id'] ?>" class="btn btn-primary">Edit Site</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

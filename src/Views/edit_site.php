<?php 
require_once __DIR__ . '/security.php'; 

// Set current page for navigation highlighting
$currentPage = 'edit_site';
// $site and $currentUser are provided by the controller
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Site - PHP Site Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="container mt-4">
        <h1>Edit Site</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form action="?action=edit_site&id=<?= htmlspecialchars($site->getId()) ?>" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Site Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($site->getName() ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="url" class="form-label">URL</label>
                <input type="url" class="form-control" id="url" name="url" value="<?= htmlspecialchars($site->getUrl() ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label for="check_interval" class="form-label">Check Interval</label>
                <select class="form-select" id="check_interval" name="check_interval">
                    <option value="1" <?= ($site->getCheckInterval() ?? 5) == 1 ? 'selected' : '' ?>>Every Minute</option>
                    <option value="5" <?= ($site->getCheckInterval() ?? 5) == 5 ? 'selected' : '' ?>>Every 5 Minutes</option>
                    <option value="10" <?= ($site->getCheckInterval() ?? 5) == 10 ? 'selected' : '' ?>>Every 10 Minutes</option>
                    <option value="15" <?= ($site->getCheckInterval() ?? 5) == 15 ? 'selected' : '' ?>>Every 15 Minutes</option>
                    <option value="30" <?= ($site->getCheckInterval() ?? 5) == 30 ? 'selected' : '' ?>>Every 30 Minutes</option>
                    <option value="60" <?= ($site->getCheckInterval() ?? 5) == 60 ? 'selected' : '' ?>>Every Hour</option>
                    <option value="720" <?= ($site->getCheckInterval() ?? 5) == 720 ? 'selected' : '' ?>>Every 12 Hours</option>
                    <option value="1440" <?= ($site->getCheckInterval() ?? 5) == 1440 ? 'selected' : '' ?>>Every Day</option>
                </select>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="ssl_check_enabled" name="ssl_check_enabled" value="1" <?= !empty($site->isSslCheckEnabled()) ? 'checked' : '' ?>>
                <label class="form-check-label" for="ssl_check_enabled">Enable SSL Certificate Check</label>
            </div>
            <div class="mb-3">
                <label for="ssl_check_interval" class="form-label">SSL Check Frequency</label>
                <select class="form-select" id="ssl_check_interval" name="ssl_check_interval">
                    <option value="900" <?= ($site->getSslCheckInterval() ?? 86400) == 900 ? 'selected' : '' ?>>Every 15 Minutes</option>
                    <option value="1800" <?= ($site->getSslCheckInterval() ?? 86400) == 1800 ? 'selected' : '' ?>>Every 30 Minutes</option>
                    <option value="3600" <?= ($site->getSslCheckInterval() ?? 86400) == 3600 ? 'selected' : '' ?>>Every Hour</option>
                    <option value="43200" <?= ($site->getSslCheckInterval() ?? 86400) == 43200 ? 'selected' : '' ?>>Every 12 Hours</option>
                    <option value="86400" <?= ($site->getSslCheckInterval() ?? 86400) == 86400 ? 'selected' : '' ?>>Every Day</option>
                    <option value="604800" <?= ($site->getSslCheckInterval() ?? 86400) == 604800 ? 'selected' : '' ?>>Every Week</option>
                    <option value="2592000" <?= ($site->getSslCheckInterval() ?? 86400) == 2592000 ? 'selected' : '' ?>>Every Month</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Site</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

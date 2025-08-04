<?php 
require_once __DIR__ . '/security.php'; 

// Helper function to format seconds into a human-readable string
function format_interval($seconds) {
    if ($seconds < 3600) return round($seconds / 60) . " Minutes";
    if ($seconds < 86400) return round($seconds / 3600) . " Hours";
    if ($seconds < 604800) return round($seconds / 86400) . " Days";
    if ($seconds < 2592000) return round($seconds / 604800) . " Weeks";
    return round($seconds / 2592000) . " Months";
}

// Set current page for navigation highlighting
$currentPage = 'site_details';
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
            <?php
            // Get latest status from results
            $latestUptimeStatus = null;
            $latestSslStatus = null;
            $latestUptimeResult = null;
            $latestSslResult = null;
            
            if (!empty($results)) {
                foreach ($results as $result) {
                    if ($result->getCheckType() === 'ssl') {
                        if (!$latestSslResult || $result->getCheckedAt() > $latestSslResult->getCheckedAt()) {
                            $latestSslResult = $result;
                            $latestSslStatus = $result->getStatus();
                        }
                    } else {
                        if (!$latestUptimeResult || $result->getCheckedAt() > $latestUptimeResult->getCheckedAt()) {
                            $latestUptimeResult = $result;
                            $latestUptimeStatus = $result->getStatus();
                        }
                    }
                }
            }
            ?>
            
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="fas fa-globe me-2"></i>
                            <?= htmlspecialchars($site->getName() ?? 'N/A') ?>
                        </h5>
                    </div>
                    <div class="d-flex gap-2">
                        <?php if ($latestUptimeStatus): ?>
                            <?php
                                $uptimeBadgeClass = 'secondary';
                                $uptimeIcon = 'question-circle';
                                if ($latestUptimeStatus === 'up') {
                                    $uptimeBadgeClass = 'success';
                                    $uptimeIcon = 'check-circle';
                                } elseif ($latestUptimeStatus === 'down') {
                                    $uptimeBadgeClass = 'danger';
                                    $uptimeIcon = 'times-circle';
                                }
                            ?>
                            <span class="badge bg-<?= $uptimeBadgeClass ?> fs-6">
                                <i class="fas fa-<?= $uptimeIcon ?> me-1"></i>
                                <?= htmlspecialchars($latestUptimeStatus) ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($latestSslStatus && $site->isSslCheckEnabled()): ?>
                            <?php
                                $sslBadgeClass = 'secondary';
                                $sslIcon = 'question-circle';
                                if ($latestSslStatus === 'up') {
                                    $sslBadgeClass = 'success';
                                    $sslIcon = 'shield-alt';
                                } elseif ($latestSslStatus === 'down' || $latestSslStatus === 'N/A') {
                                    $sslBadgeClass = 'danger';
                                    $sslIcon = 'exclamation-triangle';
                                } elseif ($latestSslStatus === 'warning') {
                                    $sslBadgeClass = 'warning';
                                    $sslIcon = 'clock';
                                }
                            ?>
                            <span class="badge bg-<?= $sslBadgeClass ?> fs-6">
                                <i class="fas fa-<?= $sslIcon ?> me-1"></i>
                                SSL <?= htmlspecialchars($latestSslStatus) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1"><i class="fas fa-link me-2"></i>Website URL</h6>
                                <a href="<?= htmlspecialchars($site->getUrl() ?? '#') ?>" target="_blank" class="text-decoration-none">
                                    <?= htmlspecialchars($site->getUrl() ?? 'N/A') ?>
                                    <i class="fas fa-external-link-alt ms-1 small"></i>
                                </a>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-muted mb-1"><i class="fas fa-clock me-2"></i>Check Interval</h6>
                                <span class="fw-bold">Every <?= htmlspecialchars($site->getCheckInterval() ?? 'N/A') ?> minutes</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1"><i class="fas fa-shield-alt me-2"></i>SSL Monitoring</h6>
                                <?php if ($site->isSslCheckEnabled()): ?>
                                    <span class="badge bg-success me-2">Enabled</span>
                                    <small class="text-muted">Every <?= format_interval($site->getSslCheckInterval() ?? 86400) ?></small>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Disabled</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-muted mb-1"><i class="fas fa-calendar-alt me-2"></i>Created</h6>
                                <span><?= htmlspecialchars(date('M d, Y', strtotime($site->getCreatedAt() ?? 'now'))) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($latestUptimeResult): ?>
                        <div class="row mt-3 pt-3 border-top">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2"><i class="fas fa-tachometer-alt me-2"></i>Latest Uptime Check</h6>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">Response Time:</small>
                                    <strong><?= htmlspecialchars($latestUptimeResult->getResponseTime() ?? 'N/A') ?>ms</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">HTTP Code:</small>
                                    <strong><?= htmlspecialchars($latestUptimeResult->getStatusCode() ?? 'N/A') ?></strong>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2"><i class="fas fa-clock me-2"></i>Last Checked</h6>
                                <small><?= htmlspecialchars($latestUptimeResult->getCheckedAt() ?? 'N/A') ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($latestSslResult && $site->isSslCheckEnabled()): ?>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2"><i class="fas fa-certificate me-2"></i>Latest SSL Check</h6>
                                <?php if ($latestSslResult->getSslExpiryDate()): ?>
                                    <?php
                                        $expiryDate = new DateTime($latestSslResult->getSslExpiryDate());
                                        $now = new DateTime();
                                        $diff = $now->diff($expiryDate);
                                        $daysLeft = $expiryDate < $now ? 0 : $diff->days;
                                    ?>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Expires:</small>
                                        <strong><?= date('M d, Y', strtotime($latestSslResult->getSslExpiryDate())) ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">Days Left:</small>
                                        <strong class="<?php
                                            if ($daysLeft < 7) {
                                                echo 'text-danger';
                                            } elseif ($daysLeft < 30) {
                                                echo 'text-warning';
                                            }
                                        ?>">
                                            <?= $daysLeft ?> days
                                        </strong>
                                    </div>
                                <?php else: ?>
                                    <small class="text-muted">No SSL expiry data available</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php
            // Separate results by check type
            $uptimeResults = [];
            $sslResults = [];
            
            if (!empty($results)) {
                foreach ($results as $result) {
                    if ($result->getCheckType() === 'ssl') {
                        $sslResults[] = $result;
                    } else {
                        $uptimeResults[] = $result;
                    }
                }
            }
            ?>
            
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        <h3 class="mb-0">Site Uptime History</h3>
                    </div>
                    <?php if (!empty($uptimeResults)): ?>
                        <div class="card shadow-sm">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-heartbeat me-1"></i>Status</th>
                                            <th><i class="fas fa-stopwatch me-1"></i>Response Time</th>
                                            <th><i class="fas fa-code me-1"></i>HTTP Code</th>
                                            <th><i class="fas fa-clock me-1"></i>Checked At</th>
                                            <th><i class="fas fa-comment me-1"></i>Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($uptimeResults as $result): ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                        $status = $result->getStatus() ?? 'unknown';
                                                        $badgeClass = 'warning'; // Default
                                                        $icon = 'question-circle';
                                                        if ($status === 'up') {
                                                            $badgeClass = 'success';
                                                            $icon = 'check-circle';
                                                        } elseif ($status === 'down') {
                                                            $badgeClass = 'danger';
                                                            $icon = 'times-circle';
                                                        }
                                                    ?>
                                                    <span class="badge bg-<?= $badgeClass ?>">
                                                        <i class="fas fa-<?= $icon ?> me-1"></i>
                                                        <?= htmlspecialchars($status) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $responseTime = $result->getResponseTime();
                                                    if ($responseTime !== null) {
                                                        $timeClass = '';
                                                        if ($responseTime > 2000) {
                                                            $timeClass = 'text-danger';
                                                        } elseif ($responseTime > 1000) {
                                                            $timeClass = 'text-warning';
                                                        } else {
                                                            $timeClass = 'text-success';
                                                        }
                                                        echo '<span class="' . $timeClass . '">' . htmlspecialchars($responseTime) . 'ms</span>';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    $statusCode = $result->getStatusCode();
                                                    if ($statusCode) {
                                                        $codeClass = '';
                                                        if ($statusCode >= 200 && $statusCode < 300) {
                                                            $codeClass = 'text-success';
                                                        } elseif ($statusCode >= 300 && $statusCode < 400) {
                                                            $codeClass = 'text-info';
                                                        } elseif ($statusCode >= 400) {
                                                            $codeClass = 'text-danger';
                                                        }
                                                        echo '<span class="' . $codeClass . '">' . htmlspecialchars($statusCode) . '</span>';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </td>
                                                <td><small><?= htmlspecialchars($result->getCheckedAt() ?? 'N/A') ?></small></td>
                                                <td><small class="text-muted"><?= htmlspecialchars($result->getErrorMessage() ?? '') ?></small></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No uptime monitoring results available yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($site->isSslCheckEnabled()): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            <h3 class="mb-0">SSL Certificate History</h3>
                        </div>
                        <?php if (!empty($sslResults)): ?>
                            <div class="card shadow-sm">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th><i class="fas fa-certificate me-1"></i>Status</th>
                                                <th><i class="fas fa-calendar-alt me-1"></i>SSL Expiry Date</th>
                                                <th><i class="fas fa-hourglass-half me-1"></i>Days Until Expiry</th>
                                                <th><i class="fas fa-clock me-1"></i>Checked At</th>
                                                <th><i class="fas fa-comment me-1"></i>Message</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sslResults as $result): ?>
                                                <tr>
                                                    <td>
                                                        <?php
                                                            $status = $result->getStatus() ?? 'unknown';
                                                            $badgeClass = 'warning'; // Default
                                                            $icon = 'question-circle';
                                                            if ($status === 'up') {
                                                                $badgeClass = 'success';
                                                                $icon = 'shield-alt';
                                                            } elseif ($status === 'down' || $status === 'N/A') {
                                                                $badgeClass = 'danger';
                                                                $icon = 'exclamation-triangle';
                                                            } elseif ($status === 'warning') {
                                                                $badgeClass = 'warning';
                                                                $icon = 'clock';
                                                            }
                                                        ?>
                                                        <span class="badge bg-<?= $badgeClass ?>">
                                                            <i class="fas fa-<?= $icon ?> me-1"></i>
                                                            <?= htmlspecialchars($status) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php if ($result->getSslExpiryDate()): ?>
                                                            <?= htmlspecialchars(date('M d, Y H:i:s', strtotime($result->getSslExpiryDate()))) ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if ($result->getSslExpiryDate()) {
                                                            $expiryDate = new DateTime($result->getSslExpiryDate());
                                                            $now = new DateTime();
                                                            $diff = $now->diff($expiryDate);
                                                            
                                                            if ($expiryDate < $now) {
                                                                echo '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Expired</span>';
                                                            } else {
                                                                $daysLeft = $diff->days;
                                                                $badgeClass = 'success';
                                                                $icon = 'check-circle';
                                                                if ($daysLeft < 7) {
                                                                    $badgeClass = 'danger';
                                                                    $icon = 'exclamation-triangle';
                                                                } elseif ($daysLeft < 30) {
                                                                    $badgeClass = 'warning';
                                                                    $icon = 'clock';
                                                                }
                                                                echo '<span class="badge bg-' . $badgeClass . '"><i class="fas fa-' . $icon . ' me-1"></i>' . $daysLeft . ' days</span>';
                                                            }
                                                        } else {
                                                            echo '<span class="text-muted">N/A</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><small><?= htmlspecialchars($result->getCheckedAt() ?? 'N/A') ?></small></td>
                                                    <td><small class="text-muted"><?= htmlspecialchars($result->getErrorMessage() ?? '') ?></small></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No SSL monitoring results available yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-danger">Site not found.</div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            <?php if (isset($site)): ?>
                <a href="?action=edit_site&id=<?= $site->getId() ?? '' ?>" class="btn btn-primary">Edit Site</a>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

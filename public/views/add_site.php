<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Site - PHP Site Monitor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-monitor"></i> PHP Site Monitor
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-dashboard"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="?action=add_site">
                            <i class="fas fa-plus"></i> Add Site
                        </a>
                    </li>
                    <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=users">
                            <i class="fas fa-users"></i> Users
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($currentUser['username'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?action=profile">
                                <i class="fas fa-user-edit"></i> Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="?action=logout">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-plus"></i> Add New Site to Monitor
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="?action=add_site">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-tag"></i> Site Name *
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?= htmlspecialchars($formData['name'] ?? '') ?>"
                                       placeholder="e.g., My Company Website"
                                       required>
                                <div class="form-text">
                                    A friendly name to identify this site
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="url" class="form-label">
                                    <i class="fas fa-link"></i> Website URL *
                                </label>
                                <input type="url" 
                                       class="form-control" 
                                       id="url" 
                                       name="url" 
                                       value="<?= htmlspecialchars($formData['url'] ?? '') ?>"
                                       placeholder="https://example.com"
                                       required>
                                <div class="form-text">
                                    The full URL including http:// or https://
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="check_interval" class="form-label">
                                            <i class="fas fa-clock"></i> Check Interval (seconds)
                                        </label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="check_interval" 
                                               name="check_interval" 
                                               value="<?= htmlspecialchars($formData['check_interval'] ?? 300) ?>"
                                               min="60"
                                               placeholder="300">
                                        <div class="form-text">
                                            How often to check the site (minimum 60 seconds)
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="ssl_check_interval" class="form-label">
                                            <i class="fas fa-certificate"></i> SSL Check Interval (seconds)
                                        </label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="ssl_check_interval" 
                                               name="ssl_check_interval" 
                                               value="<?= htmlspecialchars($formData['ssl_check_interval'] ?? 86400) ?>"
                                               min="3600"
                                               placeholder="86400">
                                        <div class="form-text">
                                            How often to check SSL certificate (minimum 1 hour)
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="ssl_check_enabled" 
                                           name="ssl_check_enabled" 
                                           value="1"
                                           <?= ($formData['ssl_check_enabled'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="ssl_check_enabled">
                                        <i class="fas fa-shield-alt"></i> Enable SSL certificate monitoring
                                    </label>
                                    <div class="form-text">
                                        Monitor SSL certificate validity and expiration dates
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Add Site
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> Monitoring Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-check-circle text-success"></i> What We Monitor</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-globe"></i> Website accessibility</li>
                                    <li><i class="fas fa-tachometer-alt"></i> Response time</li>
                                    <li><i class="fas fa-code"></i> HTTP status codes</li>
                                    <li><i class="fas fa-shield-alt"></i> SSL certificate validity</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-bell text-warning"></i> Alert Conditions</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-times-circle text-danger"></i> Site unreachable</li>
                                    <li><i class="fas fa-exclamation-triangle text-warning"></i> Slow response (>5s)</li>
                                    <li><i class="fas fa-bug text-danger"></i> HTTP errors (4xx, 5xx)</li>
                                    <li><i class="fas fa-certificate text-warning"></i> SSL expiring soon</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-detect protocol for URL
        document.getElementById('url').addEventListener('blur', function() {
            let url = this.value.trim();
            if (url && !url.match(/^https?:\/\//)) {
                this.value = 'https://' + url;
            }
        });
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const url = document.getElementById('url').value;
            const name = document.getElementById('name').value.trim();
            
            if (!name) {
                e.preventDefault();
                alert('Please enter a site name');
                return;
            }
            
            if (!url.match(/^https?:\/\/.+/)) {
                e.preventDefault();
                alert('Please enter a valid URL starting with http:// or https://');
                return;
            }
        });
    </script>
</body>
</html>

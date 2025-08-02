<?php require_once __DIR__ . "/security.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - PHP Site Monitor</title>
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
                        <a class="nav-link" href="?action=add_site">
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
                        <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($currentUser['username'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="?action=profile">
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
        <!-- Alerts -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <?php
                switch ($_GET['success']) {
                    case 'profile_updated': echo 'Your profile has been updated successfully.'; break;
                    case 'password_changed': echo 'Your password has been changed successfully.'; break;
                    default: echo 'Operation completed successfully.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($user) && $user): ?>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-user-circle"></i> My Profile</h1>
                    <a href="?action=change_password" class="btn btn-outline-primary">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </div>

                <div class="row">
                    <!-- User Info Card -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle"></i> Account Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        <i class="fas fa-hashtag"></i> User ID
                                    </label>
                                    <div class="fw-bold"><?= $user['id'] ?></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        <i class="fas fa-user"></i> Username
                                    </label>
                                    <div class="fw-bold"><?= htmlspecialchars($user['username']) ?></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        <i class="fas fa-shield-alt"></i> Role
                                    </label>
                                    <div>
                                        <?php if ($user['role'] === 'admin'): ?>
                                            <span class="badge bg-danger fs-6">
                                                <i class="fas fa-crown"></i> Administrator
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-primary fs-6">
                                                <i class="fas fa-user"></i> User
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted">
                                        <i class="fas fa-calendar-plus"></i> Member Since
                                    </label>
                                    <div class="fw-bold"><?= date('F j, Y', strtotime($user['created_at'])) ?></div>
                                </div>

                                <?php if (isset($user['updated_at']) && $user['updated_at']): ?>
                                <div class="mb-0">
                                    <label class="form-label text-muted">
                                        <i class="fas fa-clock"></i> Last Updated
                                    </label>
                                    <div class="fw-bold"><?= date('M j, Y g:i A', strtotime($user['updated_at'])) ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Card -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-edit"></i> Update Profile
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope"></i> Email Address
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               value="<?= htmlspecialchars($user['email']) ?>"
                                               required>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> 
                                            This will be used for notifications and account recovery.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock"></i> New Password
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Leave blank to keep current password">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> 
                                            Password must be at least 8 characters long. Leave blank to keep current password.
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Update Profile
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-bolt"></i> Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="index.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-dashboard"></i> Go to Dashboard
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="?action=add_site" class="btn btn-outline-success w-100">
                                    <i class="fas fa-plus"></i> Add New Site
                                </a>
                            </div>
                            <?php if ($currentUser['role'] === 'admin'): ?>
                            <div class="col-md-6 mb-3">
                                <a href="?action=users" class="btn btn-outline-info w-100">
                                    <i class="fas fa-users"></i> Manage Users
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="?action=add_user" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-user-plus"></i> Add New User
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error:</strong> Unable to load user profile data. Please try logging out and logging back in.
                </div>
                <div class="text-center">
                    <a href="index.php" class="btn btn-primary">Go to Dashboard</a>
                    <a href="?action=logout" class="btn btn-outline-secondary ms-2">Logout</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const helpText = this.nextElementSibling;
            
            if (password.length > 0 && password.length < 8) {
                this.classList.add('is-invalid');
                helpText.classList.add('text-danger');
                helpText.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Password must be at least 8 characters long.';
            } else {
                this.classList.remove('is-invalid');
                helpText.classList.remove('text-danger');
                helpText.innerHTML = '<i class="fas fa-info-circle"></i> Password must be at least 8 characters long. Leave blank to keep current password.';
            }
        });
    </script>
</body>
</html>

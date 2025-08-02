<?php require_once __DIR__ . "/security.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - PHP Site Monitor</title>
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
        <!-- Back Link -->
        <div class="mb-3">
            <a href="?action=users" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>

        <!-- Alerts -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-user-edit"></i> Edit User: <?= htmlspecialchars($user['username']) ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">
                                            <i class="fas fa-user"></i> Username <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="username" 
                                               name="username" 
                                               value="<?= htmlspecialchars($user['username']) ?>"
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               value="<?= htmlspecialchars($user['email']) ?>"
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">
                                            <i class="fas fa-shield-alt"></i> Role <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="role" name="role" required>
                                            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">
                                            <i class="fas fa-toggle-on"></i> Status <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="active" <?= $user['is_active'] ? 'selected' : '' ?>>Active</option>
                                            <option value="inactive" <?= !$user['is_active'] ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                    </div>
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

                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-info-circle"></i> User Information</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <strong>User ID:</strong> <?= $user['id'] ?><br>
                                                <strong>Created:</strong> <?= date('M j, Y g:i A', strtotime($user['created_at'])) ?><br>
                                                <?php if (isset($user['updated_at']) && $user['updated_at']): ?>
                                                <strong>Last Updated:</strong> <?= date('M j, Y g:i A', strtotime($user['updated_at'])) ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <strong>Current Status:</strong> 
                                                <?php if ($user['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Inactive</span>
                                                <?php endif; ?><br>
                                                <strong>Current Role:</strong> 
                                                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                                    <?= ucfirst($user['role']) ?>
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Update User
                                    </button>
                                    <a href="?action=users" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                                
                                <?php if ($user['id'] != $currentUser['id']): ?>
                                <div>
                                    <?php if ($user['is_active']): ?>
                                        <a href="?action=deactivate_user&id=<?= $user['id'] ?>" 
                                           class="btn btn-warning btn-sm"
                                           onclick="return confirm('Are you sure you want to deactivate this user?')">
                                            <i class="fas fa-pause"></i> Deactivate
                                        </a>
                                    <?php else: ?>
                                        <a href="?action=activate_user&id=<?= $user['id'] ?>" 
                                           class="btn btn-success btn-sm">
                                            <i class="fas fa-play"></i> Activate
                                        </a>
                                    <?php endif; ?>
                                    
                                    <div class="btn-group ms-1" role="group">
                                        <a href="?action=delete_user&id=<?= $user['id'] ?>" 
                                           class="btn btn-warning btn-sm"
                                           title="Deactivate User (Soft Delete)"
                                           onclick="return confirm('Are you sure you want to deactivate this user? They will be hidden but can be reactivated later.')">
                                            <i class="fas fa-user-slash"></i> Deactivate
                                        </a>
                                        <a href="?action=delete_user&id=<?= $user['id'] ?>&hard=true" 
                                           class="btn btn-danger btn-sm"
                                           title="Permanently Delete User"
                                           onclick="return confirm('Are you sure you want to PERMANENTLY delete this user? This action cannot be undone and will remove all their data!')">
                                            <i class="fas fa-trash"></i> Delete Forever
                                        </a>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - PHP Site Monitor</title>
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus"></i> Add New User
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="?action=add_user">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">
                                            <i class="fas fa-user"></i> Username *
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="username" 
                                               name="username" 
                                               value="<?= htmlspecialchars($formData['username'] ?? '') ?>"
                                               placeholder="Enter username"
                                               required>
                                        <div class="form-text">
                                            Username must be unique and at least 3 characters
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope"></i> Email Address *
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                                               placeholder="user@example.com"
                                               required>
                                        <div class="form-text">
                                            Email must be unique and valid
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock"></i> Password *
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password" 
                                               name="password" 
                                               placeholder="Enter password"
                                               required>
                                        <div class="form-text">
                                            Password must be at least 6 characters
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirm" class="form-label">
                                            <i class="fas fa-lock"></i> Confirm Password *
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirm" 
                                               name="password_confirm" 
                                               placeholder="Confirm password"
                                               required>
                                        <div class="form-text">
                                            Must match the password above
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">
                                            <i class="fas fa-shield-alt"></i> User Role
                                        </label>
                                        <select class="form-select" id="role" name="role">
                                            <option value="user" <?= ($formData['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>
                                                <i class="fas fa-user"></i> Regular User
                                            </option>
                                            <option value="admin" <?= ($formData['role'] ?? 'user') === 'admin' ? 'selected' : '' ?>>
                                                <i class="fas fa-crown"></i> Administrator
                                            </option>
                                        </select>
                                        <div class="form-text">
                                            Administrators can manage users and all sites
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-toggle-on"></i> Account Status
                                        </label>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_active" 
                                                   name="is_active" 
                                                   value="1"
                                                   <?= ($formData['is_active'] ?? true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="is_active">
                                                Account is active and can login
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="?action=users" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Users
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> User Roles & Permissions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="fas fa-user text-primary"></i> Regular User</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> View own monitored sites</li>
                                    <li><i class="fas fa-check text-success"></i> Add new sites to monitor</li>
                                    <li><i class="fas fa-check text-success"></i> Edit own sites</li>
                                    <li><i class="fas fa-check text-success"></i> View monitoring results</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-crown text-danger"></i> Administrator</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success"></i> All regular user permissions</li>
                                    <li><i class="fas fa-check text-success"></i> Manage all users</li>
                                    <li><i class="fas fa-check text-success"></i> View all monitored sites</li>
                                    <li><i class="fas fa-check text-success"></i> System configuration</li>
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
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            
            if (username.length < 3) {
                e.preventDefault();
                alert('Username must be at least 3 characters long');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
                return;
            }
            
            if (password !== passwordConfirm) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
            
            if (!email.includes('@')) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return;
            }
        });
    </script>
</body>
</html>

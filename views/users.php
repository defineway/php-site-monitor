<?php require_once __DIR__ . '/security.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - PHP Site Monitor</title>
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
                        <a class="nav-link active" href="?action=users">
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
                    case 'user_added': echo 'User has been added successfully.'; break;
                    case 'user_updated': echo 'User has been updated successfully.'; break;
                    case 'user_deleted': echo 'User has been deleted successfully.'; break;
                    case 'user_permanently_deleted': echo 'User has been permanently deleted from the system.'; break;
                    case 'user_activated': echo 'User has been activated successfully.'; break;
                    case 'user_deactivated': echo 'User has been deactivated successfully.'; break;
                    default: echo 'Operation completed successfully.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-users"></i> User Management</h1>
                    <a href="?action=add_user" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Add New User
                    </a>
                </div>
                
                <?php if (empty($users)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        No users found. <a href="?action=add_user">Add your first user</a>.
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-list"></i> All Users (<?= count($users) ?>)
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th><i class="fas fa-hashtag"></i> ID</th>
                                            <th><i class="fas fa-user"></i> Username</th>
                                            <th><i class="fas fa-envelope"></i> Email</th>
                                            <th><i class="fas fa-shield-alt"></i> Role</th>
                                            <th><i class="fas fa-toggle-on"></i> Status</th>
                                            <th><i class="fas fa-calendar"></i> Created</th>
                                            <th><i class="fas fa-cogs"></i> Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= $user['id'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($user['username']) ?></strong>
                                                    <?php if ($user['id'] == $currentUser['id']): ?>
                                                        <span class="badge bg-info ms-1">You</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($user['email']) ?></td>
                                                <td>
                                                    <?php if ($user['role'] === 'admin'): ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-crown"></i> Admin
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-primary">
                                                            <i class="fas fa-user"></i> User
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($user['is_active']): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check"></i> Active
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-pause"></i> Inactive
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?= date('M j, Y', strtotime($user['created_at'])) ?></small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="?action=edit_user&id=<?= $user['id'] ?>" 
                                                           class="btn btn-outline-primary" 
                                                           title="Edit User">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        
                                                        <?php if ($user['id'] != $currentUser['id']): ?>
                                                            <?php if ($user['is_active']): ?>
                                                                <a href="?action=deactivate_user&id=<?= $user['id'] ?>" 
                                                                   class="btn btn-outline-warning" 
                                                                   title="Deactivate User"
                                                                   onclick="return confirm('Are you sure you want to deactivate this user?')">
                                                                    <i class="fas fa-pause"></i>
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="?action=activate_user&id=<?= $user['id'] ?>" 
                                                                   class="btn btn-outline-success" 
                                                                   title="Activate User">
                                                                    <i class="fas fa-play"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            
                                                            <div class="btn-group" role="group">
                                                                <a href="?action=delete_user&id=<?= $user['id'] ?>" 
                                                                   class="btn btn-outline-warning btn-sm" 
                                                                   title="Deactivate User (Soft Delete)"
                                                                   onclick="return confirm('Are you sure you want to deactivate this user? They will be hidden but can be reactivated later.')">
                                                                    <i class="fas fa-user-slash"></i>
                                                                </a>
                                                                <a href="?action=delete_user&id=<?= $user['id'] ?>&hard=true" 
                                                                   class="btn btn-outline-danger btn-sm" 
                                                                   title="Permanently Delete User"
                                                                   onclick="return confirm('Are you sure you want to PERMANENTLY delete this user? This action cannot be undone and will remove all their data!')">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="btn btn-outline-secondary disabled" title="Cannot modify your own account">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Total users: <?= count($users) ?> | 
                                Active: <?= count(array_filter($users, fn($u) => $u['is_active'])) ?> |
                                Admins: <?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?>
                            </small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php 
require_once __DIR__ . '/security.php'; 

// Set current page for navigation highlighting
$currentPage = 'users';
// $currentUser is provided by the controller
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - PHP Site Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .table th {
            border-top: none;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        .btn-group .btn {
            margin: 0 1px;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.5em 0.75em;
        }
        .alert {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-stats {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-users me-2 text-primary"></i>User Management</h1>
            <?php if (is_object($currentUser) && $currentUser->isAdmin()): ?>
                <a href="?action=add_user" class="btn btn-primary">
                    <i class="fas fa-user-plus me-2"></i>Add New User
                </a>
            <?php endif; ?>
        </div>
        
        <!-- User Statistics -->
        <?php 
        $totalUsers = 0;
        $activeUsers = 0;
        $adminUsers = 0;
        $activeAdminUsers = 0;
        if (!empty($users)) {
            foreach ($users as $user) {
                $totalUsers++;
                $isActive = $user->isActive();
                $isAdmin = $user->isAdmin();
                if ($isActive) $activeUsers++;
                if ($isAdmin) $adminUsers++;
                if ($isAdmin && $isActive) $activeAdminUsers++;
            }
        }
        ?>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                        <h4 class="mb-1"><?= $totalUsers ?></h4>
                        <p class="text-muted mb-0">Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-user-check fa-2x text-success mb-2"></i>
                        <h4 class="mb-1"><?= $activeUsers ?></h4>
                        <p class="text-muted mb-0">Active Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-crown fa-2x text-warning mb-2"></i>
                        <h4 class="mb-1"><?= $adminUsers ?></h4>
                        <p class="text-muted mb-0">Administrators</p>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php
                switch ($_GET['success']) {
                    case 'user_added': echo 'User added successfully!'; break;
                    case 'user_updated': echo 'User updated successfully!'; break;
                    case 'user_activated': echo 'User activated successfully!'; break;
                    case 'user_deactivated': echo 'User deactivated successfully!'; break;
                    default: echo 'Operation completed successfully!';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php
                switch ($_GET['error']) {
                    case 'user_not_found': echo 'User not found.'; break;
                    case 'cannot_delete_self': echo 'You cannot delete your own account.'; break;
                    case 'cannot_modify_self': echo 'You cannot modify your own status.'; break;
                    default: echo 'An error occurred.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th><i class="fas fa-hashtag me-1"></i>ID</th>
                        <th><i class="fas fa-user me-1"></i>Username</th>
                        <th><i class="fas fa-envelope me-1"></i>Email</th>
                        <th><i class="fas fa-user-tag me-1"></i>Role</th>
                        <th><i class="fas fa-circle me-1"></i>Status</th>
                        <th><i class="fas fa-calendar me-1"></i>Created</th>
                        <th><i class="fas fa-cogs me-1"></i>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <?php $userStatus = $user->isActive() ? 'active' : 'inactive'; ?>
                            <tr>
                                <td><?= htmlspecialchars($user->getId()) ?></td>
                                <td><?= htmlspecialchars($user->getUsername()) ?></td>
                                <td>
                                    <i class="fas fa-envelope me-1 text-muted"></i>
                                    <?= htmlspecialchars($user->getEmail()) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $user->getRole() === 'admin' ? 'primary' : 'info' ?>">
                                        <i class="fas fa-<?= $user->getRole() === 'admin' ? 'crown' : 'user' ?> me-1"></i>
                                        <?= htmlspecialchars($user->getRole()) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $userStatus === 'active' ? 'success' : 'secondary' ?>">
                                        <i class="fas fa-<?= $userStatus === 'active' ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                        <?= htmlspecialchars($userStatus) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($user->getCreatedAt()) ?></td>
                                <td>
                                    <?php if (isset($currentUser) && $currentUser->getRole() === 'admin'): ?>
                                        <?php 
                                        // Check if this user is an active admin and would be the last one
                                        $isThisUserActiveAdmin = ($user->getRole() ?? 'user') === 'admin' && ($userStatus ?? 'inactive') === 'active';
                                        $wouldBeLastActiveAdmin = $isThisUserActiveAdmin && $activeAdminUsers <= 1;
                                        ?>
                                        <div class="btn-group" role="group">
                                            <a href="?action=edit_user&id=<?= $user->getId() ?>" class="btn btn-sm btn-outline-primary" title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user->getId() != $currentUser->getId()): ?>
                                                <?php if ($userStatus === 'active'): ?>
                                                    <?php if ($wouldBeLastActiveAdmin): ?>
                                                        <button class="btn btn-sm btn-secondary" disabled title="Cannot deactivate the last active admin">
                                                            <i class="fas fa-shield-alt"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="?action=deactivate_user&id=<?= $user->getId() ?>" class="btn btn-sm btn-outline-warning" title="Deactivate User"
                                                           onclick="return confirm('Deactivate this user?')">
                                                            <i class="fas fa-user-slash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <a href="?action=activate_user&id=<?= $user->getId() ?>" class="btn btn-sm btn-outline-success" title="Activate User">
                                                        <i class="fas fa-user-check"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled title="You cannot modify your own account status">
                                                    <i class="fas fa-user-lock"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($user->getId() != $currentUser->getId()): ?>
                                                <?php if ($wouldBeLastActiveAdmin): ?>
                                                    <button class="btn btn-sm btn-secondary" disabled title="Cannot delete the last active admin">
                                                        <i class="fas fa-shield-alt"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <a href="?action=delete_user&id=<?= $user->getId() ?>&hard=true" class="btn btn-sm btn-danger" title="Delete User Permanently"
                                                       onclick="return confirm('Are you sure you want to PERMANENTLY DELETE this user? This action cannot be undone.')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled title="You cannot delete your own account">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-lock me-1"></i>Admin access required</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                <h5>No users found</h5>
                                <p class="mb-0">There are currently no users in the system.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>User Management</h1>
            <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
                <a href="?action=add_user" class="btn btn-primary">Add New User</a>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php
                switch ($_GET['success']) {
                    case 'user_added': echo 'User added successfully!'; break;
                    case 'user_updated': echo 'User updated successfully!'; break;
                    case 'user_activated': echo 'User activated successfully!'; break;
                    case 'user_deactivated': echo 'User deactivated successfully!'; break;
                    default: echo 'Operation completed successfully!';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                switch ($_GET['error']) {
                    case 'user_not_found': echo 'User not found.'; break;
                    case 'cannot_delete_self': echo 'You cannot delete your own account.'; break;
                    case 'cannot_modify_self': echo 'You cannot modify your own status.'; break;
                    default: echo 'An error occurred.';
                }
                ?>
            </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <?php $userStatus = $user['status'] ?? 'inactive'; ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id'] ?? '') ?></td>
                                <td><?= htmlspecialchars($user['username'] ?? '') ?></td>
                                <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                                <td><?= htmlspecialchars($user['role'] ?? 'user') ?></td>
                                <td>
                                    <span class="badge bg-<?= $userStatus === 'active' ? 'success' : 'secondary' ?>">
                                        <?= htmlspecialchars($userStatus) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($user['created_at'] ?? '') ?></td>
                                <td>
                                    <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
                                        <div class="btn-group" role="group">
                                            <a href="?action=edit_user&id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <?php if ($userStatus === 'active'): ?>
                                                <a href="?action=deactivate_user&id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-warning"
                                                   onclick="return confirm('Deactivate this user?')">Deactivate</a>
                                            <?php else: ?>
                                                <a href="?action=activate_user&id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-success">Activate</a>
                                            <?php endif; ?>
                                            <a href="?action=delete_user&id=<?= $user['id'] ?>&hard=true" class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure you want to PERMANENTLY DELETE this user? This action cannot be undone.')">Delete</a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Admin access required</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/security.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - PHP Site Monitor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>User Management</h1>
            <a href="?action=add_user" class="btn btn-primary">Add New User</a>
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
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['role']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $user['status'] === 'active' ? 'success' : 'secondary' ?>">
                                        <?= htmlspecialchars($user['status']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($user['created_at']) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="?action=edit_user&id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <?php if ($user['status'] === 'active'): ?>
                                            <a href="?action=deactivate_user&id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-warning"
                                               onclick="return confirm('Deactivate this user?')">Deactivate</a>
                                        <?php else: ?>
                                            <a href="?action=activate_user&id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-success">Activate</a>
                                        <?php endif; ?>
                                        <a href="?action=delete_user&id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                    </div>
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
</body>
</html>

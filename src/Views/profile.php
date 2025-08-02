<?php 
require_once __DIR__ . '/security.php'; 

// Set current page for navigation highlighting
$currentPage = 'profile';
// $currentUser is provided by the controller
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - PHP Site Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="container mt-4">
        <h1>User Profile</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Profile updated successfully!</div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($user)): ?>
                            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                            <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                            <p><strong>Status:</strong> <?= htmlspecialchars($user['status']) ?></p>
                            <p><strong>Created:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Update Profile</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="?action=change_password" class="btn btn-warning">Change Password</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

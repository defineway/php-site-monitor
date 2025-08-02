<?php 
require_once __DIR__ . '/security.php'; 

// Set current page for navigation highlighting
$currentPage = 'add_site';
// $currentUser is provided by the controller
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Site - PHP Site Monitor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="container mt-4">
        <h1>Add New Site</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Site Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="url" class="form-label">Site URL</label>
                <input type="url" class="form-control" id="url" name="url" required>
            </div>
            <div class="mb-3">
                <label for="check_interval" class="form-label">Check Interval (minutes)</label>
                <input type="number" class="form-control" id="check_interval" name="check_interval" value="5" min="1">
            </div>
            <button type="submit" class="btn btn-primary">Add Site</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

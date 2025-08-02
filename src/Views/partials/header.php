<?php
/**
 * Navigation header component
 * Include this in all authenticated pages
 */
?>
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
                    <a class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>" href="index.php">
                        <i class="fas fa-dashboard"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'add_site' ? 'active' : '' ?>" href="?action=add_site">
                        <i class="fas fa-plus"></i> Add Site
                    </a>
                </li>
                <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>" href="?action=users">
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

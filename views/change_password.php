<?php require_once __DIR__ . "/security.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - PHP Site Monitor</title>
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
            <a href="?action=profile" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Profile
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
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-key"></i> Change Password
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Security Note:</strong> Choose a strong password with at least 8 characters, including uppercase, lowercase, numbers, and special characters.
                        </div>

                        <form method="POST" id="changePasswordForm">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">
                                    <i class="fas fa-lock"></i> Current Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="current_password" 
                                           name="current_password" 
                                           required>
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('current_password', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    <i class="fas fa-info-circle"></i> Enter your current password to verify your identity.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">
                                    <i class="fas fa-key"></i> New Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password" 
                                           name="new_password" 
                                           required
                                           minlength="8">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('new_password', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text" id="new_password_help">
                                    <i class="fas fa-info-circle"></i> Password must be at least 8 characters long.
                                </div>
                                <div class="mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" id="password_strength" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted" id="password_strength_text">Password strength: <span id="strength_level">Not entered</span></small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-check-double"></i> Confirm New Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           required
                                           minlength="8">
                                    <button class="btn btn-outline-secondary" 
                                            type="button" 
                                            onclick="togglePassword('confirm_password', this)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text" id="confirm_password_help">
                                    <i class="fas fa-info-circle"></i> Re-enter your new password to confirm.
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success" id="submit_btn">
                                    <i class="fas fa-save"></i> Change Password
                                </button>
                                <a href="?action=profile" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Tips -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-lightbulb"></i> Password Security Tips
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Use at least 8 characters (12+ recommended)</li>
                            <li>Include uppercase and lowercase letters</li>
                            <li>Add numbers and special characters (!@#$%^&*)</li>
                            <li>Avoid common words, personal information, or predictable patterns</li>
                            <li>Don't reuse passwords from other accounts</li>
                            <li>Consider using a password manager</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(fieldId, button) {
            const field = document.getElementById(fieldId);
            const icon = button.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            let level = 'Weak';
            let color = 'bg-danger';

            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 25;
            if (/[a-z]/.test(password)) strength += 10;
            if (/[A-Z]/.test(password)) strength += 10;
            if (/[0-9]/.test(password)) strength += 10;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;

            if (strength >= 80) {
                level = 'Very Strong';
                color = 'bg-success';
            } else if (strength >= 60) {
                level = 'Strong';
                color = 'bg-info';
            } else if (strength >= 40) {
                level = 'Medium';
                color = 'bg-warning';
            } else if (strength >= 20) {
                level = 'Fair';
                color = 'bg-warning';
            }

            return { strength, level, color };
        }

        // Real-time password validation
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const helpText = document.getElementById('new_password_help');
            const progressBar = document.getElementById('password_strength');
            const strengthText = document.getElementById('strength_level');

            if (password.length === 0) {
                progressBar.style.width = '0%';
                progressBar.className = 'progress-bar';
                strengthText.textContent = 'Not entered';
                this.classList.remove('is-invalid', 'is-valid');
                helpText.classList.remove('text-danger', 'text-success');
                helpText.innerHTML = '<i class="fas fa-info-circle"></i> Password must be at least 8 characters long.';
                return;
            }

            const result = checkPasswordStrength(password);
            progressBar.style.width = result.strength + '%';
            progressBar.className = 'progress-bar ' + result.color;
            strengthText.textContent = result.level;

            if (password.length < 8) {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
                helpText.classList.add('text-danger');
                helpText.classList.remove('text-success');
                helpText.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Password must be at least 8 characters long.';
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                helpText.classList.remove('text-danger');
                helpText.classList.add('text-success');
                helpText.innerHTML = '<i class="fas fa-check"></i> Password length is acceptable.';
            }

            // Check confirm password match
            checkPasswordMatch();
        });

        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const confirmField = document.getElementById('confirm_password');
            const confirmHelp = document.getElementById('confirm_password_help');

            if (confirmPassword.length === 0) {
                confirmField.classList.remove('is-invalid', 'is-valid');
                confirmHelp.classList.remove('text-danger', 'text-success');
                confirmHelp.innerHTML = '<i class="fas fa-info-circle"></i> Re-enter your new password to confirm.';
                return;
            }

            if (newPassword === confirmPassword) {
                confirmField.classList.remove('is-invalid');
                confirmField.classList.add('is-valid');
                confirmHelp.classList.remove('text-danger');
                confirmHelp.classList.add('text-success');
                confirmHelp.innerHTML = '<i class="fas fa-check"></i> Passwords match.';
            } else {
                confirmField.classList.add('is-invalid');
                confirmField.classList.remove('is-valid');
                confirmHelp.classList.add('text-danger');
                confirmHelp.classList.remove('text-success');
                confirmHelp.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Passwords do not match.';
            }
        }

        // Form validation
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword.length < 8) {
                e.preventDefault();
                alert('New password must be at least 8 characters long.');
                return;
            }

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }
        });
    </script>
</body>
</html>

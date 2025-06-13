<?php
require_once '../config/database.php';

$message = '';
$error = '';
$step = $_GET['step'] ?? 'request';

// Handle password reset request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'request') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($username) || empty($email)) {
        $error = 'Please enter both username and email.';
    } else {
        try {
            $db = Database::getInstance();
            $user = $db->fetchOne(
                "SELECT * FROM admin_users WHERE username = :username AND email = :email", 
                ['username' => $username, 'email' => $email]
            );
            
            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store reset token (you might want to add a password_resets table, but for simplicity we'll use a session)
                session_start();
                $_SESSION['reset_token'] = $token;
                $_SESSION['reset_user_id'] = $user['id'];
                $_SESSION['reset_expires'] = $expires;
                
                // In production, you'd send this via email. For now, we'll display it.
                $resetLink = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?step=reset&token=' . $token;
                $message = 'Password reset link generated! (In production, this would be emailed to you)';
                $step = 'link';
            } else {
                $error = 'No user found with those credentials.';
            }
        } catch (Exception $e) {
            $error = 'Database error occurred.';
        }
    }
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 'reset') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    session_start();
    
    if (empty($newPassword) || empty($confirmPassword)) {
        $error = 'Please enter and confirm your new password.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif (!isset($_SESSION['reset_token']) || $token !== $_SESSION['reset_token']) {
        $error = 'Invalid reset token.';
    } elseif (time() > strtotime($_SESSION['reset_expires'])) {
        $error = 'Reset token has expired.';
    } else {
        try {
            $db = Database::getInstance();
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $db->update(
                'admin_users',
                ['password' => $hashedPassword],
                'id = :id',
                ['id' => $_SESSION['reset_user_id']]
            );
            
            // Clear reset session data
            unset($_SESSION['reset_token'], $_SESSION['reset_user_id'], $_SESSION['reset_expires']);
            
            $message = 'Password updated successfully! You can now log in with your new password.';
            $step = 'success';
        } catch (Exception $e) {
            $error = 'Error updating password.';
        }
    }
}

// Validate reset token for reset form
if ($step === 'reset') {
    session_start();
    $token = $_GET['token'] ?? '';
    
    if (!isset($_SESSION['reset_token']) || $token !== $_SESSION['reset_token']) {
        $error = 'Invalid or expired reset link.';
        $step = 'request';
    } elseif (time() > strtotime($_SESSION['reset_expires'])) {
        $error = 'Reset link has expired.';
        $step = 'request';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Ted Rubin Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Unicons -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 500px;
            width: 100%;
        }
        
        .reset-header {
            background: linear-gradient(135deg, #2f55d4, #4d6ce8);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .reset-body {
            padding: 2rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #2f55d4;
            box-shadow: 0 0 0 0.2rem rgba(47, 85, 212, 0.25);
        }
        
        .btn-reset {
            background: linear-gradient(135deg, #2f55d4, #4d6ce8);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(47, 85, 212, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .link-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            word-break: break-all;
            font-family: monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="reset-card mx-auto">
                    <div class="reset-header">
                        <h3 class="mb-2">
                            <i class="uil uil-key-skeleton me-2"></i>
                            <?php 
                            switch($step) {
                                case 'request': echo 'Reset Password'; break;
                                case 'link': echo 'Reset Link Generated'; break;
                                case 'reset': echo 'Set New Password'; break;
                                case 'success': echo 'Password Updated'; break;
                            }
                            ?>
                        </h3>
                        <p class="mb-0 opacity-75">Ted Rubin Admin Panel</p>
                    </div>
                    
                    <div class="reset-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i class="uil uil-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($message): ?>
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="uil uil-check-circle me-2"></i>
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($step === 'request'): ?>
                            <form method="POST" action="">
                                <p class="text-muted mb-4">Enter your username and email address to reset your password.</p>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label fw-semibold">Username</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           placeholder="Enter your username"
                                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                           required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email" 
                                           placeholder="Enter your email address"
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                           required>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-reset">
                                        <i class="uil uil-envelope-send me-2"></i>Request Reset Link
                                    </button>
                                </div>
                            </form>

                        <?php elseif ($step === 'link'): ?>
                            <div class="text-center">
                                <i class="uil uil-envelope-check display-4 text-success mb-3"></i>
                                <h5>Reset Link Generated</h5>
                                <p class="text-muted mb-3">In a production environment, this link would be sent to your email. For now, click the link below:</p>
                                
                                <div class="link-box">
                                    <?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&token=' . ($_SESSION['reset_token'] ?? ''); ?>
                                </div>
                                
                                <a href="?step=reset&token=<?php echo $_SESSION['reset_token'] ?? ''; ?>" class="btn btn-primary btn-reset">
                                    <i class="uil uil-arrow-right me-1"></i>Continue to Reset Password
                                </a>
                            </div>

                        <?php elseif ($step === 'reset'): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                                
                                <p class="text-muted mb-4">Enter your new password below.</p>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label fw-semibold">New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="new_password" 
                                           name="new_password" 
                                           placeholder="Enter new password"
                                           minlength="6"
                                           required>
                                    <div class="form-text">Password must be at least 6 characters long.</div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label fw-semibold">Confirm New Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Confirm new password"
                                           required>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-reset">
                                        <i class="uil uil-check me-2"></i>Update Password
                                    </button>
                                </div>
                            </form>

                        <?php elseif ($step === 'success'): ?>
                            <div class="text-center">
                                <i class="uil uil-check-circle display-4 text-success mb-3"></i>
                                <h5>Password Updated Successfully!</h5>
                                <p class="text-muted mb-4">Your password has been changed. You can now log in with your new password.</p>
                                
                                <a href="login.php" class="btn btn-primary btn-reset">
                                    <i class="uil uil-signin me-2"></i>Go to Login
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="text-center mt-4">
                            <a href="login.php" class="text-muted text-decoration-none">
                                <i class="uil uil-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('new_password').value;
            const confirm = this.value;
            
            if (password !== confirm) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
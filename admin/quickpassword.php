<?php
/**
 * Quick Password Reset Utility
 * For emergency admin password resets
 * 
 * SECURITY WARNING: Delete this file after use in production!
 */

// Uncomment the line below to enable this utility
// define('EMERGENCY_RESET_ENABLED', true);

if (!defined('EMERGENCY_RESET_ENABLED')) {
    die('Emergency reset utility is disabled. Edit this file to enable it.');
}

require_once '../config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    
    if (empty($username) || empty($newPassword)) {
        $error = 'Please enter both username and new password.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        try {
            $db = Database::getInstance();
            
            // Check if user exists
            $user = $db->fetchOne(
                "SELECT id FROM admin_users WHERE username = :username", 
                ['username' => $username]
            );
            
            if (!$user) {
                $error = 'User not found.';
            } else {
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $db->update(
                    'admin_users',
                    ['password' => $hashedPassword],
                    'id = :id',
                    ['id' => $user['id']]
                );
                
                $message = "Password updated successfully for user '{$username}'!";
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emergency Password Reset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; padding: 2rem 0; }
        .container { max-width: 500px; }
        .alert-warning { border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">🚨 Emergency Password Reset</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>Security Warning:</strong> This utility should only be used in emergencies. 
                    Delete this file after use in production!
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                <?php else: ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="username" 
                                   name="username" 
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                   required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password" 
                                   name="new_password" 
                                   minlength="6"
                                   required>
                            <div class="form-text">Minimum 6 characters</div>
                        </div>
                        
                        <button type="submit" class="btn btn-danger">Reset Password</button>
                        <a href="login.php" class="btn btn-secondary ms-2">Cancel</a>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-muted">Delete this file after use for security!</small>
        </div>
    </div>
</body>
</html>
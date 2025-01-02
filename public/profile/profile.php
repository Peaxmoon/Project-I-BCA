<?php
session_start();
require '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validate inputs
    if (strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    // Check if email is already taken by another user
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Email is already taken";
    }
    
    // If changing password
    if (!empty($current_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect";
        }
        
        if (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters long";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
    }
    
    if (empty($errors)) {
        // Update user information
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $hashed_password, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $email, $user_id);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: profile.php");
            exit();
        } else {
            $errors[] = "Error updating profile";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - TableServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
    <div class="page-wrapper">
        <?php include '../../includes/header.php'; ?>

        <div class="content-wrapper">
            <div class="auth-container">
                <a href="../dashboarduser.php" class="back-to-dashboard">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>

                <div class="auth-header">
                    <h1>Edit Profile</h1>
                    <p>Update your account information</p>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <?php 
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <ul class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <li><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <form method="POST" action="" id="profileForm">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="name" name="name" class="form-input" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-input" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>

                    <div class="password-section">
                        <h2 class="section-title">Change Password</h2>
                        <p class="text-muted">Leave blank if you don't want to change your password</p>

                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="current_password" name="current_password" class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-key"></i>
                                <input type="password" id="new_password" name="new_password" class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <div class="input-with-icon">
                                <i class="fas fa-key"></i>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-input">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="register-btn">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <?php include '../../includes/footer.php'; ?>
    </div>

    <script>
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const currentPassword = document.getElementById('current_password').value;

            if (newPassword || confirmPassword || currentPassword) {
                if (newPassword.length < 6) {
                    e.preventDefault();
                    alert('New password must be at least 6 characters long');
                    return;
                }

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New passwords do not match');
                    return;
                }

                if (!currentPassword) {
                    e.preventDefault();
                    alert('Please enter your current password to change password');
                    return;
                }
            }
        });
    </script>
</body>
</html>

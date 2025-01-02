<?php
if (!defined('INCLUDED_FROM_DASHBOARD')) {
    header("Location: ../admindashboard.php?page=profile");
    exit();
}

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM admins WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    
    $update_sql = "UPDATE admins SET name = ?, email = ?";
    $params = array($name, $email);
    $types = "ss";
    
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql .= ", password = ?";
        $params[] = $hashed_password;
        $types .= "s";
    }
    
    $update_sql .= " WHERE id = ?";
    $params[] = $admin_id;
    $types .= "i";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        $_SESSION['admin_name'] = $name;
        $success_message = "Profile updated successfully!";
        // Refresh admin data
        $result = $conn->query("SELECT * FROM admins WHERE id = $admin_id");
        $admin = $result->fetch_assoc();
    } else {
        $error_message = "Error updating profile.";
    }
}
?>

<div class="admin-profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="profile-title">
            <h2>Admin Profile</h2>
            <p>Manage your account settings</p>
        </div>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="admin-message success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="admin-message error">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="profile-card">
        <form method="POST" class="profile-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-with-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="new_password">New Password (leave blank to keep current)</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="new_password" name="new_password" minlength="6">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" name="update_profile" class="admin-btn">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <div class="profile-card">
        <h3>Account Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Account Type</span>
                <span class="info-value">Administrator</span>
            </div>
            <div class="info-item">
                <span class="info-label">Last Login</span>
                <span class="info-value"><?php echo date('Y-m-d H:i:s'); ?></span>
            </div>
        </div>
    </div>
</div>

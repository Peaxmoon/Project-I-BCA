<?php
session_start();  // Start the session

// Check if the user is logged in by verifying if `user_id` exists in the session
if (!isset($_SESSION['user_id'])) {
    // If no `user_id` is found, redirect to the login page
    header("Location: /Project-I-BCA/public/profile/login.php");
    exit();  // Ensure no further code is executed
}
if (!isset($_COOKIE['table_number'])) {
    header("Location: /Project-I-BCA/scantable.php");
    exit();
}

// If the user is logged in, display the dashboard
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - TableServe</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Project-I-BCA/public/assets/css/style.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="main-content">
        <div class="dashboard-container">
            <section class="info-section">
                <h2>Account Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <p class="info-label">Name:</p>
                        <p class="info-value"><?= htmlspecialchars($_SESSION['user_name']); ?></p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">User ID:</p>
                        <p class="info-value"><?= htmlspecialchars($_SESSION['user_id']); ?></p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Table Number:</p>
                        <p class="info-value">
                            <?php
                            if (isset($_COOKIE['table_number'])) {
                                echo htmlspecialchars($_COOKIE['table_number']);
                            } else {
                                echo "<span class='not-assigned'>Not assigned yet</span>";
                            }
                            ?>
                        </p>
                    </div>
                </div>
            </section>
            
            <section class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-cards">
                    <a href="./profile/profile.php" class="action-card">
                        <i class="fas fa-user"></i>
                        <h3>Edit Profile</h3>
                    </a>
                    <a href="./orders/orders.php" class="action-card">
                        <i class="fas fa-utensils"></i>
                        <h3>View Orders</h3>
                    </a>
                    <a href="./profile/settings.php" class="action-card">
                        <i class="fas fa-cog"></i>
                        <h3>Settings</h3>
                    </a>
                </div>
            </section>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script>
</body>

</html>
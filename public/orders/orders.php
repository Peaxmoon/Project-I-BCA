<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';
session_start();

if (!isset($_COOKIE['table_number'])) {
    // Redirect to scantable.php if the cookie is not set
    header("Location: /Project-I-BCA/scantable.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /Project-I-BCA/public/profile/login.php"); // Redirect to login page if not logged in
    exit();
}

// Get user ID
$user_id = $_SESSION['user_id'];

// Fetch the user's orders from the database
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_close($conn); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
    <link rel="stylesheet" href="/Project-I-BCA/assets/css/style.css">
</head>
<body>  
    <?php include '../../includes/header.php'; ?>
    <div class="container">
        <h1 class="page-title">Your Orders</h1>
        <div class="orders-list">
            <?php if (count($orders) > 0): ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-item">
                        <h3>Order #<?= htmlspecialchars($order['id']); ?></h3>
                        <p><strong>Date:</strong> <?= htmlspecialchars($order['created_at']); ?></p>
                        <p><strong>Total:</strong> $<?= htmlspecialchars($order['total_price']); ?></p>
                        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']); ?></p>
                        <div class="links">
                            <a href="order_details.php?id=<?= htmlspecialchars($order['id']); ?>" class="btn primary-btn">View Details</a>
                            <a href="cancel_order.php?id=<?= htmlspecialchars($order['id']); ?>" class="btn secondary-btn">Cancel Order</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no orders yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../../includes/footer.php'; ?>
</body>
</html>

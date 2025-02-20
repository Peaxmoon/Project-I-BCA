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
</head>
<body>  
    <?php include '../../includes/header.php'; ?>
    <h1>Your Previous Orders</h1>

    <?php if (count($orders) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['id']); ?></td>
                        <td>₹<?= htmlspecialchars($order['total_price']); ?></td>
                        <td><?= htmlspecialchars($order['status']); ?></td>
                        <td><?= htmlspecialchars($order['payment_status']); ?></td>
                        <td><?= htmlspecialchars($order['created_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no orders yet.</p>
    <?php endif; ?>

    <a href="../menu/menu_items.php">Back to Menu</a>
    <a href="my_receipt.php">Go My receipt</a>
    <a href="my_table_receipt.php">Go Table receipt</a>

    <?php include '../../includes/footer.php'; ?>

</body>
</html>

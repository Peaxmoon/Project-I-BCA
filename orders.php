<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <link rel="stylesheet" href="/Project-I-BCA/assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <h1 class="page-title">Your Orders</h1>
        <div class="orders-list">
            <?php
            // Fetch orders from the database
            // ...existing code to fetch orders...

            // Loop through orders and display them
            foreach ($orders as $order) {
                echo '<div class="order-item">';
                echo '<h3>Order #' . htmlspecialchars($order['id']) . '</h3>';
                echo '<p><strong>Date:</strong> ' . htmlspecialchars($order['date']) . '</p>';
                echo '<p><strong>Total:</strong> $' . htmlspecialchars($order['total']) . '</p>';
                echo '<p><strong>Status:</strong> ' . htmlspecialchars($order['status']) . '</p>';
                echo '<p><strong>Payment Status:</strong> ' . htmlspecialchars($order['payment_status']) . '</p>';
                echo '<div class="links">';
                echo '<a href="order_details.php?id=' . htmlspecialchars($order['id']) . '" class="btn primary-btn">View Details</a>';
                echo '<a href="cancel_order.php?id=' . htmlspecialchars($order['id']) . '" class="btn secondary-btn">Cancel Order</a>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>

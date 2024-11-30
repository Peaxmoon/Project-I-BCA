<?php
// Include the database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if user is not logged in
    header("Location: login.php");
    exit();
}

// Check if an order ID is passed
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']); // Sanitize the input
    $user_id = $_SESSION['user_id']; // Retrieve the logged-in user's ID

    // Fetch order details, ensuring the order belongs to the logged-in user
    $order_stmt = $conn->prepare("SELECT o.id, o.total_price, o.status, u.name AS user_name 
                                  FROM orders o 
                                  JOIN users u ON o.user_id = u.id 
                                  WHERE o.id = ? AND o.user_id = ?");
    $order_stmt->bind_param("ii", $order_id, $user_id);
    $order_stmt->execute();
    $order_result = $order_stmt->get_result();

    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();

        // Fetch the items in the order
        $order_items_stmt = $conn->prepare("SELECT oi.quantity, mi.name, mi.price 
                                            FROM order_items oi 
                                            JOIN menu_items mi ON oi.menu_item_id = mi.id 
                                            WHERE oi.order_id = ?");
        $order_items_stmt->bind_param("i", $order_id);
        $order_items_stmt->execute();
        $order_items_result = $order_items_stmt->get_result();

        echo "<h1>Order Confirmation</h1>";
        echo "<p>Order ID: " . htmlspecialchars($order['id']) . "</p>";
        echo "<p>User: " . htmlspecialchars($order['user_name']) . "</p>";
        echo "<p>Status: " . htmlspecialchars($order['status']) . "</p>";
        echo "<p>Total Price: Rs. " . htmlspecialchars($order['total_price']) . "</p>";

        echo "<h3>Items Ordered</h3>";
        echo "<ul>";
        while ($item = $order_items_result->fetch_assoc()) {
            echo "<li>" . $item['quantity'] . " x " . htmlspecialchars($item['name']) . " (Rs. " . htmlspecialchars($item['price']) . " each)</li>";
        }
        echo "</ul>";

        // Add a "Go Back" button
        echo "<a href='../menu/menu_items.php'><button>Go Back to Menu</button></a>";
    } else {
        echo "<h1>Error</h1>";
        echo "<p>Order not found or does not belong to you!</p>";
        echo "<a href='../menu/menu_items.php'><button>Go Back to Menu</button></a>";
    }
} else {
    echo "<h1>Error</h1>";
    echo "<p>No order ID provided!</p>";
    echo "<a href='../menu/menu_items.php'><button>Go Back to Menu</button></a>";
}

$conn->close();
?>

<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';
session_start();

if (!isset($_COOKIE['table_number'])) {
    header("Location: /Project-I-BCA/scantable.php");
    exit();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: /Project-I-BCA/public/profile/login.php"); // Redirect to login page if not logged in
    exit();
}
// Validate POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);
    $user_id = $_SESSION['user_id'] ?? 0; // Fetch user ID from session
    $table_id = $_COOKIE['table_number'] ?? 0; // Fetch table number from cookies

    // Ensure quantity is valid
    if ($quantity < 1) {
        die("Invalid quantity!");
    }

    // Fetch menu item details
    $menu_item_sql = "SELECT price FROM menu_items WHERE id = $item_id";
    $menu_item_result = $conn->query($menu_item_sql);

    if ($menu_item_result->num_rows > 0) {
        $menu_item = $menu_item_result->fetch_assoc();
        $total_price = $menu_item['price'] * $quantity; // Calculate total price

        // Insert into `orders` table
        $order_sql = "INSERT INTO orders (user_id, table_id, total_price, status) 
                      VALUES ($user_id, $table_id, $total_price, 'pending')";
        if ($conn->query($order_sql)) {
            $order_id = $conn->insert_id; // Get the last inserted order ID

            // Insert into `order_items` table
            $order_item_sql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) 
                               VALUES ($order_id, $item_id, $quantity, {$menu_item['price']})";

            if ($conn->query($order_item_sql)) {
                // Redirect to order confirmation
                header("Location: /Project-I-BCA/public/orders/order_confirmation.php?order_id=$order_id");
                exit();
            } else {
                echo "Error adding item to order: " . $conn->error;
            }
        } else {
            echo "Error creating order: " . $conn->error;
        }
    } else {
        echo "Item not found!";
    }
} else {
    echo "Invalid request!";
}

$conn->close();
?>

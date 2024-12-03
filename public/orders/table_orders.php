<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_COOKIE['table_number'])) {
    header("Location: /Project-I-BCA/scantable.php");
    exit();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: /Project-I-BCA/public/profile/login.php"); // Redirect to login page if not logged in
    exit();
}
// Get table ID from query string
$table_id = isset($_GET['table_id']) ? intval($_GET['table_id']) : 0;

if ($table_id > 0) {
    // Fetch all orders for the given table ID
    $sql = "SELECT o.id AS order_id, o.total_price, o.status, o.created_at, 
            oi.menu_item_id, oi.quantity, oi.price 
            FROM orders o 
            JOIN order_items oi ON o.id = oi.order_id 
            WHERE o.table_id = $table_id 
            ORDER BY o.created_at DESC";

    $result = $conn->query($sql);

    // Organize data into an array
    $orders = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $order_id = $row['order_id'];
            if (!isset($orders[$order_id])) {
                $orders[$order_id] = [
                    'total_price' => $row['total_price'],
                    'status' => $row['status'],
                    'created_at' => $row['created_at'],
                    'items' => [],
                ];
            }
            $orders[$order_id]['items'][] = [
                'menu_item_id' => $row['menu_item_id'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
            ];
        }
    } else {
        echo "No orders found for this table.";
    }
}
$conn->close();
?>

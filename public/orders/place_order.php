<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error'] = "Your cart is empty";
    header("Location: ../menu/menu_items.php");
    exit();
}

if (!isset($_COOKIE['table_number'])) {
    $_SESSION['error'] = "Please select a table first";
    header("Location: order_confirmation.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$table_id = $_COOKIE['table_number'];
$total_amount = array_sum(array_column($_SESSION['cart'], 'total'));

// Start transaction
$conn->begin_transaction();

try {
    // Insert main order with pending status
    $stmt = $conn->prepare("INSERT INTO orders (user_id, table_id, total_price, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iid", $user_id, $table_id, $total_amount);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    foreach ($_SESSION['cart'] as $item) {
        $stmt->bind_param("iiid", $order_id, $item['item_id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    $conn->commit();
    
    // Clear cart after successful order
    unset($_SESSION['cart']);
    
    $_SESSION['success'] = "Order placed successfully! Your order number is #" . $order_id;
    header("Location: ../menu/menu_items.php");
    exit();
    
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error placing order: " . $e->getMessage();
    header("Location: order_confirmation.php");
    exit();
}
?> 
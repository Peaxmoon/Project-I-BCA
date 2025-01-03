<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error'] = "No items in cart";
    header("Location: ../menu/menu_items.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$table_id = $_COOKIE['table_number'];
$total_amount = array_sum(array_column($_SESSION['cart'], 'total'));

// Start transaction
$conn->begin_transaction();

try {
    // Insert main order
    $order_sql = "INSERT INTO orders (user_id, table_id, total_price, status) 
                  VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("iid", $user_id, $table_id, $total_amount);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert order items
    $item_sql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($item_sql);
    
    foreach ($_SESSION['cart'] as $item) {
        // Check if menu_item_id exists in menu_items table
        $check_sql = "SELECT id FROM menu_items WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $item['item_id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows === 0) {
            throw new Exception("Menu item ID " . $item['item_id'] . " does not exist.");
        }

        // Log the menu_item_id for debugging
        error_log("Inserting item with menu_item_id: " . $item['item_id']);
        
        $stmt->bind_param("iiid", $order_id, $item['item_id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();
    
    // Clear cart
    unset($_SESSION['cart']);
    
    // Set success message
    $_SESSION['success'] = "Order placed successfully!";
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    $_SESSION['error'] = "Error placing order: " . $e->getMessage();
    
    // Log the error message for debugging
    error_log("Order placement error: " . $e->getMessage());
    
    header("Location: order_confirmation.php");
    exit();
}
?>
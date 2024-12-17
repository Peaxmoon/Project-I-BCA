<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $order_id = $_POST['order_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        $sql = "SELECT oi.id, mi.name AS food_name, oi.quantity, oi.price, 
        (oi.quantity * oi.price) AS total_price, o.table_id, 
        o.payment_status, o.id as order_id
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE o.user_id = ? AND DATE(o.created_at) = ?
        GROUP BY o.id";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $order_id, $user_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to update payment status");
        }

        // Log the revenue
        $log_sql = "INSERT INTO revenue_logs (order_id, amount, payment_method) VALUES (?, ?, 'online')";
        $stmt = $conn->prepare($log_sql);
        $stmt->bind_param("id", $order_id, $amount);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to log revenue");
        }

        $conn->commit();
        
        $_SESSION['success'] = "Payment processed successfully!";
        header("Location: my_receipt.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Payment failed: " . $e->getMessage();
        header("Location: my_receipt.php");
        exit();
    }
}
?> 
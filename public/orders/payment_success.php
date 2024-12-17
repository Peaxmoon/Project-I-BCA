<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if (!isset($_GET['pidx']) || !isset($_SESSION['pending_payment'])) {
    header('Location: my_table_receipt.php');
    exit();
}

$pidx = $_GET['pidx'];
$payment_details = $_SESSION['pending_payment'];

try {
    // Start transaction
    $conn->begin_transaction();

    // Update order payment status for the specific order
    $update_sql = "UPDATE orders SET payment_status = 'paid' 
                   WHERE id = ? AND table_id = ? AND DATE(created_at) = CURRENT_DATE";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $payment_details['order_id'], $payment_details['table_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update order payment status");
    }

    // Update all order_items payment status for this table
    $update_items_sql = "UPDATE order_items oi 
                        JOIN orders o ON oi.order_id = o.id 
                        SET oi.payment_status = 'paid' 
                        WHERE o.table_id = ? AND DATE(o.created_at) = CURRENT_DATE";
    $stmt = $conn->prepare($update_items_sql);
    $stmt->bind_param("i", $payment_details['table_id']);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update order items payment status");
    }

    // Get the actual amount from orders table
    $amount_sql = "SELECT total_price FROM orders WHERE id = ?";
    $stmt = $conn->prepare($amount_sql);
    $stmt->bind_param("i", $payment_details['order_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    // Log the payment in revenue_logs
    $log_sql = "INSERT INTO revenue_logs (order_id, amount, payment_method, transaction_date) 
                VALUES (?, ?, 'khalti', NOW())";
    $stmt = $conn->prepare($log_sql);
    $actual_amount = $order['total_price'];
    $stmt->bind_param("id", $payment_details['order_id'], $actual_amount);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to log revenue");
    }

    $conn->commit();
    unset($_SESSION['pending_payment']);
    
    $_SESSION['success'] = "Payment completed successfully!";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Payment processing failed: " . $e->getMessage();
}

header('Location: my_table_receipt.php');
exit();
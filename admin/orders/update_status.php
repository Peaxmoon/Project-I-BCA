<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    // Validate status
    $valid_statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
    if (!empty($order_id) && in_array($status, $valid_statuses)) {
        try {
            // Start transaction
            $conn->begin_transaction();
            
            // First, check if the order exists and its current status
            $check_order = $conn->prepare("SELECT status, payment_status FROM orders WHERE id = ?");
            $check_order->bind_param("i", $order_id);
            $check_order->execute();
            $order_result = $check_order->get_result();
            $order_data = $order_result->fetch_assoc();
            
            if (!$order_data) {
                throw new Exception("Order not found");
            }
            
            // Don't allow cancellation of paid orders
            if ($status === 'cancelled' && $order_data['payment_status'] === 'paid') {
                throw new Exception("Cannot cancel a paid order");
            }
            
            // Update order status
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $order_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update order status");
            }
            
            $conn->commit();
            echo json_encode([
                'success' => true, 
                'message' => "Order #$order_id status updated to " . ucfirst($status)
            ]);
            
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode([
                'success' => false, 
                'message' => "Error: " . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => "Invalid status value provided"
        ]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
exit();
?>

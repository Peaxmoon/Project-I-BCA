<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    // Validate inputs
    if (!empty($order_id) && in_array($status, ['pending', 'in progress', 'completed', 'cancelled'])) {
        // Update the status in the database
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);

        if ($stmt->execute()) {
            // Redirect back with a success message
            header("Location: orders_management.php?success=Status updated successfully");
        } else {
            // Redirect back with an error message
            header("Location: orders_management.php?error=Failed to update status");
        }
        $stmt->close();
    } else {
        header("Location: orders_management.php?error=Invalid data");
    }
    $conn->close();
} else {
    header("Location: orders_management.php?error=Invalid request");
    exit();
}
?>

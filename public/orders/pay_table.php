<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['table_id']) || empty($_POST['table_id'])) {
        die('Invalid table number');
    }

    $table_id = intval($_POST['table_id']);

    // Mark all orders for the table as paid
    $sql = "UPDATE orders SET payment_status = 'paid' WHERE table_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $table_id);

    if ($stmt->execute()) {
        header("Location: /Project-I-BCA/public/orders/my_table_receipt.php?status=success");
    } else {
        header("Location: /Project-I-BCA/public/orders/my_table_receipt.php?status=error");
    }

    $stmt->close();
}
$conn->close();
?>

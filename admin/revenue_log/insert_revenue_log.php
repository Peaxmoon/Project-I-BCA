<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Assuming the payment amount is passed via POST (after payment processing)
if (isset($_POST['amount']) && isset($_POST['type'])) {
    $amount = $_POST['amount'];  // Amount of revenue
    $type = $_POST['type'];      // Type of revenue (payment, refund, etc.)

    // Prepare the SQL query to insert the revenue log
    $sql = "INSERT INTO revenue_log (amount, type) VALUES (?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ds', $amount, $type); // Bind amount and type
        if ($stmt->execute()) {
            echo "Revenue logged successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

    // Close the statement and connection
    $stmt->close();
}

$conn->close();
?>

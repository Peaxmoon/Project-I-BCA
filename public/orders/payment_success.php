<?php
session_start();
require_once '../../config/database.php';

if (!isset($_GET['pidx']) || !isset($_GET['status'])) {
    $_SESSION['error'] = "Invalid payment response";
    header('Location: my_receipt.php');
    exit();
}

$pidx = $_GET['pidx'];
$status = $_GET['status'];
$order_id = $_GET['purchase_order_id'];

try {
    // Verify payment status with Khalti API
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://a.khalti.com/api/v2/epayment/lookup/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
        CURLOPT_HTTPHEADER => [
            "Authorization: Key 9b845d0c6b5d4dcb81549e5b01e39e2c",
            "Content-Type: application/json"
        ]
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        throw new Exception("Payment verification failed: " . $err);
    }

    $verification = json_decode($response, true);
    
    if ($verification['status'] === 'Completed') {
        // Start transaction
        $conn->begin_transaction();

        try {
            // First, verify if the order exists and get its current status
            $check_order = "SELECT payment_status FROM orders WHERE id = ?";
            $stmt = $conn->prepare($check_order);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Order not found");
            }

            // Update order payment status
            $update_sql = "UPDATE orders 
                          SET payment_status = 'paid', 
                              payment_date = NOW(),
                              payment_ref = ?
                          WHERE id = ? AND payment_status != 'paid'";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("si", $pidx, $order_id);
            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                // If no rows were updated, either the order was already paid or doesn't exist
                throw new Exception("Order already paid or not found");
            }

            // Update order items payment status
            $update_items_sql = "UPDATE order_items 
                                SET payment_status = 'paid',
                                    payment_date = NOW()
                                WHERE order_id = ? AND payment_status != 'paid'";
            $stmt = $conn->prepare($update_items_sql);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();

            // Log the payment in revenue_log
            $log_sql = "INSERT INTO revenue_log 
                       (order_id, payment_ref, amount, status, payment_date, transaction_details) 
                       VALUES (?, ?, ?, 'verified', NOW(), ?)";
            $stmt = $conn->prepare($log_sql);
            $amount = $_GET['amount'] / 100; // Convert from paisa to rupees
            $transaction_details = json_encode(array_merge($_GET, ['verification' => $verification]));
            $stmt->bind_param("isds", $order_id, $pidx, $amount, $transaction_details);
            $stmt->execute();

            $conn->commit();
            $_SESSION['success'] = "Payment successful! Your order has been paid.";
            
            // Debug log
            error_log("Payment processed successfully for order #" . $order_id . " with pidx: " . $pidx);
            
            // Redirect based on payment source
            if (strpos($_GET['purchase_order_name'], 'Table') !== false) {
                header('Location: my_table_receipt.php');
            } else {
                header('Location: my_receipt.php');
            }
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            error_log("Payment processing error: " . $e->getMessage());
            throw $e;
        }
    } else {
        throw new Exception("Payment was not completed. Status: " . $verification['status']);
    }

} catch (Exception $e) {
    error_log("Payment error: " . $e->getMessage());
    $_SESSION['error'] = "Error processing payment: " . $e->getMessage();
    header('Location: my_receipt.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4CAF50;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .message {
            text-align: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="message">
        <div class="loader"></div>
        <p>Processing your payment...</p>
    </div>
</body>
</html> 
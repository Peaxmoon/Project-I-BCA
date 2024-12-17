<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Receive the callback data from Khalti
$callback_data = file_get_contents('php://input');
$data = json_decode($callback_data, true);

// Log the callback data for debugging
$log_file = fopen("khalti_callback.log", "a");
fwrite($log_file, date('Y-m-d H:i:s') . " - Callback received: " . $callback_data . "\n");

try {
    if (isset($data['pidx'])) {
        $pidx = $data['pidx'];
        
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
            throw new Exception("Verification failed: " . $err);
        }

        $verification = json_decode($response, true);
        
        if ($verification['status'] === 'Completed') {
            // Start transaction
            $conn->begin_transaction();

            // Get order details from revenue_log
            $order_sql = "SELECT order_id, amount FROM revenue_log WHERE payment_ref = ?";
            $stmt = $conn->prepare($order_sql);
            $stmt->bind_param("s", $pidx);
            $stmt->execute();
            $result = $stmt->get_result();
            $payment_data = $result->fetch_assoc();

            if ($payment_data) {
                // Update order payment status
                $update_sql = "UPDATE orders SET payment_status = 'paid' WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("i", $payment_data['order_id']);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update payment status");
                }

                // Update revenue_log status
                $log_sql = "UPDATE revenue_log 
                           SET status = 'verified', 
                               verification_date = NOW(),
                               transaction_details = ?
                           WHERE payment_ref = ?";
                $stmt = $conn->prepare($log_sql);
                $transaction_details = json_encode($verification);
                $stmt->bind_param("ss", $transaction_details, $pidx);
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update revenue log");
                }

                $conn->commit();
                fwrite($log_file, date('Y-m-d H:i:s') . " - Payment verified for pidx: " . $pidx . "\n");
                
                http_response_code(200);
                echo json_encode(['status' => 'success']);
            } else {
                throw new Exception("Payment record not found");
            }
        } else {
            throw new Exception("Payment not completed");
        }
    } else {
        throw new Exception("Invalid callback data");
    }
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    fwrite($log_file, date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n");
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

fclose($log_file);
?>

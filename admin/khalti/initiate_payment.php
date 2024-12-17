<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = intval($_POST['amount']); // Amount in paisa (already multiplied by 100)
    $table_id = $_POST['table_id'];
    $order_id = $_POST['order_id'];
    
    // Get user details
    $user_id = $_SESSION['user_id'];
    $user_sql = "SELECT name, email FROM users WHERE id = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    $data = [
        'return_url' => 'http://localhost/Project-I-BCA/public/orders/payment_success.php',
        'website_url' => 'http://localhost/Project-I-BCA',
        'amount' => $amount,
        'purchase_order_id' => $order_id,
        'purchase_order_name' => "Table {$table_id} Order",
        'customer_info' => [
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => '9800000000' // You might want to add phone number to user profile
        ]
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://a.khalti.com/api/v2/epayment/initiate/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            "Authorization: Key 9b845d0c6b5d4dcb81549e5b01e39e2c",
            "Content-Type: application/json"
        ]
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        $_SESSION['error'] = "Payment initialization failed: " . $err;
        header('Location: /Project-I-BCA/public/menu/menu_items.php');
        exit();
    } else {
        $responseData = json_decode($response, true);
        if (isset($responseData['payment_url'])) {
            // Store payment details in session for verification
            $_SESSION['pending_payment'] = [
                'order_id' => $order_id,
                'table_id' => $table_id,
                'amount' => $amount
            ];
            header('Location: ' . $responseData['payment_url']);
        } else {
            $_SESSION['error'] = "Payment initialization failed: " . ($responseData['detail'] ?? 'Unknown error');
            header('Location: /Project-I-BCA/public/orders/my_table_receipt.php');
        }
    }
    exit();
}
?>

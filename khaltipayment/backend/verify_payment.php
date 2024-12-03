<?php
// Simulate manual verification by storing submitted data temporarily
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $amount = $data['amount'];
    $transaction_id = $data['transactionId'];

    // Save this data to a temporary file or database for manual verification
    $file = fopen('payments.txt', 'a');
    fwrite($file, "Amount: $amount, Transaction ID: $transaction_id\n");
    fclose($file);

    echo "Payment received! Please verify manually in Khalti.";
}
?>

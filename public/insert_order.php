<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';


$user_id = $_POST['user_id'];
$table_id = $_POST['table_id'];
$total_price = $_POST['total_price'];
$status = "pending";  // Default status for a new order

$sql = "INSERT INTO orders (user_id, table_id, total_price, status) VALUES ($user_id, $table_id, $total_price, '$status')";

if ($conn->query($sql) === TRUE) {
    echo "Order placed successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

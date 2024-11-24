<?php
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';


$order_id = $_POST['order_id'];
$menu_item_id = $_POST['menu_item_id'];
$quantity = $_POST['quantity'];
$price = $_POST['price'];

$sql = "INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES ($order_id, $menu_item_id, $quantity, $price)";

if ($conn->query($sql) === TRUE) {
    echo "Order item added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

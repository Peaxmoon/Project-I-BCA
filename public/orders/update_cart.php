<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index']) && isset($_POST['quantity'])) {
    $index = intval($_POST['index']);
    $quantity = intval($_POST['quantity']);
    
    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $quantity;
        $_SESSION['cart'][$index]['total'] = $_SESSION['cart'][$index]['price'] * $quantity;
    }
}

header("Location: order_confirmation.php");
exit();
?> 

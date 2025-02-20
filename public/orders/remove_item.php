<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index'])) {
    $index = intval($_POST['index']);
    
    // Check if the index exists in the cart
    if (isset($_SESSION['cart'][$index])) {
        // Remove the item
        array_splice($_SESSION['cart'], $index, 1);
        
        // Set success message
        $_SESSION['success'] = "Item removed from cart successfully!";
    } else {
        $_SESSION['error'] = "Item not found in cart.";
    }
}

// Redirect back to cart
header("Location: order_confirmation.php");
exit(); 
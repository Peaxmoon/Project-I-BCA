<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index'])) {
    $index = intval($_POST['index']);
    
    if (isset($_SESSION['cart'][$index])) {
        array_splice($_SESSION['cart'], $index, 1);
    }
    
    if (empty($_SESSION['cart'])) {
        header("Location: ../menu/menu_items.php");
        exit();
    }
}

header("Location: order_confirmation.php");
exit();
?> 

<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit();
}

if (!isset($_COOKIE['table_number'])) {
    echo json_encode(['success' => false, 'message' => 'Please select a table first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($item_id && $name && $price) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if item already exists in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['item_id'] == $item_id) { // Changed from === to == for type coercion
                $cart_item['quantity'] += $quantity;
                $cart_item['total'] = $cart_item['price'] * $cart_item['quantity'];
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['cart'][] = [
                'item_id' => $item_id,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'total' => $price * $quantity
            ];
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Item added to cart successfully!',
            'cart_count' => count($_SESSION['cart'])
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid item data'
        ]);
        exit();
    }
}

echo json_encode([
    'success' => false,
    'message' => 'Invalid request'
]);
exit();
?>

<?php
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';
session_start();

if (!isset($_COOKIE['table_number'])) {
    echo json_encode(['success' => false, 'message' => 'Table not selected']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = intval($_POST['item_id']);
    $quantity = intval($_POST['quantity']);
    
    $menu_item_sql = "SELECT * FROM menu_items WHERE id = ?";
    $stmt = $conn->prepare($menu_item_sql);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $menu_item_result = $stmt->get_result();
    
    if ($menu_item_result->num_rows > 0) {
        $menu_item = $menu_item_result->fetch_assoc();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if item already exists in cart
        $item_exists = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['item_id'] === $item_id) {
                $cart_item['quantity'] += $quantity;
                $cart_item['total'] = $cart_item['price'] * $cart_item['quantity'];
                $item_exists = true;
                break;
            }
        }
        
        // If item doesn't exist, add it
        if (!$item_exists) {
            $_SESSION['cart'][] = [
                'item_id' => $item_id,
                'name' => $menu_item['name'],
                'quantity' => $quantity,
                'price' => $menu_item['price'],
                'total' => $menu_item['price'] * $quantity
            ];
        }
        
        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart']),
            'message' => 'Item added to cart successfully'
        ]);
        exit();
    }
}

echo json_encode([
    'success' => false,
    'message' => 'Failed to add item to cart'
]);
exit();
?>

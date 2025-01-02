<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit();
}

// Get POST data
$item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

// Validate inputs
if ($item_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item or quantity']);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    
    // Start transaction
    $conn->begin_transaction();

    // Check if item exists in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND menu_item_id = ?");
    $stmt->bind_param("ii", $user_id, $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing cart item
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity;
        
        $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $new_quantity, $cart_item['id']);
    } else {
        // Insert new cart item
        $stmt = $conn->prepare("INSERT INTO cart_items (user_id, menu_item_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $item_id, $quantity);
    }

    $stmt->execute();

    // Get total cart items count
    $stmt = $conn->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_count = $stmt->get_result()->fetch_assoc()['count'];

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Item added to cart successfully',
        'cartCount' => $cart_count
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error adding item to cart: ' . $e->getMessage()
    ]);
} 
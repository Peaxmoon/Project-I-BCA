<?php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to rate']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$menu_item_id = isset($_POST['menu_item_id']) ? (int)$_POST['menu_item_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

// Validate inputs
if ($menu_item_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit();
}

try {
    // Check if user has already rated this item
    $stmt = $conn->prepare("SELECT id FROM food_ratings WHERE user_id = ? AND menu_item_id = ?");
    $stmt->bind_param("ii", $user_id, $menu_item_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        // Update existing rating
        $stmt = $conn->prepare("UPDATE food_ratings SET rating = ?, comment = ?, created_at = CURRENT_TIMESTAMP WHERE user_id = ? AND menu_item_id = ?");
        $stmt->bind_param("isii", $rating, $comment, $user_id, $menu_item_id);
    } else {
        // Insert new rating
        $stmt = $conn->prepare("INSERT INTO food_ratings (user_id, menu_item_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $menu_item_id, $rating, $comment);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Error saving rating');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} 
<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /Project-I-BCA/admin/admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = intval($_POST['item_id']);
    $name = $_POST['name'];
    $category_id = intval($_POST['category_id']);
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $image = $_FILES['image'];

    // Handle image upload if a new image is provided
    if ($image['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($image['name']);
        $image_path = "../../assets/images/" . $image_name;
        move_uploaded_file($image['tmp_name'], $image_path);
    } else {
        // If no new image is provided, keep the existing image
        $stmt = $conn->prepare("SELECT image FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();
        $image_name = $item['image'];
    }

    // Update menu item in the database
    $stmt = $conn->prepare("UPDATE menu_items SET name = ?, category_id = ?, description = ?, price = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sisisi", $name, $category_id, $description, $price, $image_name, $item_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Menu item updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating menu item.";
    }

    header("Location: menu_list.php");
    exit();
} else {
    header("Location: menu_list.php");
    exit();
}
?>

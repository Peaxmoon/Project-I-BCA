<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: /Project-I-BCA/admin/admin_login.php");
    exit();
}

$message = '';

if (isset($_GET['item_id'])) {
    $item_id = intval($_GET['item_id']);

    // Fetch the item to get its image filename (if applicable)
    $fetch_sql = "SELECT image FROM menu_items WHERE id = $item_id";
    $fetch_result = $conn->query($fetch_sql);

    if ($fetch_result->num_rows > 0) {
        $item = $fetch_result->fetch_assoc();
        $image = $item['image'];

        // Delete the menu item from the database
        $delete_sql = "DELETE FROM menu_items WHERE id = $item_id";

        if ($conn->query($delete_sql) === TRUE) {
            // Remove the image file from the server if it exists
            if (!empty($image) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/Project-I-BCA/assets/images/" . $image)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . "/Project-I-BCA/assets/images/" . $image);
            }
            $message = "Menu item deleted successfully!";
        } else {
            $message = "Error deleting item: " . $conn->error;
        }
    } else {
        $message = "Menu item not found!";
    }
} else {
    $message = "No menu item specified!";
}

// Redirect back to the menu list with a message
header("Location: menu_list.php?message=" . urlencode($message));
exit();
?>

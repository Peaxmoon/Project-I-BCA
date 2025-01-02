<?php
session_start();
require '../../config/database.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Get the current hashed password from the database
    $sql = "SELECT password FROM users WHERE id = '$user_id'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    // Verify the old password
    if ($old_password === $user['password']) {
        // Update the password in the database
        $sql = "UPDATE users SET password = '$new_password' WHERE id = '$user_id'";
        if (mysqli_query($conn, $sql)) {
            echo "Password updated successfully!";
        } else {
            echo "Error updating password: " . mysqli_error($conn);
        }
    } else {
        echo "The old password you entered is incorrect.";
    }
}

mysqli_close($conn); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
</head>
<body>
    <h1>Change Password</h1>
    <form action="admin_settings.php" method="POST">
        <label for="old_password">Old Password:</label>
        <input type="password" id="old_password" name="old_password" required>
        <br>

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <br>

        <button type="submit">Update Password</button>
    </form>
    <a href="../dashboarduser.php">Back to Dashboard</a>
</body>
</html>

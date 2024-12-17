<?php
session_start();
require '../../config/database.php';// Include database connection

if (!isset($_COOKIE['table_number'])) {
    header("Location: /Project-I-BCA/scantable.php");
    exit();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: /Project-I-BCA/public/profile/login.php"); // Redirect to login page if not logged in
    exit();
}

// Get the user's current information from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Handle form submission for profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $name;
        $_SESSION['success'] = "Profile updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating profile: " . $conn->error;
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

mysqli_close($conn); // Close the database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 4px;
            color: white;
            z-index: 1000;
            animation: slideIn 0.5s ease-out;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            min-width: 200px;
            text-align: center;
        }
        
        .notification.success {
            background-color: #4CAF50;
        }
        
        .notification.error {
            background-color: #f44336;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <h1>Edit Profile</h1>
    <form action="profile.php" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
        <br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        <br>

        <button type="submit">Update Profile</button>
    </form>
    <a href="../dashboarduser.php">Back to Dashboard</a>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach(notification => {
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.5s ease-in forwards';
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        });
    });
    </script>
</body>
</html>

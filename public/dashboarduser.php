<?php
session_start();  // Start the session

// Check if the user is logged in by verifying if `user_id` exists in the session
if (!isset($_SESSION['user_id'])) {
    // If no `user_id` is found, redirect to the login page
    header("Location: login.php");
    exit();  // Ensure no further code is executed
}

// If the user is logged in, display the dashboard
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="/public/assets/style.css"> <!-- Optional CSS file for styling -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
        }

        main {
            padding: 20px;
        }

        section {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #333;
            color: #fff;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>

<body>
    <header>
        <h1>Welcome to Your Dashboard, <?= htmlspecialchars($_SESSION['user_name']); ?>!</h1> <!-- Safely output user data -->
        <nav>
            <ul>
                <li><a href="dashboarduser.php">Dashboard</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="orders.php">Your Orders</a></li>
                <li><a href="logout.php">Logout</a></li> <!-- Logout link -->
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Account Information</h2>
            <p>Name: <?= htmlspecialchars($_SESSION['user_name']); ?></p>
            <p>User ID: <?= htmlspecialchars($_SESSION['user_id']); ?></p>
            <!-- You can add more user-specific information here -->
        </section>

        <section>
            <h2>Quick Actions</h2>
            <ul>
                <li><a href="profile.php">Edit Profile</a></li>
                <li><a href="orders.php">View Your Orders</a></li>
                <li><a href="settings.php">Account Settings</a></li>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 YourWebsite. All rights reserved.</p>
    </footer>
</body>

</html>
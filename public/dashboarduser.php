<?php
session_start();  // Start the session

// Check if the user is logged in by verifying if `user_id` exists in the session
if (!isset($_SESSION['user_id'])) {
    // If no `user_id` is found, redirect to the login page
    header("Location: /Project-I-BCA/public/profile/login.php");
    exit();  // Ensure no further code is executed
}
if (!isset($_COOKIE['table_number'])) {
    header("Location: /Project-I-BCA/scantable.php");
    exit();
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
                <li><a href="../homepage.php">Home Page</a></li>
                <li><a href="./profile/profile.php">Edit Profile</a></li>
                <li><a href="./orders/orders.php">Your Orders</a></li>
                <li><a href="./menu/menu_items.php">View Menu</a></li>
                <li><a href="./profile/logout.php">Logout</a></li> <!-- Logout link -->
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Account Information</h2>
            <p>Name: <?= htmlspecialchars($_SESSION['user_name']); ?></p>
            <p>User ID: <?= htmlspecialchars($_SESSION['user_id']); ?></p>
            <h1>Your table number is ,
                <?php
                // Check if the 'table_number' cookie is set
                if (isset($_COOKIE['table_number'])) {
                    // Display the table number
                    echo htmlspecialchars($_COOKIE['table_number']);
                } else {
                    echo "not assigned yet.";
                }
                ?>
            </h1>
            <!-- You can add more user-specific information here -->
        </section>

        <section>
            <h2>Quick Actions</h2>
            <ul>
                <li><a href="./profile/profile.php">Edit Profile</a></li>
                <li><a href="./orders/orders.php">View Your Orders</a></li>
                <li><a href="./profile/settings.php">Account Settings</a></li>
            </ul>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 YourWebsite. All rights reserved.</p>
    </footer>
</body>

</html>
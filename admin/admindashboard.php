<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

session_start();  // Start the session

// Check if the user is logged in by verifying if `admin_id` exists in the session
if (!isset($_SESSION['admin_id'])) {
    // If no `admin_id` is found in the session, redirect to the login page
    header("Location: admin_login.php"); 
    exit();  // Ensure no further code is executed
}

// If the user is logged in, display the dashboard content
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/public/assets/style.css"> <!-- Optional CSS -->
    
</head>
<body>
<header>
        <h1>Welcome to Your Admin Dashboard, <?= htmlspecialchars($_SESSION['admin_name']); ?>!</h1> <!-- Safely output user data -->
        <nav>
            <ul>
                <li><a href="admindashboard.php">Dashboard</a></li>
                <li><a href="./revenue_log/view_revenue.php">Revenue Report</a></li>
                <li><a href="./profile/adminprofile.php">Profile</a></li>
                <li><a href="admin_logout.php">Logout</a></li> <!-- Logout link -->
                <!-- <li><a href="insert_newadmin.php">Create admin</a></li> register link -->
            </ul>
        </nav>
    </header>

    <nav>
        <ul>
            <li><a href="admindashboard.php?page=menu">Manage Menu</a></li>
            <li><a href="admindashboard.php?page=users">Manage Users</a></li>
            <li><a href="admindashboard.php?page=orders">Manage Orders</a></li>
            <li><a href="admindashboard.php?page=tables">Manage Tables</a></li>
        </ul>
    </nav>

    <main>
        <?php
        // Dynamically include pages based on the selected option
        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            if ($page === 'tables') {
                // Handle table management actions
                if (isset($_GET['action'])) {
                    $action = $_GET['action'];

                    if ($action === 'add') {
                        include './tables/insert_table.php'; // Add new table
                    } elseif ($action === 'update' && isset($_GET['id'])) {
                        include './tables/update_table.php'; // Update existing table
                    } elseif ($action === 'delete' && isset($_GET['id'])) {
                        include './tables/delete_table.php'; // Delete table
                    } else {
                        echo "Invalid action!";
                    }
                } else {
                    include './tables/table_list.php'; // Display table list by default
                }
            } elseif ($page === 'menu') {
                include './menu/menu_list.php'; // Manage Menu page
            } elseif ($page === 'users') {
                include './users/user_list.php'; // Manage Users page
            } elseif ($page === 'orders') {
                include './orders/orders_list.php'; // Manage Orders page
            } else {
                echo "Page not found!";
            }
        } else {
            echo "Welcome to the Dashboard!";
        }
        ?>
    </main>

    <footer>
        <p>&copy; 2024 TableServe Resturant</p>
    </footer>
</body>
</html>

<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/public/assets/style.css"> <!-- Optional CSS -->
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
            color: blue;
            text-decoration: none;
        }

        main {
            padding: 20px;
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
        <h1>Admin Dashboard</h1>
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
        <p>&copy; 2024 Your Project</p>
    </footer>
</body>
</html>

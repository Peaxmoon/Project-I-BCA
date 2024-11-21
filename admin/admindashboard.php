<?php
// Example config file for including database connection
include '../config/database.php';


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>

<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php?page=menu">Manage Menu</a></li>
                <li><a href="dashboard.php?page=users">Manage Users</a></li>
                <li><a href="dashboard.php?page=orders">Manage Orders</a></li>
            </ul>
        </nav>
    </header>


    <main>
        <?php
        // Dynamically include pages based on the selected option
        if (isset($_GET['page'])) {
            $page = $_GET['page'];

            if ($page == 'menu') {
                include './menu/insert_menu_item.php'; // Include the "Manage Menu" page
            } elseif ($page == 'users') {
                include './users/user_list.php'; // Include the "Manage Users" page
            } elseif ($page == 'orders') {
                include './orders/orders_list.php'; // Include the "Manage Orders" page
            } else {
                echo "Page not found!";
            }
        } else {
            echo "Welcome to the Dashboard!";
        }
        ?>


        <nav>
            <ul>
                <li><a href="./menu/insert_menu_item.php">insertOrders</a></li>
                <li><a href="./menu/insert_menu_item.php">Manage Orders</a></li>
                <li><a href="./menu/insert_menu_item.php">Manage Orders</a></li>
                <li><a href="./orders/orders_list.php">Manage orders</a></li>
                <li><a href="./orders/orders_list.php">Manage Orders</a></li>
                <li><a href="./orders/orders_list.php">Manage Orders</a></li>
                <li><a href="./tables/table_list.php">Manage tables</a></li>
                <li><a href="./users/user_list.php">Manage users</a></li>
            </ul>
        </nav>
    </main>
</body>

</html>
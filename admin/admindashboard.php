<?php
// Example config file for including database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';



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
            color: blue;
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
    


    <section>
        <h2>Quick Actions</h2>
        <ul>
            <li><a href="profile.php">Edit Profile</a></li>
            <li><a href="orders.php">View Your Orders</a></li>
            <li><a href="settings.php">Account Settings</a></li>
        </ul>
    </section>




    <h1>Admin Dashboard new versoin</h1>
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

            if ($page == 'menu') {
                include './menu/insert_menu_item.php'; // Include the "Manage Menu" page
            } elseif ($page == 'users') {
                include './users/user_list.php'; // Include the "Manage Users" page
            } elseif ($page == 'orders') {
                include './orders/orders_list.php'; // Include the "Manage Orders" page
            } elseif ($page == 'users') {
                include '../tables/table_list.php'; // Include the "Manage Tables"page
            } else {
                echo "Page not found!";
            }
        } else {
            echo "Welcome to the Dashboard!";
        }
        ?>

    </main>
</body>

</html>
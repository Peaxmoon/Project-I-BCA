<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TableServe Restaurant</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>TableServe Restaurant</h1>
        <div class="topnav">

            <a href="./public/menu/menu_items.php">Menu</a>
            <a href="./public/orders/orders.php">Orders</a>
            <a href="./public/dashboarduser.php">Dashboard</a>
            <a href="./public/orders/receipt.php">Receipt</a>

            <a href="./public/profile/login.php">Login</a>
            <a href="./public/profile/register.php">Register</a>
            <a href="./public/profile/logout.php">Logout</a>
        </div>
    </header>


<!-- <?include '/includes/header.php'; ?> -->



<h1>Here it will be menu i think</h1>


<?php include_once './public/menu/menu_items.php'; ?>
<br>
<br>
<br>
<br>
<br>

    <?include './includes/footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>


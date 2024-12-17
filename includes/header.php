<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Project-I-BCA/public/assets/css/style.css">
</head>
<body>
<header class="main-header">
    <div class="container">
        <div class="logo">
            <a href="/Project-I-BCA/homepage.php">
                <img src="/Project-I-BCA/public/assets/images/tableservetransparentmid.png" alt="TableServe Restaurant">
            </a>
        </div>
        <nav class="main-nav">
            <ul class="nav-links">
                <li><a href="/Project-I-BCA/homepage.php">Home</a></li>
                <li><a href="/Project-I-BCA/public/menu/menu_items.php">Menu</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">
                            <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?>
                            <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/Project-I-BCA/public/dashboarduser.php">Dashboard</a></li>
                            <li><a href="/Project-I-BCA/public/orders/order_confirmation.php">View Cart</a></li>
                            <li><a href="/Project-I-BCA/public/orders/orders.php">My Orders</a></li>
                            <li><a href="/Project-I-BCA/public/profile/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/Project-I-BCA/public/profile/login.php">Login</a></li>
                    <li><a href="/Project-I-BCA/public/profile/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<style>
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle {
    padding-right: 20px;
    position: relative;
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff4444;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
}

.dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    min-width: 160px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    border-radius: 4px;
    z-index: 1000;
}

.dropdown-menu li {
    display: block;
}

.dropdown-menu li a {
    color: #333;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-menu li a:hover {
    background-color: #f5f5f5;
}

.dropdown:hover .dropdown-menu {
    display: block;
}
</style>

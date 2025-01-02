<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Project-I-BCA/assets/css/style.css">
    <style>
        .header-main {
            transition: background-color 0.6s, top 0.6s;
        }
    </style>
</head>
<body>
<header class="header-main" id="header">
    <div class="container">
        <div class="logo">
            <a href="/Project-I-BCA/homepage.php">
                <img src="/Project-I-BCA/assets/images/TableServetransparentmid.png" alt="Logo">
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
                            <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) >= 0): ?>
                                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/Project-I-BCA/public/dashboarduser.php">Dashboard</a></li>
                            <li><a href="/Project-I-BCA/public/orders/order_confirmation.php">View Cart</a></li>
                            <li><a href="/Project-I-BCA/public/orders/my_receipt.php">My Orders</a></li>
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
<script>
    window.addEventListener('scroll', function() {
        var header = document.getElementById('header');
        if (window.scrollY > 10) {
            header.style.backgroundColor = '#4CAF50'; // Green theme
            header.style.position = 'fixed';
            header.style.top = '0';
            header.style.width = '100%';
        } else {
            header.style.backgroundColor = 'transparent';
            header.style.position = 'relative';
        }
    });
</script>
</body>
</html>

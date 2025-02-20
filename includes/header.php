<?php
// session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php'; // Include database connection

function getUnpaidOrdersCount($conn, $user_id) {
    // Get unpaid orders from the last 24 hours
    $sql = "SELECT COUNT(*) as count 
            FROM orders 
            WHERE user_id = ? 
            AND status = 'pending' 
            AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            AND payment_status = 'not_paid'";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Get the count if user is logged in
$unpaidOrdersCount = 0;
if (isset($_SESSION['user_id'])) {
    $unpaidOrdersCount = getUnpaidOrdersCount($conn, $_SESSION['user_id']);
}
?>

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
        .cart-icon-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1000;
        }

        .cart-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: #4CAF50;
            border-radius: 50%;
            color: white;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
        }

        .cart-icon:hover {
            transform: scale(1.1);
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-badge {
            background: #ff4444;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            min-width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 5px;
        }

        .dropdown-menu li a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 15px;
        }

        .dropdown-menu li a:hover .notification-badge {
            background: #cc0000;
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
                            <li>
                                <a href="/Project-I-BCA/public/orders/my_receipt.php" style="display: flex; align-items: center; justify-content: space-between;">
                                    My Orders
                                    <?php if ($unpaidOrdersCount > 0): ?>
                                        <span class="notification-badge" title="Unpaid orders from last 24 hours"><?php echo $unpaidOrdersCount; ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li><a href="/Project-I-BCA/public/profile/logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/Project-I-BCA/public/profile/login.php">Login</a></li>
                    <li><a href="/Project-I-BCA/public/profile/register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="cart-icon-container">
            <a href="/Project-I-BCA/public/orders/order_confirmation.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                <?php endif; ?>
            </a>
        </div>
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

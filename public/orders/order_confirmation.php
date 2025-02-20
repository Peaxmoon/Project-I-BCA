<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../profile/login.php");
    exit();
}

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle adding items to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($item_id && $name && $price) {
        // Check if item already exists in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['item_id'] === $item_id) {
                $cart_item['quantity'] += $quantity;
                $cart_item['total'] = $cart_item['quantity'] * $cart_item['price'];
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['cart'][] = [
                'item_id' => $item_id,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
                'total' => $price * $quantity
            ];
        }
        
        $_SESSION['success'] = "Item added to cart successfully!";
        header("Location: order_confirmation.php");
        exit();
    }
}

$total_order_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_order_amount += $item['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - TableServe</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .order-summary {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .order-item {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 1fr auto;
            gap: 10px;
            padding: 10px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        .order-total {
            text-align: right;
            font-weight: bold;
            padding: 20px;
        }
        .actions {
            display: flex;
            justify-content: space-between;
            padding: 20px;
        }
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary {
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-danger {
            background: #ff4444;
            color: white;
            border: none;
            cursor: pointer;
        }
        .empty-cart {
            text-align: center;
            padding: 40px;
        }
        .success-message {
            background: #4CAF50;
            color: white;
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            max-width: 800px;
            text-align: center;
        }
        .error-message {
            background: #ff4444;
            color: white;
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            max-width: 800px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="empty-cart">
                <p>Your cart is empty. <a href="../menu/menu_items.php">Browse our menu</a></p>
            </div>
        <?php else: ?>
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="order-items">
                    <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                        <div class="order-item">
                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                            <span>₹<?php echo htmlspecialchars($item['price']); ?></span>
                            <span>
                                <form action="update_cart.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="index" value="<?php echo $index; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()" style="width: 60px;">
                                </form>
                            </span>
                            <span>₹<?php echo htmlspecialchars($item['total']); ?></span>
                            <form action="remove_item.php" method="POST" style="display: inline;">
                                <input type="hidden" name="index" value="<?php echo $index; ?>">
                                <button type="submit" class="btn btn-danger" title="Remove item">×</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-total">
                    Total Amount: ₹<?php echo number_format($total_order_amount, 2); ?>
                </div>
                
                <div class="actions">
                    <a href="../menu/menu_items.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Continue Shopping
                    </a>
                    <form method="POST" action="place_order.php" onsubmit="return confirmOrder(event)">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i> Place Order
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../../includes/footer.php'; ?>

    <script>
    function confirmOrder(event) {
        event.preventDefault();
        
        const total = <?php echo array_sum(array_column($_SESSION['cart'], 'total')); ?>;
        
        if (confirm(`Are you sure you want to place this order?\nTotal Amount: Rs. ${total.toFixed(2)}`)) {
            event.target.submit();
        }
        return false;
    }
    </script>
</body>
</html>

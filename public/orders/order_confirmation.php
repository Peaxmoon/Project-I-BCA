<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: /Project-I-BCA/public/menu/menu_items.php");
    exit();
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
    <title>Order Confirmation</title>
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
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        .btn-danger {
            background: #ff4444;
            color: white;
        }
    </style>
</head>
<body>
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
                        <button type="submit" class="btn btn-danger">×</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="order-total">
            Total Amount: ₹<?php echo number_format($total_order_amount, 2); ?>
        </div>
        
        <div class="actions">
            <a href="../menu/menu_items.php" class="btn">Add More Items</a>
            <form action="place_order.php" method="POST" style="display: inline;">
                <button type="submit" class="btn btn-primary">Place Order</button>
            </form>
        </div>
    </div>
</body>
</html>

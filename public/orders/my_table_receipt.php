<?php
// Include the database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';
session_start();

if (!isset($_COOKIE['table_number'])) {
    // Redirect to scantable.php if the cookie is not set
    header("Location: /Project-I-BCA/scantable.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /Project-I-BCA/public/profile/login.php"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_name'];

// Fetch today's orders for the table with payment status
$current_date = date('Y-m-d');
$sql = "SELECT oi.id, mi.name AS food_name, oi.quantity, oi.price, 
        (oi.quantity * oi.price) AS total_price, o.table_id, 
        u.name AS ordered_by, o.payment_status, o.id as order_id,
        oi.payment_status as item_payment_status
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        JOIN users u ON o.user_id = u.id
        WHERE o.table_id = ? AND DATE(o.created_at) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $_COOKIE['table_number'], $current_date);
$stmt->execute();
$result = $stmt->get_result();

// Calculate totals
$grand_total = 0;
$unpaid_total = 0;
$payment_status = null;

if ($result->num_rows > 0) {
    $first_row = $result->fetch_assoc();
    $payment_status = $first_row['payment_status'];
    $result->data_seek(0);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table Order Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #f4f4f4;
        }

        .grand-total {
            font-weight: bold;
            font-size: 18px;
            text-align: right;
            margin-top: 20px;
        }

        .pay-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
        }

        .pay-button:hover {
            background-color: #45a049;
        }
        .payment-info {
    margin: 10px 0;
    text-align: center;
}
.payment-info p {
    margin: 5px 0;
}
.item-paid { 
    color: #4CAF50; 
    font-weight: bold;
}
.item-unpaid { 
    color: #ff4444; 
    font-weight: bold;
}
    </style>
</head>
<body>
    <header>
        <h1>Table Order Receipt</h1>
        <p>Hello, <?php echo htmlspecialchars($username); ?>!</p>
        <p>Table Number: <?php echo htmlspecialchars($_COOKIE['table_number']); ?></p>
        <h2 style="color: <?php echo ($payment_status === 'paid') ? '#4CAF50' : '#ff4444'; ?>">
            Table Payment Status: <?php echo ucfirst(htmlspecialchars($payment_status ?? 'unpaid')); ?>
        </h2>
    </header>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Food Item</th>
                    <th>Quantity</th>
                    <th>Price (Rs.)</th>
                    <th>Total (Rs.)</th>
                    <th>Ordered By</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                while ($row = $result->fetch_assoc()):
                    $grand_total += $row['total_price'];
                    if ($row['item_payment_status'] === 'not_paid') {
                        $unpaid_total += $row['total_price'];
                    }
                ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                        <td><?php echo htmlspecialchars($row['ordered_by']); ?></td>
                        <td class="<?php echo $row['item_payment_status'] === 'paid' ? 'item-paid' : 'item-unpaid'; ?>">
                            <?php echo ucfirst(htmlspecialchars($row['item_payment_status'])); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p class="grand-total">Total Table Amount: Rs. <?php echo htmlspecialchars($grand_total); ?></p>
        <?php if ($unpaid_total > 0): ?>
            <p class="grand-total">Unpaid Amount: Rs. <?php echo htmlspecialchars($unpaid_total); ?></p>
            <?php 
            $test_amount = ceil($unpaid_total / 10);
            $test_amount = min(1000, max(10, $test_amount));
            ?>
            <form method="POST" action="/Project-I-BCA/admin/khalti/initiate_payment.php">
                <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($_COOKIE['table_number']); ?>">
                <input type="hidden" name="amount" value="<?php echo htmlspecialchars($test_amount * 100); ?>">
                <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($first_row['order_id']); ?>">
                <div class="payment-info">
                    <p><small>*You will be paying for all unpaid orders at this table</small></p>
                    <p><small>(Test Mode: Actual amount Rs. <?php echo htmlspecialchars($unpaid_total); ?> is scaled to Rs. <?php echo htmlspecialchars($test_amount); ?> for testing)</small></p>
                </div>
                <button type="submit" class="pay-button">Pay for Table</button>
            </form>
        <?php else: ?>
            <p class="payment-status paid">All table payments completed</p>
        <?php endif; ?>
    <?php else: ?>
        <p>No orders found for today.</p>
    <?php endif; ?>

    <div class="actions">
        <a href="/Project-I-BCA/public/menu/menu_items.php" class="button">Back to Menu</a>
        <a href="my_receipt.php">Go My Receipt</a>
        <a href="orders.php">Go Previous Orders</a>
        <button onclick="window.print()" class="button">Print Receipt</button>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

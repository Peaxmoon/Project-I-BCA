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

// Fetch today's orders for the table, along with user names
$current_date = date('Y-m-d'); // Get the current date
$sql = "SELECT oi.id, mi.name AS food_name, oi.quantity, oi.price, 
               (oi.quantity * oi.price) AS total_price, o.table_id, 
               u.name AS ordered_by, o.payment_status
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        JOIN users u ON o.user_id = u.id
        WHERE o.table_id = ? AND DATE(o.created_at) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $_COOKIE['table_number'], $current_date); // Use table number from cookie
$stmt->execute();
$result = $stmt->get_result();

// Calculate the total cost
$grand_total = 0;

// Get the payment status
$payment_status = null;
if ($result->num_rows > 0) {
    $first_row = $result->fetch_assoc();
    $payment_status = $first_row['payment_status'];
    $result->data_seek(0); // Reset pointer for iteration
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
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
    </style>
</head>
<body>
    <header>
        <h1>Order Receipt</h1>
        <p>Hello, <?php echo htmlspecialchars($username); ?>!</p>
        <p>You ordered from Table: <?php echo htmlspecialchars($_COOKIE['table_number']); ?></p>
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
                </tr>
            </thead>
            <tbody>
                <?php
                $counter = 1;
                while ($row = $result->fetch_assoc()):
                    $grand_total += $row['total_price'];
                ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo htmlspecialchars($row['food_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_price']); ?></td>
                        <td><?php echo htmlspecialchars($row['ordered_by']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <p class="grand-total">Grand Total: Rs. <?php echo htmlspecialchars($grand_total); ?></p>
        <?php if ($payment_status === 'paid'): ?>
            <p class="grand-total">This table's payment has already been completed. Thank you!</p>
        <?php else: ?>
            <form method="POST" action="/Project-I-BCA/public/orders/pay_table.php">
                <input type="hidden" name="table_id" value="<?php echo htmlspecialchars($_COOKIE['table_number']); ?>">
                <small>*You will be paying for the food only table you scanned</small>
                <button type="submit" class="pay-button">Pay for Entire Table</button>
            </form>
        <?php endif; ?>
    <?php else: ?>
        <p>No orders found for today.</p>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

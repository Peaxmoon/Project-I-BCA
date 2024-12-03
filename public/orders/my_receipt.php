<!-- my_receipt.php -->
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

// Fetch today's orders for the user
$current_date = date('Y-m-d'); // Get the current date
$sql = "SELECT oi.id, mi.name AS food_name, oi.quantity, oi.price, 
               (oi.quantity * oi.price) AS total_price, o.table_id
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN menu_items mi ON oi.menu_item_id = mi.id
        WHERE o.user_id = ? AND DATE(o.created_at) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $user_id, $current_date);
$stmt->execute();
$result = $stmt->get_result();

// Calculate the total cost
$grand_total = 0;

// Get the table number from the first row (if any order exists)
$table_number = null;
if ($result->num_rows > 0) {
    $first_row = $result->fetch_assoc();
    $table_number = $first_row['table_id'];
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
        <?php if ($table_number): ?>
            <p>You ordered from Table: <?php echo htmlspecialchars($table_number); ?></p>
        <?php endif; ?>
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
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <p class="grand-total">Grand Total: Rs. <?php echo htmlspecialchars($grand_total); ?></p>
        <small>*You will be paying for the food only you ordered</small>
        <a href="/Project-I-BCA/public/orders/payment.html" class="pay-button">Pay Online</a>
    <?php else: ?>
        <p>No orders found for today.</p>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

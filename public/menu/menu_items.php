<!-- menu_items.php -->
<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';

// Fetch menu items from the database
$sql = "SELECT id, name, description, price, image FROM menu_items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        header {
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .container {
            width: 90%;
            margin: 20px auto;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .menu-item {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s;
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .menu-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .menu-details {
            padding: 15px;
        }

        .menu-name {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }

        .menu-description {
            font-size: 14px;
            color: #666;
            margin: 10px 0;
        }

        .menu-price {
            font-size: 16px;
            font-weight: bold;
            color: #4CAF50;
            margin: 10px 0;
        }
        .quantity-selector {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin: 10px 0;
}

.quantity-btn {
    padding: 5px 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.quantity-btn:hover {
    background-color: #45a049;
}

.quantity-input {
    width: 50px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}


        .order-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .order-button:hover {
            background-color: #45a049;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>

<body>
    <header>
        <h1>Our Menu</h1>
        <br>
        <a href="/Project-I-BCA/homepage.php">Home Page</a>
        <a href="/Project-I-BCA/public/dashboarduser.php">Dashboard</a>
        <a href="../orders/receipt.php">Receipt</a>
    </header>

    <div class="container">
        <div class="menu-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="menu-item">
    <img src="/uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="menu-image">
    <div class="menu-details">
        <p class="menu-name"><?php echo htmlspecialchars($row['name']); ?></p>
        <p class="menu-description"><?php echo htmlspecialchars($row['description']); ?></p>
        <p class="menu-price">Rs. <?php echo htmlspecialchars($row['price']); ?></p>

        <!-- Quantity Selector Form -->
        <form action="/Project-I-BCA/public/orders/insert_order.php" method="POST">
            <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
            <div class="quantity-selector">
                <button type="button" onclick="changeQuantity(this, -1)">-</button>
                <input type="number" name="quantity" class="quantity-input" value="1" min="1">
                <button type="button" onclick="changeQuantity(this, 1)">+</button>
            </div>
            <button type="submit" class="order-button">Order Now</button>
        </form>
    </div>
</div>

                <?php endwhile; ?>
            <?php else: ?>
                <p>No menu items available.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Restaurant Management</p>
    </footer>
     <script> 
//         function increaseQuantity(button) {
//     const input = button.previousElementSibling; // Select the input field
//     input.value = parseInt(input.value) + 1;
// }

// function decreaseQuantity(button) {
//     const input = button.nextElementSibling; // Select the input field
//     const currentValue = parseInt(input.value);
//     if (currentValue > 1) {
//         input.value = currentValue - 1;
//     }
// }

// function placeOrder(itemId, orderButton) {
//     const quantityInput = orderButton.previousElementSibling.querySelector('.quantity-input');
//     const quantity = quantityInput.value;

//     // Redirect to server with itemId and quantity (or send via AJAX)
//     window.location.href = `/Project-I-BCA/public/orders/insert_order.php?item_id=${itemId}&quantity=${quantity}`;
// }
function changeQuantity(button, delta) {
    const input = button.parentElement.querySelector('.quantity-input');
    let value = parseInt(input.value) || 1;
    value = Math.max(1, value + delta); // Prevent quantity below 1
    input.value = value;
}
    </script>
</body>

</html>
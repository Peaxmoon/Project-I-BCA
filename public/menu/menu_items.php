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
                            <a href="/Project-I-BCA/public/orders/insert_order.php?item_id=<?php echo $row['id']; ?>" class="order-button">Order Now</a>
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
</body>
</html>

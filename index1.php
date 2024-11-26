<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TableServe Restaurant</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>TableServe Restaurant</h1>
    </header>
















    
    <main>
        <section id="menu">
            <h2>Menu</h2>
            <div id="menu-items"></div>
        </section>
        <section id="order">
            <h2>Your Order</h2>
            <div id="order-items"></div>
            <button id="place-order">Place Order</button>
        </section>
    </main>




<!-- insert_table.php -->
    <h2>Add Table</h2>
    <form action="./public/insert_table.php" method="POST">
        <label>Table Number:</label><br>
        <input type="number" name="table_number" required><br><br>
        
        <label>Location:</label><br>
        <input type="text" name="location"><br><br>
        
        <button type="submit">Add Table</button>
    </form>


<!-- insert_menu_item.php -->
    <h2>Add Menu Item</h2>
    <form action="./public/insert_menu_item.php" method="POST">
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>
        
        <label>Description:</label><br>
        <textarea name="description"></textarea><br><br>
        
        <label>Price:</label><br>
        <input type="number" step="1" name="price" required><br><br>
        
        <button type="submit">Add Menu Item</button>
    </form>


    <!-- order_form.php -->
    <h2>Place Order</h2>
    <form action="./public/insert_order.php" method="POST">
        <label>User ID:</label><br>
        <input type="number" name="user_id" required><br><br>
        
        <label>Table ID:</label><br>
        <input type="number" name="table_id" required><br><br>
        
        <label>Total Price:</label><br>
        <input type="number" step="0.01" name="total_price" required><br><br>
        
        <button type="submit">Place Order</button>
    </form>




<!-- insert_order_item.php -->
    <h2>Add Order Item</h2>
    <form action="./public/insert_order_item.php" method="POST">
        <label>Order ID:</label><br>
        <input type="number" name="order_id" required><br><br>
        
        <label>Menu Item ID:</label><br>
        <input type="number" name="menu_item_id" required><br><br>
        
        <label>Quantity:</label><br>
        <input type="number" name="quantity" required><br><br>
        
        <label>Price:</label><br>
        <input type="number" step="0.01" name="price" required><br><br>
        
        <button type="submit">Add Order Item</button>
    </form>

    <script src="script.js"></script>
</body>
</html>
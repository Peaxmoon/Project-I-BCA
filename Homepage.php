<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TableServe Restaurant</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <?php 
    session_start();
    include './includes/header.php'; 
    ?>

    <section id="hero">
        <div class="hero-content">
            <h1>Welcome to TableServe</h1>
            <p>Experience the finest dining with our digital ordering system</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="./public/menu/menu_items.php" class="btn">View Menu</a>
            <?php else: ?>
                <a href="./public/profile/login.php" class="btn">Login to Order</a>
            <?php endif; ?>
        </div>
    </section>

    <section id="about" class="container">
        <h2>About Us</h2>
        <p>At TableServe Restaurant, we combine traditional dining with modern technology to provide you with the best possible experience. Our digital ordering system allows you to browse our menu and place orders directly from your table.</p>
    </section>

    <section id="features" class="container">
        <h2>Our Services</h2>
        <div class="features-grid">
            <div class="feature">
                <h3>Digital Menu</h3>
                <p>Browse our complete menu with detailed descriptions and images.</p>
            </div>
            <div class="feature">
                <h3>Table Service</h3>
                <p>Scan your table's QR code for a personalized dining experience.</p>
            </div>
            <div class="feature">
                <h3>Quick Ordering</h3>
                <p>Place orders directly from your device without waiting for staff.</p>
            </div>
        </div>
    </section>

    <section id="menu-preview" class="container">
        <h2>Popular Dishes</h2>
        <div class="menu-grid">
            <?php
            include './config/database.php';
            $sql = "SELECT * FROM menu_items LIMIT 3";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='menu-item'>";
                    echo "<img src='/uploads/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'>";
                    echo "<h3>" . htmlspecialchars($row['name']) . "</h3>";
                    echo "<p>" . htmlspecialchars($row['description']) . "</p>";
                    echo "<p class='price'>Rs. " . htmlspecialchars($row['price']) . "</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>
        <div class="cta">
            <a href="./public/menu/menu_items.php" class="btn">View Full Menu</a>
        </div>
    </section>

    <?php include './includes/footer.php'; ?>
    <script src="./assets/js/script.js"></script>
</body>
</html>


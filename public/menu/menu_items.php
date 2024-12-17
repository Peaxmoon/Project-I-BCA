<!-- menu_items.php -->
<?php
// Include database connection
include $_SERVER['DOCUMENT_ROOT'] . '/Project-I-BCA/config/database.php';
session_start();

if (!isset($_COOKIE['table_number'])) {
    header("Location: /Project-I-BCA/scantable.php");
    exit();
}

// Fetch menu items from the database
$sql = "SELECT id, name, description, price, image FROM menu_items";
$result = $conn->query($sql);
?>
<!-- Add this alert section -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert success">
        <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
        ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert error">
        <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
        ?>
    </div>
<?php endif; ?>

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
    <?php include '../../includes/header.php'; ?>


    <h1>Our Menu</h1>


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
        <form onsubmit="return addToCart(this)" action="/Project-I-BCA/public/orders/insert_order.php" class="order-form">
            <input type="hidden" name="item_id" value="<?php echo $row['id']; ?>">
            <div class="quantity-selector">
                <button type="button" onclick="changeQuantity(this, -1)">-</button>
                <input type="number" name="quantity" class="quantity-input" value="1" min="1">
                <button type="button" onclick="changeQuantity(this, 1)">+</button>
            </div>
            <button type="submit" class="order-button">Add to Cart</button>
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
function changeQuantity(button, delta) {
    const input = button.parentElement.querySelector('.quantity-input');
    let value = parseInt(input.value) || 1;
    value = Math.max(1, value + delta);
    input.value = value;
}

function addToCart(form) {
    event.preventDefault(); // Prevent default form submission
    
    const formData = new FormData(form);
    
    // Use absolute path to insert_order.php
    fetch('/Project-I-BCA/public/orders/insert_order.php', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update cart count in header
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.cart_count;
            }
            
            // Show success message
            showNotification(data.message || 'Item added to cart!', 'success');
            
            // Reset quantity to 1
            form.querySelector('.quantity-input').value = 1;
        } else {
            throw new Error(data.message || 'Failed to add item to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification(error.message || 'Error adding item to cart', 'error');
    });
    
    return false;
}

function showNotification(message, type = 'success') {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());

    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Add styles if they don't exist
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 4px;
                color: white;
                z-index: 1000;
                animation: slideIn 0.5s ease-out;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                min-width: 200px;
                text-align: center;
            }
            
            .notification.success {
                background-color: #4CAF50;
            }
            
            .notification.error {
                background-color: #f44336;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.5s ease-in forwards';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}
    </script>
</body>

</html>
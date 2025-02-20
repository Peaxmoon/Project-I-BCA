<!-- menu_items.php -->
<?php
session_start();
require_once '../../config/database.php';

// Fetch all categories
$stmt = $conn->prepare("
    SELECT c.*, COUNT(m.id) as item_count 
    FROM menu_categories c 
    LEFT JOIN menu_items m ON c.id = m.category_id 
    GROUP BY c.id
");
$stmt->execute();
$categories = $stmt->get_result();

// Get selected category (if any)
$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Fetch menu items with category filter
$query = "
    SELECT 
        m.*,
        c.name as category_name,
        ROUND(AVG(r.rating), 1) as average_rating,
        COUNT(r.id) as rating_count
    FROM menu_items m
    LEFT JOIN menu_categories c ON m.category_id = c.id
    LEFT JOIN food_ratings r ON m.id = r.menu_item_id
";

if ($selected_category > 0) {
    $query .= " WHERE m.category_id = ?";
}

$query .= " GROUP BY m.id ORDER BY m.category_id, m.name";

$stmt = $conn->prepare($query);
if ($selected_category > 0) {
    $stmt->bind_param("i", $selected_category);
}
$stmt->execute();
$menu_items = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - TableServe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="../../assets/js/notifications.js"></script>
    <style>
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
        }

        .menu-item {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .menu-item:hover {
            transform: translateY(-5px);
        }

        .menu-image {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .menu-content {
            padding: 1rem;
        }

        .menu-category {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .menu-name {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 0.3rem;
        }

        .menu-description {
            color: #666;
            margin-bottom: 0.8rem;
            font-size: 0.85rem;
        }

        .menu-price {
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 1rem;
        }

        .rating-section {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            padding-top: 0.5rem;
            border-top: 1px solid #eee;
        }

        .stars {
            color: #ffd700;
            font-size: 1.1rem;
        }

        .rating-count {
            color: #666;
            font-size: 0.9rem;
        }

        .menu-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.8rem;
        }

        .rate-btn {
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .rate-btn:hover {
            background: #e9ecef;
        }

        .add-to-cart {
            width: 100%;
            padding: 0.5rem;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            text-align: center;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .add-to-cart:hover {
            background: #45a049;
        }

        .rating-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .menu-grid {
                padding: 1rem;
            }
        }

        .categories-nav {
            background: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .categories-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .categories-list {
            display: flex;
            gap: 1rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-item {
            flex: 0 0 auto;
        }

        .category-link {
            display: inline-block;
            padding: 0.5rem 1rem;
            color: #666;
            text-decoration: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
        }

        .category-link:hover {
            background: #e9ecef;
            color: #333;
        }

        .category-link.active {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .category-link .item-count {
            display: inline-block;
            background: rgba(0,0,0,0.1);
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            font-size: 0.8rem;
            margin-left: 0.5rem;
        }

        .category-header {
            margin: 2rem 0;
            text-align: center;
        }

        .category-header h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .category-header p {
            color: #666;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .categories-container {
                padding: 0 1rem;
            }

            .category-link {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
            }
        }

        .cart-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .quantity-input {
            width: 60px;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.5s ease-out;
        }
        
        .success-notification {
            background-color: #4CAF50;
        }
        
        .error-notification {
            background-color: #f44336;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        .rating-summary {
            display: flex;
            gap: 2rem;
            margin: 1rem 0;
        }

        .average-rating {
            text-align: center;
        }

        .big-rating {
            font-size: 3rem;
            font-weight: bold;
            color: #333;
        }

        .rating-bars {
            flex: 1;
        }

        .rating-bar-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 0.5rem 0;
        }

        .rating-bar {
            flex: 1;
            height: 8px;
            background: #eee;
            border-radius: 4px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: #ffc107;
            transition: width 0.3s ease;
        }

        .recent-ratings {
            margin-top: 2rem;
        }

        .rating-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }

        .rating-item .user-name {
            font-weight: 500;
        }

        .rating-item .rating-date {
            color: #666;
            font-size: 0.9em;
        }

        .stars-display {
            color: #ffc107;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <nav class="categories-nav">
        <div class="categories-container">
            <ul class="categories-list">
                <li class="category-item">
                    <a href="menu_items.php" 
                       class="category-link <?php echo !$selected_category ? 'active' : ''; ?>">
                        All Menu
                    </a>
                </li>
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <li class="category-item">
                        <a href="?category=<?php echo $category['id']; ?>" 
                           class="category-link <?php echo $selected_category == $category['id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                            <span class="item-count"><?php echo $category['item_count']; ?></span>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php if ($selected_category): ?>
            <?php 
            $categories->data_seek(0);
            while ($category = $categories->fetch_assoc()) {
                if ($category['id'] == $selected_category) {
                    echo '<div class="category-header">';
                    echo '<h2>' . htmlspecialchars($category['name']) . '</h2>';
                    if ($category['description']) {
                        echo '<p>' . htmlspecialchars($category['description']) . '</p>';
                    }
                    echo '</div>';
                    break;
                }
            }
            ?>
        <?php else: ?>
            <div class="category-header">
                <h2>Our Complete Menu</h2>
                <p>Explore our wide variety of delicious dishes</p>
            </div>
        <?php endif; ?>

        <div class="menu-grid">
            <?php while ($item = $menu_items->fetch_assoc()): ?>
                <div class="menu-item">
                    <div class="menu-image-container">
                        <?php 
                        $imagePath = $item['image'];
                        $fullPath = "../../assets/images/" . $imagePath;
                        // Debug output
                        error_log("Image path from DB: " . $imagePath);
                        error_log("Full image path: " . $fullPath);
                        error_log("File exists: " . (file_exists($fullPath) ? 'true' : 'false'));
                        
                        if ($imagePath && file_exists($fullPath)): 
                        ?>
                            <img src="../../assets/images/<?php echo htmlspecialchars($imagePath); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 class="menu-image">
                        <?php else: ?>
                            <img src="../../assets/images/default-food.jpg" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 class="menu-image">
                        <?php endif; ?>
                    </div>
                    
                    <div class="menu-content">
                        <div class="menu-category">
                            <?php echo htmlspecialchars($item['category_name']); ?>
                        </div>
                        
                        <h3 class="menu-name">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </h3>
                        
                        <p class="menu-description">
                            <?php echo htmlspecialchars($item['description']); ?>
                        </p>
                        
                        <div class="menu-price">
                            ₹<?php echo number_format($item['price'], 2); ?>
                        </div>

                        <div class="rating-section">
                            <div class="stars">
                                <?php
                                $rating = $item['average_rating'] ?? 0;
                                $fullStars = floor($rating);
                                $hasHalfStar = $rating - $fullStars >= 0.5;
                                
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $fullStars) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($i == $fullStars + 1 && $hasHalfStar) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <span class="rating-count">
                                <?php 
                                    echo number_format($rating, 1);
                                    echo ' (' . $item['rating_count'] . ' ' . 
                                         ($item['rating_count'] == 1 ? 'review' : 'reviews') . ')';
                                ?>
                            </span>
                        </div>

                        <div class="menu-actions">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="rating.php?id=<?php echo $item['id']; ?>" class="rate-btn">
                                    <i class="fas fa-star"></i> Rate
                                </a>
                                <form onsubmit="addToCart(event, this)" class="add-to-cart-form">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                                    <input type="hidden" name="price" value="<?php echo $item['price']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="add-to-cart">
                                        Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <a href="../profile/login.php" class="rate-btn">
                                    <i class="fas fa-user"></i> Login to Rate
                                </a>
                                <a href="../profile/login.php" class="add-to-cart">
                                    Login to Order
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script>
    function addToCart(event, form) {
        event.preventDefault();
        
        const formData = new FormData(form);
        formData.append('add_to_cart', '1');
        
        fetch('../orders/insert_order.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success notification
                const notification = document.createElement('div');
                notification.className = 'notification success-notification';
                notification.innerHTML = `
                    <i class="fas fa-check-circle"></i>
                    ${data.message}
                `;
                document.body.appendChild(notification);
                
                // Update cart count
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                    cartCount.style.display = data.cart_count > 0 ? 'flex' : 'none';
                }
                
                // Remove notification after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            } else {
                throw new Error(data.message || 'Error adding item to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Show error notification
            const notification = document.createElement('div');
            notification.className = 'notification error-notification';
            notification.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                ${error.message}
            `;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.remove();
            }, 3000);
        });
    }
    </script>

    <!-- Add this where you want to show the ratings -->
    <div class="rating-modal" id="ratingModal">
        <div class="rating-content">
            <div class="rating-header">
                <h3>Ratings & Reviews</h3>
                <div class="rating-summary">
                    <div class="average-rating">
                        <span class="big-rating">0.0</span>
                        <div class="stars-display"></div>
                        <span class="total-ratings">(0 ratings)</span>
                    </div>
                    <div class="rating-bars">
                        <div class="rating-bar-row">
                            <span>5 ★</span>
                            <div class="rating-bar">
                                <div class="bar-fill" style="width: 0%"></div>
                            </div>
                            <span class="bar-percent">0%</span>
                        </div>
                        <!-- Repeat for 4,3,2,1 stars -->
                    </div>
                </div>
            </div>
            <div class="recent-ratings">
                <h4>Recent Reviews</h4>
                <div class="ratings-list"></div>
            </div>
            <!-- Add rating form here -->
        </div>
    </div>

    <script>
    function updateRatingDisplay(itemId) {
        fetch(`/Project-I-BCA/public/menu/get_ratings.php?item_id=${itemId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update average rating
                    document.querySelector('.big-rating').textContent = data.stats.average_rating;
                    document.querySelector('.total-ratings').textContent = 
                        `(${data.stats.total_ratings} ratings)`;
                    
                    // Update rating bars
                    document.querySelector('[data-stars="5"] .bar-fill').style.width = 
                        `${data.stats.five_star_percent}%`;
                    // ... repeat for other star ratings
                    
                    // Update recent reviews
                    const ratingsList = document.querySelector('.ratings-list');
                    ratingsList.innerHTML = data.ratings.map(rating => `
                        <div class="rating-item">
                            <div class="user-name">${rating.user_name}</div>
                            <div class="stars-display">${'★'.repeat(rating.rating)}${'☆'.repeat(5-rating.rating)}</div>
                            <div class="rating-date">${new Date(rating.created_at).toLocaleDateString()}</div>
                            <div class="comment">${rating.comment}</div>
                        </div>
                    `).join('');
                }
            })
            .catch(error => console.error('Error:', error));
    }
    </script>
</body>
</html>
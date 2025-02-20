<?php
require_once '../../config/database.php';

// Get page number from URL, default to 1
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 10; // Number of ratings per page
$offset = ($page - 1) * $per_page;

if (isset($_GET['item_id'])) {
    $item_id = intval($_GET['item_id']);
    
    try {
        // Get overall rating statistics
        $stats_sql = "SELECT 
            COUNT(*) as total_ratings,
            ROUND(AVG(rating), 1) as average_rating,
            SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
            SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
            SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
            SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
            SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
            FROM food_ratings 
            WHERE menu_item_id = ?";
            
        $stmt = $conn->prepare($stats_sql);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stats = $stmt->get_result()->fetch_assoc();
        
        // Get total number of ratings for pagination
        $count_sql = "SELECT COUNT(*) as total FROM food_ratings WHERE menu_item_id = ?";
        $stmt = $conn->prepare($count_sql);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $total_count = $stmt->get_result()->fetch_assoc()['total'];
        $total_pages = ceil($total_count / $per_page);
        
        // Get paginated ratings with user names
        $ratings_sql = "SELECT 
            fr.rating,
            fr.comment,
            fr.created_at,
            u.name as user_name
            FROM food_ratings fr
            JOIN users u ON fr.user_id = u.id
            WHERE fr.menu_item_id = ?
            ORDER BY fr.created_at DESC
            LIMIT ? OFFSET ?";
            
        $stmt = $conn->prepare($ratings_sql);
        $stmt->bind_param("iii", $item_id, $per_page, $offset);
        $stmt->execute();
        $ratings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Calculate percentages for star ratings
        $total = $stats['total_ratings'] > 0 ? $stats['total_ratings'] : 1;
        $stats['five_star_percent'] = round(($stats['five_star'] / $total) * 100);
        $stats['four_star_percent'] = round(($stats['four_star'] / $total) * 100);
        $stats['three_star_percent'] = round(($stats['three_star'] / $total) * 100);
        $stats['two_star_percent'] = round(($stats['two_star'] / $total) * 100);
        $stats['one_star_percent'] = round(($stats['one_star'] / $total) * 100);
        
        // Get menu item details
        $item_sql = "SELECT name FROM menu_items WHERE id = ?";
        $stmt = $conn->prepare($item_sql);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        
        // Return HTML instead of JSON
        header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ratings for <?php echo htmlspecialchars($item['name']); ?></title>
    <link rel="stylesheet" href="/Project-I-BCA/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .ratings-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .rating-summary {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        .rating-list {
            display: grid;
            gap: 1rem;
        }
        .rating-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        .page-link {
            padding: 0.5rem 1rem;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        .page-link.active {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        .stars {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="ratings-container">
        <a href="/Project-I-BCA/public/menu/menu_items.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Menu
        </a>
        
        <h1>Ratings for <?php echo htmlspecialchars($item['name']); ?></h1>
        
        <div class="rating-summary">
            <h2>Overall Rating: <?php echo $stats['average_rating']; ?> ★</h2>
            <p>Total Reviews: <?php echo $stats['total_ratings']; ?></p>
            <div class="rating-bars">
                <?php for($i = 5; $i >= 1; $i--): ?>
                    <div class="rating-bar-row">
                        <span><?php echo $i; ?> ★</span>
                        <div class="rating-bar">
                            <div class="bar-fill" style="width: <?php echo $stats[$i.'_star_percent']; ?>%"></div>
                        </div>
                        <span><?php echo $stats[$i.'_star']; ?> reviews</span>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        
        <div class="rating-list">
            <?php foreach($ratings as $rating): ?>
                <div class="rating-item">
                    <div class="stars">
                        <?php echo str_repeat('★', $rating['rating']) . str_repeat('☆', 5 - $rating['rating']); ?>
                    </div>
                    <p class="comment"><?php echo htmlspecialchars($rating['comment']); ?></p>
                    <div class="rating-meta">
                        <span class="user"><?php echo htmlspecialchars($rating['user_name']); ?></span>
                        <span class="date"><?php echo date('M d, Y', strtotime($rating['created_at'])); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if($total_pages > 1): ?>
            <div class="pagination">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?item_id=<?php echo $item_id; ?>&page=<?php echo $i; ?>" 
                       class="page-link <?php echo $page === $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Item ID not provided";
}
?> 
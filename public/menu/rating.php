<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../profile/login.php');
    exit();
}

// Fetch menu item details
$menu_item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->bind_param("i", $menu_item_id);
$stmt->execute();
$menu_item = $stmt->get_result()->fetch_assoc();

if (!$menu_item) {
    header('Location: menu_items.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate <?php echo htmlspecialchars($menu_item['name']); ?> - TableServe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .rating-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .rating-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .rating-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 0.5rem;
            margin: 2rem 0;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffd700;
        }

        .comment-section {
            margin-top: 2rem;
        }

        .comment-input {
            width: 100%;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 1rem;
            resize: vertical;
            min-height: 100px;
        }

        .submit-btn {
            background: #4CAF50;
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #45a049;
        }

        .ratings-list {
            margin-top: 3rem;
        }

        .rating-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            margin-bottom: 1rem;
        }

        .rating-item:last-child {
            border-bottom: none;
        }

        .rating-stars {
            color: #ffd700;
            margin-bottom: 0.5rem;
        }

        .rating-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .rating-comment {
            color: #333;
            line-height: 1.5;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            display: none;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            display: none;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="rating-container">
        <div class="rating-header">
            <h1>Rate <?php echo htmlspecialchars($menu_item['name']); ?></h1>
            <p>Share your experience with this dish</p>
        </div>

        <div class="success-message" id="successMessage"></div>
        <div class="error-message" id="errorMessage"></div>

        <form id="ratingForm">
            <input type="hidden" name="menu_item_id" value="<?php echo $menu_item_id; ?>">
            
            <div class="star-rating">
                <input type="radio" name="rating" value="5" id="star5">
                <label for="star5" class="fas fa-star"></label>
                <input type="radio" name="rating" value="4" id="star4">
                <label for="star4" class="fas fa-star"></label>
                <input type="radio" name="rating" value="3" id="star3">
                <label for="star3" class="fas fa-star"></label>
                <input type="radio" name="rating" value="2" id="star2">
                <label for="star2" class="fas fa-star"></label>
                <input type="radio" name="rating" value="1" id="star1">
                <label for="star1" class="fas fa-star"></label>
            </div>

            <div class="comment-section">
                <textarea name="comment" class="comment-input" placeholder="Share your thoughts about this dish..." required></textarea>
            </div>

            <button type="submit" class="submit-btn">Submit Rating</button>
        </form>

        <div class="ratings-list" id="ratingsList">
            <!-- Ratings will be loaded here -->
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadRatings();

            document.getElementById('ratingForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('submit_rating.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage('successMessage', 'Thank you for your rating!');
                        this.reset();
                        loadRatings();
                    } else {
                        showMessage('errorMessage', data.message || 'Error submitting rating');
                    }
                })
                .catch(error => {
                    showMessage('errorMessage', 'Error submitting rating');
                });
            });
        });

        function loadRatings() {
            const menuItemId = <?php echo $menu_item_id; ?>;
            
            fetch(`get_ratings.php?menu_item_id=${menuItemId}`)
                .then(response => response.json())
                .then(data => {
                    const ratingsHtml = data.map(rating => `
                        <div class="rating-item">
                            <div class="rating-stars">
                                ${getStarRating(rating.rating)}
                            </div>
                            <div class="rating-meta">
                                By ${rating.user_name} on ${formatDate(rating.created_at)}
                            </div>
                            <div class="rating-comment">
                                ${rating.comment}
                            </div>
                        </div>
                    `).join('');
                    
                    document.getElementById('ratingsList').innerHTML = ratingsHtml;
                })
                .catch(error => {
                    console.error('Error loading ratings:', error);
                });
        }

        function getStarRating(rating) {
            return '★'.repeat(rating) + '☆'.repeat(5 - rating);
        }

        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        function showMessage(elementId, message) {
            const element = document.getElementById(elementId);
            element.textContent = message;
            element.style.display = 'block';
            
            setTimeout(() => {
                element.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html> 
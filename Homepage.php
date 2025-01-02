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


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Bootstrap Example</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  <body class="p-3 m-0 border-0 bd-example">

    <!-- Example Code -->

    <style>
  .carousel-inner img {
    width: 100%;
    height: 600px;
    object-fit: cover;
  }
</style>

 <!-- Hero Section -->
 <section id="hero" class="hero-section">

    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="/Project-I-BCA/assets/images/pexels-amar-15119173.jpg" class="d-block w-100" alt="First slide">
    </div>
    <div class="carousel-item">
      <img src="/Project-I-BCA/assets/images/pexels-rachel-claire-6127316.jpg" class="d-block w-100" alt="Second slide">
    </div>
    <div class="carousel-item">
      <img src="/Project-I-BCA/assets/images/pexels-cottonbro-5371552.jpg" class="d-block w-100" alt="Third slide">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>

    
    <!-- End Example Code -->
  </body>
</html>

        <div class="hero-content container">
            <h1>Welcome to TableServe</h1>
            <p>Experience the finest dining with our digital ordering system</p>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="./public/menu/menu_items.php" class="btn primary-btn">View Menu</a>
            <?php else: ?>
                <a href="./public/profile/login.php" class="btn secondary-btn">Login to Order</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
        <div class="about-container">
            <div class="about-image">
                <img src="/Project-I-BCA/assets/images/restaurant-image.jpg" alt="Our Restaurant">
            </div>
            <div class="about-content">
                <h2>About TableServe</h2>
                <p>Your restaurant description here...</p>
            </div>
        </div>
    </section>

    <!-- Featured Section -->
    <section class="featured-section">
        <div class="featured-grid">
            <!-- Repeat for each feature -->
            <div class="featured-card">
                <div class="featured-image">
                    <img src="/Project-I-BCA/assets/images/feature-image.jpg" alt="Feature">
                </div>
                <div class="featured-content">
                    <h3>Special Dishes</h3>
                    <p>Description of the feature...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-number">5000+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <!-- Add more stat items -->
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section">
        <div class="container">
            <div class="testimonial-card">
                <div class="testimonial-content">
                    "Customer testimonial here..."
                </div>
                <div class="testimonial-author">
                    <div class="author-image">
                        <img src="/Project-I-BCA/assets/images/author-image.jpg" alt="Author">
                    </div>
                    <div class="author-info">
                        <h4>Customer Name</h4>
                        <p>Regular Customer</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include './includes/footer.php'; ?>
    <script src="./assets/js/script.js"></script>
</body>
</html>

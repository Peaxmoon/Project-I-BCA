<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TableServe Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php
    session_start();
    include './includes/header.php';
    ?>

    <!-- Hero Section with Carousel -->
    <section id="hero" class="hero-section position-relative">
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="/Project-I-BCA/assets/images/pexels-amar-15119173.jpg" class="d-block w-100" alt="Restaurant Ambiance">
                    <div class="carousel-caption">
                        <h1 class="display-4 fw-bold">Welcome to TableServe</h1>
                        <p class="lead">Experience the finest dining with our digital ordering system</p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="./public/menu/menu_items.php" class="btn btn-primary btn-lg">View Menu</a>
                        <?php else: ?>
                            <a href="./public/profile/login.php" class="btn btn-outline-light btn-lg">Login to Order</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="/Project-I-BCA/assets/images/pexels-rachel-claire-6127316.jpg" class="d-block w-100" alt="Fine Dining Experience">
                    <div class="carousel-caption">
                        <h1 class="display-4 fw-bold">Exquisite Dining</h1>
                        <p class="lead">Savor the moment with our carefully crafted dishes</p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="./public/menu/menu_items.php" class="btn btn-primary btn-lg">View Menu</a>
                        <?php else: ?>
                            <a href="./public/profile/login.php" class="btn btn-outline-light btn-lg">Login to Order</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="/Project-I-BCA/assets/images/pexels-cottonbro-5371552.jpg" class="d-block w-100" alt="Culinary Excellence">
                    <div class="carousel-caption">
                        <h1 class="display-4 fw-bold">Culinary Excellence</h1>
                        <p class="lead">Where every meal tells a story</p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="./public/menu/menu_items.php" class="btn btn-primary btn-lg">View Menu</a>
                        <?php else: ?>
                            <a href="./public/profile/login.php" class="btn btn-outline-light btn-lg">Login to Order</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- About Section -->
    <section class="about-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="/Project-I-BCA/assets/images/restaurantillustration.jpg" alt="Our Restaurant" class="img-fluid rounded shadow">
                </div>
                <div class="col-md-6">
                    <h2 class="display-5 mb-4">Our Story</h2>
                    <p class="lead">Welcome to TableServe, where tradition meets innovation. We've been serving
                        delightful culinary experiences since 2024, combining the warmth of traditional hospitality
                        with modern digital convenience.</p>
                    <p>Our passion for exceptional food and service has made us a favorite destination for
                        food lovers. With our digital ordering system, we've made it easier than ever to enjoy
                        your favorite dishes.</p>
                    <a href="/Project-I-BCA/public/menu/menu_items.php" class="btn btn-outline-dark mt-3">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Dishes Section -->
    <section class="featured-section bg-light py-5">
        <div class="container">
            <h2 class="text-center display-5 mb-5">Featured Dishes</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="/Project-I-BCA/assets/images/coffee.jpg" class="card-img-top" alt="Special Dish">
                        <div class="card-body">
                            <h3 class="card-title h5">Delicious Coffee</h3>
                            <p class="card-text">Delicious coffee</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="/Project-I-BCA/assets/images/pizza.jpg" class="card-img-top" alt="Special Dish">
                        <div class="card-body">
                            <h3 class="card-title h5">Pizza on different types</h3>
                            <p class="card-text">Premium cuts grilled to perfection</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <img src="/Project-I-BCA/assets/images/samosa.jpg" class="card-img-top" alt="Special Dish">
                        <div class="card-body">
                            <h3 class="card-title h5">Delicious Samosa</h3>
                            <p class="card-text">Sweet endings to your perfect meal</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5 bg-dark text-white">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <div class="stat-number h2">50+</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-utensils fa-3x mb-3"></i>
                        <div class="stat-number h2">20+</div>
                        <div class="stat-label">Dishes</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-award fa-3x mb-3"></i>
                        <div class="stat-number h2">15+</div>
                        <div class="stat-label">Awards</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <i class="fas fa-clock fa-3x mb-3"></i>
                        <div class="stat-number h2">1</div>
                        <div class="stat-label">Year of Service</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
<section class="testimonials-section py-5">
    <div class="container">
        <h2 class="text-center display-5 mb-5">What Our Customers Say</h2>
        <div class="row">
            <!-- Testimonial 1 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Amazing food and excellent service! The digital ordering system makes everything so convenient."</p>
                        <div class="d-flex align-items-center">
                            <img src="/Project-I-BCA/assets/images/profile.jpg" alt="Customer"
                                class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">Sujjal Khadka</h6>
                                <small class="text-muted">Regular Customer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Testimonial 2 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"A seamless experience from booking a table to payment. Highly recommend this restaurant!"</p>
                        <div class="d-flex align-items-center">
                            <img src="/Project-I-BCA/assets/images/profile.jpg" alt="Customer"
                                class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">Pratik Rana</h6>
                                <small class="text-muted">Food Blogger</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Testimonial 3 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Loved the quick service and digital menu! The ordering process was very smooth."</p>
                        <div class="d-flex align-items-center">
                            <img src="/Project-I-BCA/assets/images/profile.jpg" alt="Customer"
                                class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">Aaysh Shrestha</h6>
                                <small class="text-muted">Frequent Diner</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End of Row -->
    </div> <!-- End of Container -->
</section>


    <!-- Call to Action Section -->
    <section class="cta-section py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-6 mb-4">Ready to Experience Our Delicious Menu?</h2>
            <p class="lead mb-4">Join us today and discover why we're the preferred choice for food lovers.</p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="./public/profile/login.php" class="btn btn-light btn-lg">Sign Up Now</a>
            <?php else: ?>
                <a href="./public/menu/menu_items.php" class="btn btn-light btn-lg">Order Now</a>
            <?php endif; ?>
        </div>
    </section>

    <?php include './includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/script.js"></script>
</body>

</html>
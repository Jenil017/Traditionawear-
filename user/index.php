<?php
require_once '../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section position-relative d-flex align-items-center justify-content-center">
    <div class="hero-bg-image" id="heroBgImage"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-content text-center text-white">
        <h1 class="display-4 mb-3">Welcome to Rameshwar Traditional Wear</h1>
        <p class="lead mb-4">Rent Premium Traditional Outfits for Every Occasion</p>
        <a href="products.php" class="btn btn-lg btn-warning fw-bold shadow-lg hero-btn">Browse Collection</a>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Our Categories</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <a href="products.php?category=1" class="category-card d-block text-decoration-none">
                    <i class="fas fa-user-tie"></i>
                    <h4>Sherwani</h4>
                    <p>Elegant sherwanis for weddings and special occasions</p>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="products.php?category=2" class="category-card d-block text-decoration-none">
                    <i class="fas fa-tshirt"></i>
                    <h4>Kurta</h4>
                    <p>Comfortable kurtas for festivals and events</p>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="products.php?category=3" class="category-card d-block text-decoration-none">
                    <i class="fas fa-user-suit"></i>
                    <h4>Blazer</h4>
                    <p>Stylish blazers for formal occasions</p>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">How It Works</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-search fa-3x text-primary mb-3"></i>
                <h4>Browse & Select</h4>
                <p>Choose from our wide collection of traditional wear</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                <h4>Book Dates</h4>
                <p>Select your rental dates and provide details</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-star fa-3x text-primary mb-3"></i>
                <h4>Wear & Enjoy</h4>
                <p>Look great at your special occasion</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">What Our Customers Say</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <div class="star-rating mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"Amazing quality and service! The sherwani I rented was perfect for my wedding."</p>
                    <strong>- Raj Patel</strong>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <div class="star-rating mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"Great collection and affordable prices. Highly recommend for special occasions."</p>
                    <strong>- Priya Shah</strong>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <div class="star-rating mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p>"Easy booking process and excellent customer service. Will definitely use again."</p>
                    <strong>- Amit Kumar</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Link Hero CSS -->
<link rel="stylesheet" href="../assets/css/hero.css">

<!-- Hero Image Slider Script -->
<script>
// Array of hero section images (update paths as needed)
const heroImages = [
    "/rtwrs_web/assets/images/slide1.jpeg",
    "/rtwrs_web/assets/images/slide2.png",
    "/rtwrs_web/assets/images/img3.jpg"
];

let heroIndex = 0;
const heroBg = document.getElementById('heroBgImage');

// Preload images for smooth transitions
heroImages.forEach(src => { const img = new Image(); img.src = src; });

function setHeroImage(idx) {
    heroBg.style.opacity = 0;
    setTimeout(() => {
        heroBg.style.backgroundImage = `url('${heroImages[idx]}')`;;
        heroBg.style.opacity = 1;
    }, 420);
}

// Initial image
setHeroImage(heroIndex);

// Cycle images every 4 seconds
setInterval(() => {
    heroIndex = (heroIndex + 1) % heroImages.length;
    setHeroImage(heroIndex);
}, 4000);
</script>

<?php require_once '../includes/footer.php'; ?>

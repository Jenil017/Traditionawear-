<?php
require_once '../includes/header.php';
require_once '../config/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $inquiry_type = $_POST['inquiry_type'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Validation
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($subject)) $errors[] = "Subject is required";
    if (empty($message)) $errors[] = "Message is required";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_inquiries (name, email, phone, subject, message, inquiry_type, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message, $inquiry_type, $user_id]);
            $success_message = "Thank you for contacting us! We'll get back to you within 24 hours.";
            // Clear form data after successful submission
            $_POST = [];
        } catch (PDOException $e) {
            $errors[] = "Error submitting your inquiry. Please try again.";
        }
    }
}
?>

<!-- Contact Page Title -->
<div class="row mb-4">
    <div class="col-12">
        <div class="bg-primary text-white text-center py-4 rounded">
            <h2 class="mb-2">Contact Us</h2>
            <p class="mb-0">Get in touch with Rameshwar Traditional Wear Rental</p>
        </div>
    </div>
</div>



    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content animate__animated animate__fadeInUp">
            <h1>Contact Rameshwar Traditional Wear Rental</h1>
            <p>Book Your Outfit or Ask a Query</p>
            <div class="hero-breadcrumb">
                <a href="index.php">Home</a> <span>/</span> <span>Contact Us</span>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Contact Information Section -->
        <section class="contact-info-section animate__animated animate__fadeInLeft">
            <div class="container">
                <div class="section-header">
                    <h2>Let's Discuss</h2>
                    <p class="section-subtitle">Discover the perfect traditional wear for your special day! Visit our nearby showroom for a curated collection of traditional and designer styles. Step into timeless elegance today!</p>
                </div>
                
                <div class="contact-cards">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Visit Our Store</h3>
                            <address>
                                Rameshwar Traditional Wear Rental,<br>
                                2nd Floor, Anand Libas,<br>
                                Railway Road,<br>
                                Rohtak - 124001,<br>
                                Haryana, India
                            </address>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Email Us</h3>
                            <p><a href="mailto:rtwrs@gmail.com">rtwrs@gmail.com</a></p>
                            <p><a href="mailto:support@rtwrs.com">support@rtwrs.com</a></p>
                        </div>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h3>Call Us</h3>
                            <p><a href="tel:+917988766165">+91 79887 66165</a></p>
                            <p><a href="tel:+919876543210">+91 98765 43210</a></p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="social-media">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="https://www.facebook.com/rtwrs" class="social-link facebook" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </a>
                        <a href="https://www.instagram.com/rtwrs_official/" class="social-link instagram" target="_blank">
                            <i class="fab fa-instagram"></i>
                            <span>Instagram</span>
                        </a>
                        <a href="https://api.whatsapp.com/send?phone=917988766165" class="social-link whatsapp" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                            <span>WhatsApp</span>
                        </a>
                        <a href="https://twitter.com/rtwrs" class="social-link twitter" target="_blank">
                            <i class="fab fa-twitter"></i>
                            <span>Twitter</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section class="contact-form-section animate__animated animate__fadeInRight">
            <div class="container">
                <div class="form-container">
                    <div class="form-header">
                        <h2>Send Us Your Message</h2>
                        <p class="form-subtitle">Have a question, need assistance with booking, or want to share feedback? We're here to help!</p>
                    </div>

                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success animate__animated animate__bounceIn">
                            <i class="fas fa-check-circle"></i>
                            <div class="alert-content">
                                <strong>Success!</strong>
                                <p><?php echo $success_message; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error animate__animated animate__shakeX">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div class="alert-content">
                                <strong>Please fix the following errors:</strong>
                                <ul>
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="contact-form" novalidate>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                                <div class="form-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                <div class="form-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" placeholder="+91 98765 43210">
                                <div class="form-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inquiry_type">Inquiry Type</label>
                                <select id="inquiry_type" name="inquiry_type">
                                    <option value="general" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'general') ? 'selected' : ''; ?>>General Inquiry</option>
                                    <option value="booking" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'booking') ? 'selected' : ''; ?>>Booking Inquiry</option>
                                    <option value="complaint" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'complaint') ? 'selected' : ''; ?>>Complaint</option>
                                    <option value="suggestion" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'suggestion') ? 'selected' : ''; ?>>Suggestion</option>
                                    <option value="partnership" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'partnership') ? 'selected' : ''; ?>>Partnership</option>
                                    <option value="other" <?php echo (isset($_POST['inquiry_type']) && $_POST['inquiry_type'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                                <div class="form-icon">
                                    <i class="fas fa-list"></i>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject <span class="required">*</span></label>
                            <input type="text" id="subject" name="subject" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" required placeholder="Brief description of your inquiry">
                            <div class="form-icon">
                                <i class="fas fa-tag"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message">Message <span class="required">*</span></label>
                            <textarea id="message" name="message" rows="6" required placeholder="Please provide detailed information about your inquiry..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            <div class="form-icon">
                                <i class="fas fa-comment"></i>
                            </div>
                        </div>

                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i>
                            <span>Send Message</span>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section class="map-section">
            <div class="container">
                <div class="map-header">
                    <h2>Find Us</h2>
                    <p>Visit our showroom to experience our collection firsthand</p>
                </div>
                <div class="map-container">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3484.123456789!2d76.6066!3d28.8955!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d69d9d9d9d9d9%3A0x1234567890abcdef!2sRailway%20Road%2C%20Rohtak%2C%20Haryana%20124001!5e0!3m2!1sen!2sin!4v1234567890123"
                        width="100%" 
                        height="400" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade"
                        title="RTWRS Location Map">
                    </iframe>
                </div>
                <div class="map-info">
                    <div class="map-details">
                        <div class="map-detail">
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Business Hours</strong>
                                <p>Monday - Saturday: 10:00 AM - 8:00 PM<br>Sunday: 11:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                        <div class="map-detail">
                            <i class="fas fa-car"></i>
                            <div>
                                <strong>Parking</strong>
                                <p>Free parking available<br>Easy access from main road</p>
                            </div>
                        </div>
                        <div class="map-detail">
                            <i class="fas fa-subway"></i>
                            <div>
                                <strong>Public Transport</strong>
                                <p>Near Railway Station<br>Bus stop 2 minutes walk</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="categories-section">
            <div class="container">
                <h2>Our Services</h2>
                <div class="categories-grid">
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <h3>Sherwani Sets</h3>
                        <p>Traditional and designer sherwanis for weddings and special occasions</p>
                        <a href="products.php?category=sherwani" class="category-link">View Collection</a>
                    </div>
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Indo-Western</h3>
                        <p>Modern fusion wear combining traditional and contemporary styles</p>
                        <a href="products.php?category=indo-western" class="category-link">View Collection</a>
                    </div>
                    <div class="category-card">
                        <div class="category-icon">
                            <i class="fas fa-crown"></i>
                        </div>
                        <h3>Accessories</h3>
                        <p>Complete your look with traditional accessories and jewelry</p>
                        <a href="products.php?category=accessories" class="category-link">View Collection</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <i class="fas fa-tshirt"></i>
                        <span>RTWRS</span>
                    </div>
                    <p>Your trusted partner for traditional wear rentals. Making your special occasions memorable with our exquisite collection of traditional Indian attire.</p>
                    <div class="footer-social">
                        <a href="https://www.facebook.com/rtwrs" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/rtwrs_official/" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="https://api.whatsapp.com/send?phone=917988766165" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://twitter.com/rtwrs" target="_blank"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="rent.php">Rent</a></li>
                        <li><a href="my-bookings.php">My Bookings</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul>
                        <li><a href="products.php?category=sherwani">Sherwani Rental</a></li>
                        <li><a href="products.php?category=kurta">Kurta Sets</a></li>
                        <li><a href="products.php?category=indo-western">Indo-Western</a></li>
                        <li><a href="#">Custom Fitting</a></li>
                        <li><a href="#">Home Delivery</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <div class="contact-info">
                        <p><i class="fas fa-phone"></i> +91 79887 66165</p>
                        <p><i class="fas fa-envelope"></i> rtwrs@gmail.com</p>
                        <p><i class="fas fa-map-marker-alt"></i> Railway Road, Rohtak, Haryana</p>
                        <p><i class="fas fa-clock"></i> Mon-Sat: 10AM-8PM</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Rameshwar Traditional Wear Rental. All Rights Reserved.</p>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Rental Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="../assets/js/contact.js"></script>
</body>
</html>
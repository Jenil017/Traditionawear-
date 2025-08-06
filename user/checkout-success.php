<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if checkout was successful
if (!isset($_SESSION['checkout_success'])) {
    header('Location: cart.php');
    exit;
}

$checkout_data = $_SESSION['checkout_success'];
unset($_SESSION['checkout_success']); // Clear the success data

require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="checkout-success-card">
                <div class="text-center mb-4">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="success-title">Order Placed Successfully!</h2>
                    <p class="success-subtitle">Thank you for your booking with RTWRS</p>
                </div>

                <div class="order-summary">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="summary-item">
                                <i class="fas fa-shopping-bag me-2"></i>
                                <strong>Items Booked:</strong> <?= $checkout_data['item_count'] ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="summary-item">
                                <i class="fas fa-rupee-sign me-2"></i>
                                <strong>Total Amount:</strong> ₹<?= number_format($checkout_data['total_amount'], 2) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="booking-ids">
                    <h5><i class="fas fa-receipt me-2"></i>Your Booking IDs:</h5>
                    <div class="booking-list">
                        <?php foreach ($checkout_data['booking_ids'] as $booking_id): ?>
                            <span class="booking-id-badge">#<?= $booking_id ?></span>
                        <?php endforeach; ?>
                    </div>
                    <p class="booking-note">
                        <i class="fas fa-info-circle me-1"></i>
                        Please save these booking IDs for your records. You can view your bookings in the "My Bookings" section.
                    </p>
                </div>

                <div class="next-steps">
                    <h5><i class="fas fa-list-check me-2"></i>What's Next?</h5>
                    <ul class="steps-list">
                        <li>You will receive a confirmation email shortly</li>
                        <li>Our team will contact you within 24 hours to confirm your booking</li>
                        <li>Prepare for pickup/delivery as per your selected dates</li>
                        <li>Ensure you have valid ID for verification</li>
                    </ul>
                </div>

                <div class="action-buttons text-center">
                    <a href="my-bookings.php" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-list me-2"></i>View My Bookings
                    </a>
                    <a href="products.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Continue Shopping
                    </a>
                </div>

                <div class="contact-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="contact-item">
                                <i class="fas fa-phone me-2"></i>
                                <strong>Call Us:</strong> +91 9876543210
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-item">
                                <i class="fas fa-envelope me-2"></i>
                                <strong>Email:</strong> support@rtwrs.com
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.checkout-success-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 3rem;
    margin-bottom: 2rem;
}

.success-icon {
    font-size: 4rem;
    color: #28a745;
    margin-bottom: 1rem;
}

.success-title {
    color: #8B4513;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.success-subtitle {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.order-summary {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-left: 4px solid #28a745;
}

.summary-item {
    color: #333;
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.summary-item i {
    color: #8B4513;
}

.booking-ids {
    background: #fff3cd;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-left: 4px solid #ffc107;
}

.booking-ids h5 {
    color: #8B4513;
    margin-bottom: 1rem;
}

.booking-list {
    margin-bottom: 1rem;
}

.booking-id-badge {
    background: #8B4513;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    display: inline-block;
}

.booking-note {
    color: #856404;
    font-size: 0.9rem;
    margin-bottom: 0;
}

.next-steps {
    background: #d1ecf1;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-left: 4px solid #17a2b8;
}

.next-steps h5 {
    color: #8B4513;
    margin-bottom: 1rem;
}

.steps-list {
    color: #0c5460;
    margin-bottom: 0;
}

.steps-list li {
    margin-bottom: 0.5rem;
}

.action-buttons {
    margin: 2rem 0;
}

.btn-primary {
    background: linear-gradient(135deg, #8B4513 0%, #D2691E 100%);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 69, 19, 0.3);
}

.btn-outline-secondary {
    border-color: #8B4513;
    color: #8B4513;
    padding: 0.75rem 2rem;
    font-weight: 500;
}

.btn-outline-secondary:hover {
    background-color: #8B4513;
    border-color: #8B4513;
}

.contact-info {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 1.5rem;
    border-top: 3px solid #8B4513;
}

.contact-item {
    color: #333;
    margin-bottom: 0.5rem;
}

.contact-item i {
    color: #8B4513;
}

@media (max-width: 768px) {
    .checkout-success-card {
        padding: 2rem 1rem;
    }
    
    .action-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .booking-id-badge {
        display: block;
        text-align: center;
        margin-bottom: 0.5rem;
    }
}
</style>

<?php require_once '../includes/footer.php'; ?>

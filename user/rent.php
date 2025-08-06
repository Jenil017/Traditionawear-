<?php
// Start session first
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once '../config/db.php';

// FIXED: Enable error reporting for debugging (in development)
if (defined('DEVELOPMENT') && DEVELOPMENT === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0); // Hide errors in production
}

// Check if user is logged in FIRST
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=rent.php');
    exit;
}

$user_id = $_SESSION['user_id']; // FIXED: Define user_id variable early

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    if (!isset($user_id)) {
        $message[] = 'Please Login to add products to cart';
    } else {
        $product_name = $_POST['product_name'];
        $product_id_post = $_POST['product_id'];
        $product_image = $_POST['product_image'];
        $product_price = $_POST['product_price'];
        $selected_size = $_POST['selected_size'] ?? 'M';
        $selected_color = $_POST['selected_color'] ?? 'Default';
        $rental_days = $_POST['rental_days'] ?? 1;
        $total_price = number_format($product_price * $rental_days, 2, '.', '');
        
        // Check if product already exists in cart
        $select_product = $pdo->prepare("SELECT product_id FROM cart_items WHERE user_id = ? AND product_id = ?");
        $select_product->execute([$user_id, $product_id_post]);
        
        if ($select_product->rowCount() > 0) {
            $message[] = 'This Product is already in your cart';
        } else {
            // Get additional product details
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$product_id_post]);
            $product_details = $stmt->fetch();
            
            if ($product_details) {
                $insert_cart = $pdo->prepare("INSERT INTO cart_items (product_id, user_id, product_name, product_number, category_id, description, selected_size, selected_color, available_sizes, available_colors, price_per_day, image_url, quantity, rental_days, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $result = $insert_cart->execute([
                    $product_id_post,
                    $user_id,
                    $product_name,
                    $product_details['product_number'],
                    $product_details['category_id'],
                    $product_details['description'],
                    $selected_size,
                    $selected_color,
                    $product_details['available_sizes'],
                    $product_details['available_colors'],
                    $product_price,
                    $product_image,
                    1, // quantity
                    $rental_days,
                    $total_price
                ]);
                
                if ($result) {
                    $message[] = 'Product Added To Cart Successfully';
                } else {
                    $message[] = 'Failed to add product to cart';
                }
            } else {
                $message[] = 'Product not found';
            }
        }
    }
}

// Check if user is logged in BEFORE including header
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=rent.php');
    exit;
}

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header('Location: products.php');
    exit;
}

// Include files after all header redirects are done
require_once '../includes/header.php';

// Get product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND status = 'active' AND quantity_available > 0");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$images = array_filter(array_map(function($img) {
    $img = ltrim(trim($img), '.');
    if (strpos($img, '/rtwrs_web/') !== 0) {
        $img = '/rtwrs_web/' . ltrim($img, '/');
    }
    // Check if file exists, else skip
    $filePath = $_SERVER['DOCUMENT_ROOT'] . $img;
    return (file_exists($filePath) && is_file($filePath)) ? $img : null;
}, explode(',', $product['image_url'])));
if (empty($images)) {
    $images[] = '/rtwrs_web/assets/images/placeholder.jpg'; // fallback placeholder
}
?>

<style>
.message {
  position: sticky;
  top: 0;
  margin: 0 auto;
  width: 61%;
  background-color: #fff;
  padding: 6px 9px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  z-index: 100;
  gap: 0px;
  border: 2px solid rgb(68, 203, 236);
  border-top-right-radius: 8px;
  border-bottom-left-radius: 8px;
}
.message span {
  font-size: 22px;
  color: rgb(240, 18, 18);
  font-weight: 400;
}
.message i {
  cursor: pointer;
  color: rgb(3, 227, 235);
  font-size: 15px;
}
</style>

<?php
if(isset($message)){
  foreach($message as $message){
    echo '
    <div class="message" id="messages"><span>'.$message.'</span>
    </div>
    ';
  }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="booking-form">
                <h2 class="mb-4">Book: <?= htmlspecialchars($product['product_name']) ?></h2>

                <div class="mb-3">
                    <div class="product-image-container">
                        <img id="mainProductImage" src="<?= htmlspecialchars($images[0]) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="img-fluid rounded" style="width:100%; height:auto; max-height:400px; object-fit:contain; border:1px solid #ddd;">
                    </div>
                    <?php if(count($images) > 1): ?>
                        <div class="row mt-3">
                            <?php foreach($images as $index => $img): ?>
                                <div class="col-3 mb-2">
                                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="img-fluid rounded thumbnail-image" style="width:100%; height:auto; max-height:80px; object-fit:contain; border:2px solid <?= $index === 0 ? '#007bff' : '#ddd' ?>; cursor:pointer;" onclick="changeMainImage('<?= htmlspecialchars($img) ?>', this)">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <h4 class="price-tag">₹<?= $product['price_per_day'] ?>/day</h4>
                    <p><strong>Available Sizes:</strong> <?= htmlspecialchars($product['size']) ?></p>
                    <p><strong>Available Colors:</strong> <?= htmlspecialchars($product['color']) ?></p>
                    <p><strong>Stock:</strong> <?= $product['quantity_available'] ?> available</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="booking-form">
                <h3 class="mb-4">Rental Details</h3>

                <form method="POST">
                    <!-- Hidden inputs for cart -->
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>">
                    <input type="hidden" name="product_price" value="<?= $product['price_per_day'] ?>">
                    <input type="hidden" name="product_image" value="<?= htmlspecialchars($images[0]) ?>">
                    <input type="hidden" name="rental_days" value="1" id="rental_days_hidden">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="totalPrice" class="form-label">Total Price</label>
                        <input type="text" class="form-control" id="totalPrice" readonly>
                        <small class="text-muted">For <span id="totalDays">0</span> days</small>
                    </div>

                    <div class="mb-3">
                        <label for="selected_size" class="form-label">Select Size</label>
                        <select class="form-select" id="selected_size" name="selected_size" required>
                            <option value="">Choose size...</option>
                            <?php foreach (explode(',', $product['size']) as $size): ?>
                                <option value="<?= trim($size) ?>"><?= trim($size) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="selected_color" class="form-label">Select Color</label>
                        <select class="form-select" id="selected_color" name="selected_color" required>
                            <option value="">Choose color...</option>
                            <?php foreach (explode(',', $product['color']) as $color): ?>
                                <option value="<?= trim($color) ?>"><?= trim($color) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="special_requests" class="form-label">Special Requests (Optional)</label>
                        <textarea class="form-control" id="special_requests" name="special_requests" rows="3"></textarea>
                    </div>

                    <!-- Product Condition Instructions -->
                    <div class="alert alert-info mb-3">
                        <h6><i class="fas fa-info-circle me-2"></i>Important Instructions</h6>
                        <p class="mb-2"><strong>Product Condition Responsibility:</strong></p>
                        <ul class="mb-0">
                            <li>Please inspect the product upon delivery</li>
                            <li>Report any damage or defective pieces immediately</li>
                            <li>You are responsible for any damage during rental period</li>
                            <li>Damaged or defective products will incur additional charges</li>
                        </ul>
                    </div>

                    <!-- Product Condition Agreement -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="condition_agreement" name="condition_agreement" required>
                            <label class="form-check-label" for="condition_agreement">
                                <strong>I acknowledge that I have read and understood the product condition responsibilities.*</strong>
                            </label>
                        </div>
                        <small class="text-muted">* This agreement is required to proceed with booking</small>
                    </div>

                    <h4 class="mb-3">Payment Method</h4>

                    <div class="mb-3">
                        <div class="payment-option" onclick="selectPayment('COD')">
                            <input type="radio" name="payment_method" value="COD" id="cod" required>
                            <label for="cod" class="form-label">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Cash on Delivery
                            </label>
                        </div>

                        <div class="payment-option" onclick="selectPayment('UPI')">
                            <input type="radio" name="payment_method" value="UPI" id="upi" required>
                            <label for="upi" class="form-label">
                                <i class="fas fa-mobile-alt me-2"></i>
                                UPI Payment
                            </label>
                        </div>
                    </div>

                    <div id="upiSection" class="mb-3" style="display: none;">
                        <label for="upi_id" class="form-label">UPI ID</label>
                        <input type="text" class="form-control" id="upi_id" name="upi_id" placeholder="yourname@bank">
                        <div class="text-center mt-2">
                            <img src="../assets/images/upi-qr.png" alt="UPI QR Code" style="max-width: 150px;">
                        </div>
                    </div>

                    <!-- Damage Fine Policy -->
                    <div class="alert alert-warning mb-3">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Damage Fine Policy</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Minor Damage:</strong> ₹500</p>
                                <small class="text-muted">Small tears, stains, missing buttons</small>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Major Damage:</strong> ₹1500</p>
                                <small class="text-muted">Large tears, permanent damage, missing pieces</small>
                            </div>
                        </div>
                        <hr class="my-2">
                        <p class="mb-0"><small><strong>Note:</strong> Damage charges will be assessed after product return and added to your final bill.</small></p>
                    </div>

                    <div class="booking-actions">
                        <input type="submit" name="add_to_cart" value="Add to Cart" class="btn btn-outline-primary btn-lg me-2" style="flex: 1;">
                        <input type="submit" name="book_now" value="Book Now" class="btn btn-primary btn-lg" style="flex: 1;">
                    </div>
                    
                    <div class="cart-info mt-3 text-center">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Add to cart to book multiple items together, or book now for immediate booking
                        </small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.booking-actions {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}

.booking-actions .btn {
    transition: all 0.3s ease;
}

.booking-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.cart-info {
    background: #f8f9fa;
    padding: 0.75rem;
    border-radius: 8px;
    border-left: 3px solid #8B4513;
}

@media (max-width: 768px) {
    .booking-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .booking-actions .btn {
        width: 100%;
    }
}
</style>

<script>
function selectPayment(method) {
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
    });

    event.currentTarget.classList.add('selected');
    document.getElementById(method.toLowerCase()).checked = true;
}

// Function to change main product image
function changeMainImage(imageSrc, thumbnailElement) {
    // Update main image
    document.getElementById('mainProductImage').src = imageSrc;
    
    // Update thumbnail borders
    document.querySelectorAll('.thumbnail-image').forEach(img => {
        img.style.border = '2px solid #ddd';
    });
    
    // Highlight selected thumbnail
    if (thumbnailElement) {
        thumbnailElement.style.border = '2px solid #007bff';
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>

<script>
// Auto-hide messages after 5 seconds (like demo code)
setTimeout(() => {
  const box = document.getElementById('messages');
  if (box) {
    box.style.display = 'none';
  }
}, 5000);
</script>

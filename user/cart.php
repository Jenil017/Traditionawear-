<?php
session_start();
require_once '../config/db.php';

// FIXED: Enable error reporting for debugging (in development)
if (defined('DEVELOPMENT') && DEVELOPMENT === true) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0); // Hide errors in production
}

// FIXED: Define user_id variable early
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=cart.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Remove from Cart (simple POST)
if (isset($_POST['remove_from_cart'])) {
    if (!isset($user_id)) {
        $message[] = 'Please Login to manage your cart';
    } else {
        $cart_item_id = $_POST['cart_item_id'];
        
        // Delete the cart item
        $delete_cart = $pdo->prepare("DELETE FROM cart_items WHERE id = ? AND user_id = ?");
        $result = $delete_cart->execute([$cart_item_id, $user_id]);
        
        if ($result) {
            $message[] = 'Item removed from cart successfully';
        } else {
            $message[] = 'Failed to remove item from cart';
        }
    }
}



require_once '../includes/header.php';

// Fetch cart items for the user
try {
    $stmt = $pdo->prepare("SELECT ci.* 
                          FROM cart_items ci 
                          WHERE ci.user_id = ? 
                          ORDER BY ci.added_at DESC");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    // Calculate totals
    $total_items = count($cart_items);
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['subtotal'];
    }
    $gst = $subtotal * 0.18; // 18% GST
    $final_total = $subtotal + $gst;
    
} catch (Exception $e) {
    error_log('Cart Display Error: ' . $e->getMessage());
    $cart_items = [];
    $total_items = 0;
    $subtotal = $gst = $final_total = 0;
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

<link rel="stylesheet" href="../assets/css/cart.css">

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-cart me-2"></i>My Cart</h2>
                <a href="products.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
            
            <?php if (empty($cart_items)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
                    <h4>Your cart is empty</h4>
                    <p class="text-muted">Add some products to get started!</p>
                    <a href="products.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="cart-items">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="card mb-3">
                                    <div class="row g-0">
                                        <div class="col-md-3">
                                            <?php 
                                            $image_url = $item['image_url'];
                                            if ($image_url && !str_starts_with($image_url, 'http')) {
                                                $image_url = '../' . ltrim($image_url, '/');
                                            }
                                            if (!$image_url) {
                                                $image_url = '../assets/images/placeholder.jpg';
                                            }
                                            ?>
                                            <img src="<?= htmlspecialchars($image_url) ?>" 
                                                 class="img-fluid rounded-start h-100 object-fit-cover" 
                                                 alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                 style="min-height: 200px;">
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <h5 class="card-title"><?= htmlspecialchars($item['product_name']) ?></h5>
                                                        <p class="text-muted mb-1">
                                                            <small>Traditional Wear</small>
                                                        </p>
                                                        <p class="text-muted mb-2">
                                                            Product #: <?= htmlspecialchars($item['product_number']) ?>
                                                        </p>
                                                    </div>
                                                    <div class="text-end">
                                                        <h5 class="text-primary">₹<?= number_format($item['subtotal'], 2) ?></h5>
                                                        <small class="text-muted">₹<?= number_format($item['price_per_day'], 2) ?>/day</small>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mt-3">
                                                    <div class="col-sm-6">
                                                        <p class="mb-1"><strong>Size:</strong> <?= htmlspecialchars($item['selected_size']) ?></p>
                                                        <p class="mb-1"><strong>Color:</strong> <?= htmlspecialchars($item['selected_color']) ?></p>
                                                        <p class="mb-1"><strong>Rental Days:</strong> <?= $item['rental_days'] ?> days</p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <?php if ($item['start_date'] && $item['end_date']): ?>
                                                            <p class="mb-1"><strong>Start Date:</strong> <?= date('d M Y', strtotime($item['start_date'])) ?></p>
                                                            <p class="mb-1"><strong>End Date:</strong> <?= date('d M Y', strtotime($item['end_date'])) ?></p>
                                                        <?php endif; ?>
                                                        <p class="mb-1"><strong>Payment:</strong> <?= htmlspecialchars($item['payment_method'] ?? 'COD') ?></p>
                                                    </div>
                                                </div>
                                                
                                                <?php if ($item['special_requests']): ?>
                                                    <div class="mt-2">
                                                        <p class="mb-1"><strong>Special Requests:</strong></p>
                                                        <p class="text-muted small"><?= htmlspecialchars($item['special_requests']) ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="mt-3">
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="cart_item_id" value="<?= $item['id'] ?>">
                                                        <input type="submit" name="remove_from_cart" value="Remove" class="btn btn-outline-danger btn-sm"
                                                                onclick="return confirm('Are you sure you want to remove this item from cart?')">
                                                    </form>
                                                    <a href="rent.php?id=<?= $item['product_id'] ?>" 
                                                       class="btn btn-outline-primary btn-sm ms-2">
                                                        <i class="fas fa-edit me-1"></i>Edit Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="cart-summary">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Items (<?= $total_items ?>)</span>
                                        <span>₹<?= number_format($subtotal, 2) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>GST (18%)</span>
                                        <span>₹<?= number_format($gst, 2) ?></span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Total</strong>
                                        <strong class="text-primary">₹<?= number_format($final_total, 2) ?></strong>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="checkout.php" class="btn btn-primary btn-lg">
                                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                        </a>
                                        <a href="products.php" class="btn btn-outline-secondary">
                                            <i class="fas fa-plus me-2"></i>Add More Items
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-body">
                                    <h6><i class="fas fa-info-circle me-2"></i>Important Notes</h6>
                                    <ul class="small text-muted mb-0">
                                        <li>All prices include GST</li>
                                        <li>Rental period starts from pickup date</li>
                                        <li>Late return charges may apply</li>
                                        <li>Damage charges as per policy</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto-hide messages after 5 seconds (like demo code)
setTimeout(() => {
  const box = document.getElementById('messages');
  if (box) {
    box.style.display = 'none';
  }
}, 5000);
</script>

<?php include '../includes/footer.php'; ?>

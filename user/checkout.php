<?php
session_start();
require_once '../config/db.php';
// Fetch cart items from database
try {
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ? ORDER BY added_at DESC");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    // Check if cart is not empty
    if (empty($cart_items)) {
        $_SESSION['cart_message'] = 'Your cart is empty. Please add some items before checkout.';
        header('Location: cart.php');
        exit;
    }
    
} catch (Exception $e) {
    $_SESSION['cart_message'] = 'Error loading cart items: ' . $e->getMessage();
    header('Location: cart.php');
    exit;
}

$booking_ids = [];
$total_amount = 0;
$gst_rate = 0.18;

try {
    $pdo->beginTransaction();
    
    // Process each cart item as a separate booking
    foreach ($cart_items as $item) {
        // Calculate GST (use subtotal from cart_items table)
        $subtotal = $item['subtotal'] ?? ($item['price_per_day'] * $item['rental_days']);
        $gst_amount = $subtotal * $gst_rate;
        $final_total = $subtotal + $gst_amount;
        $total_amount += $final_total;
        
        // Insert booking
        $stmt = $pdo->prepare("
            INSERT INTO bookings (
                user_id, product_id, start_date, end_date, 
                selected_size, selected_color, payment_method, 
                upi_id, special_requests, total_price, 
                gst_amount, final_total, status, booking_date
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        
        $stmt->execute([
            $user_id,
            $item['product_id'],
            $item['start_date'],
            $item['end_date'],
            $item['selected_size'],
            $item['selected_color'],
            $item['payment_method'] ?? 'COD',
            $item['upi_id'],
            $item['special_requests'],
            $subtotal,
            $gst_amount,
            $final_total
        ]);
        
        $booking_id = $pdo->lastInsertId();
        $booking_ids[] = $booking_id;
        
        // Update product quantity (optional - you may want to reserve instead)
        $stmt = $pdo->prepare("UPDATE products SET quantity_available = quantity_available - 1 WHERE id = ?");
        $stmt->execute([$item['product_id']]);
    }
    
    $pdo->commit();
    
    // Clear the cart after successful checkout (from database)
    $clear_stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $clear_stmt->execute([$user_id]);
    
    // Set success message
    $_SESSION['checkout_success'] = [
        'booking_ids' => $booking_ids,
        'total_amount' => $total_amount,
        'item_count' => count($cart_items)
    ];
    
    // Redirect to success page
    header('Location: checkout-success.php');
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    
    // Set error message and redirect back to cart
    $_SESSION['cart_message'] = 'Checkout failed: ' . $e->getMessage();
    header('Location: cart.php');
    exit;
}
?>

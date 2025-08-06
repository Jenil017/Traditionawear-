<?php
session_start();
require_once '../config/db.php';

echo "<h2>Detailed Checkout Debug - Finding the Exception</h2>";

if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❌ Please login first</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items and show ALL fields
echo "<h3>1. Cart Items with ALL Fields</h3>";
try {
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ? ORDER BY added_at DESC");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    if (empty($cart_items)) {
        echo "<p style='color: red;'>❌ No cart items found!</p>";
        exit;
    }
    
    echo "<p>Found " . count($cart_items) . " cart items:</p>";
    
    foreach ($cart_items as $i => $item) {
        echo "<h4>Item " . ($i + 1) . ": " . htmlspecialchars($item['product_name']) . "</h4>";
        echo "<table border='1'>";
        foreach ($item as $field => $value) {
            $display_value = ($value === null) ? '<span style="color: red;">NULL</span>' : htmlspecialchars($value);
            echo "<tr><td><strong>$field</strong></td><td>$display_value</td></tr>";
        }
        echo "</table><br>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fetching cart items: " . $e->getMessage() . "</p>";
    exit;
}

// Test the exact INSERT query that checkout.php uses
echo "<h3>2. Testing Checkout INSERT Query</h3>";

$booking_ids = [];
$total_amount = 0;
$gst_rate = 0.18;

try {
    $pdo->beginTransaction();
    
    foreach ($cart_items as $item) {
        echo "<h4>Processing: " . htmlspecialchars($item['product_name']) . "</h4>";
        
        // Calculate GST (same logic as checkout.php)
        $subtotal = $item['subtotal'] ?? ($item['price_per_day'] * $item['rental_days']);
        $gst_amount = $subtotal * $gst_rate;
        $final_total = $subtotal + $gst_amount;
        $total_amount += $final_total;
        
        echo "<p>Subtotal: ₹$subtotal, GST: ₹$gst_amount, Final: ₹$final_total</p>";
        
        // Show the exact values that will be inserted
        $insert_values = [
            'user_id' => $user_id,
            'product_id' => $item['product_id'],
            'start_date' => $item['start_date'],
            'end_date' => $item['end_date'],
            'selected_size' => $item['selected_size'],
            'selected_color' => $item['selected_color'],
            'payment_method' => $item['payment_method'] ?? 'COD',
            'upi_id' => $item['upi_id'],
            'special_requests' => $item['special_requests'],
            'subtotal' => $subtotal,
            'gst_amount' => $gst_amount,
            'final_total' => $final_total
        ];
        
        echo "<p><strong>Values to insert into bookings table:</strong></p>";
        echo "<table border='1'>";
        foreach ($insert_values as $field => $value) {
            $display_value = ($value === null) ? '<span style="color: red;">NULL</span>' : htmlspecialchars($value);
            $warning = '';
            
            // Check for potential issues
            if ($value === null && in_array($field, ['product_id', 'start_date', 'end_date'])) {
                $warning = ' <span style="color: red;">⚠️ REQUIRED FIELD IS NULL!</span>';
            }
            
            echo "<tr><td><strong>$field</strong></td><td>$display_value$warning</td></tr>";
        }
        echo "</table>";
        
        // Try the actual INSERT
        echo "<p><strong>Testing INSERT...</strong></p>";
        try {
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
            echo "<p style='color: green;'>✅ INSERT successful! Booking ID: $booking_id</p>";
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ INSERT FAILED: " . $e->getMessage() . "</p>";
            echo "<p><strong>This is why checkout.php redirects to cart.php!</strong></p>";
            $pdo->rollBack();
            break;
        }
        
        echo "<hr>";
    }
    
    $pdo->rollBack(); // Don't actually commit, this is just a test
    echo "<p style='color: blue;'>ℹ️ Transaction rolled back (this was just a test)</p>";
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo "<p style='color: red;'>❌ Overall transaction failed: " . $e->getMessage() . "</p>";
}

echo "<br><br>";
echo "<h3>Quick Actions:</h3>";
echo "<a href='checkout.php'>Try Real Checkout</a> | ";
echo "<a href='cart.php'>Back to Cart</a>";
?>

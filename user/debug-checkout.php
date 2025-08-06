<?php
session_start();
require_once '../config/db.php';

echo "<h2>Checkout Debug - Finding the Redirect Issue</h2>";

// Check if user is logged in
echo "<h3>1. User Login Check</h3>";
if (!isset($_SESSION['user_id'])) {
    echo "<p style='color: red;'>❌ USER NOT LOGGED IN! This would cause redirect to login.php</p>";
    echo "<p><a href='login.php'>Please login first</a></p>";
    exit;
} else {
    $user_id = $_SESSION['user_id'];
    echo "<p style='color: green;'>✅ User is logged in (ID: $user_id)</p>";
}

// Check database connection
echo "<h3>2. Database Connection</h3>";
try {
    $stmt = $pdo->query("SELECT 1");
    echo "<p style='color: green;'>✅ Database connection working</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// Test the exact checkout.php cart fetch logic
echo "<h3>3. Testing Checkout Cart Fetch Logic</h3>";
try {
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ? ORDER BY added_at DESC");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    echo "<p>Cart items found: <strong>" . count($cart_items) . "</strong></p>";
    
    if (empty($cart_items)) {
        echo "<p style='color: red;'>❌ CART IS EMPTY - This is why checkout.php redirects back to cart.php!</p>";
        echo "<p>The checkout.php logic says: if cart is empty, redirect to cart.php</p>";
        
        // Check if there are ANY cart items in the database
        $all_stmt = $pdo->query("SELECT user_id, product_name, added_at FROM cart_items ORDER BY added_at DESC LIMIT 5");
        $all_items = $all_stmt->fetchAll();
        
        if (empty($all_items)) {
            echo "<p style='color: orange;'>⚠️ No cart items exist in database at all!</p>";
            echo "<p><strong>Solution:</strong> Add some products to cart first, then try checkout.</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Cart items exist for other users but not for current user:</p>";
            echo "<table border='1'>";
            echo "<tr><th>User ID</th><th>Product Name</th><th>Added At</th></tr>";
            foreach ($all_items as $item) {
                $highlight = ($item['user_id'] == $user_id) ? 'background-color: yellow;' : '';
                echo "<tr style='$highlight'>";
                echo "<td>" . $item['user_id'] . "</td>";
                echo "<td>" . htmlspecialchars($item['product_name']) . "</td>";
                echo "<td>" . $item['added_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<p><strong>Solution:</strong> Add products to cart for current user (ID: $user_id).</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Cart items found! Checkout should work:</p>";
        echo "<table border='1'>";
        echo "<tr><th>Product Name</th><th>Size</th><th>Color</th><th>Price</th><th>Added At</th></tr>";
        foreach ($cart_items as $item) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($item['product_name']) . "</td>";
            echo "<td>" . htmlspecialchars($item['selected_size']) . "</td>";
            echo "<td>" . htmlspecialchars($item['selected_color']) . "</td>";
            echo "<td>₹" . number_format($item['subtotal'], 2) . "</td>";
            echo "<td>" . $item['added_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p style='color: green;'>✅ Checkout.php should NOT redirect with these items!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error in checkout cart fetch: " . $e->getMessage() . "</p>";
}

echo "<br><br>";
echo "<h3>Quick Actions:</h3>";
echo "<a href='rent.php?id=1'>Add Product to Cart</a> | ";
echo "<a href='cart.php'>View Cart</a> | ";
echo "<a href='checkout.php'>Try Checkout Again</a>";
?>

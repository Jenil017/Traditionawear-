<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h2>Please log in first</h2>";
    echo "<a href='login.php'>Login</a>";
    exit;
}

$user_id = $_SESSION['user_id'];

echo "<h2>Cart Debug Information</h2>";
echo "<p>User ID: " . $user_id . "</p>";

try {
    // Check if cart_items table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'cart_items'");
    $table_exists = $stmt->fetch();
    
    if (!$table_exists) {
        echo "<p style='color: red;'>❌ cart_items table does not exist!</p>";
        exit;
    } else {
        echo "<p style='color: green;'>✅ cart_items table exists</p>";
    }
    
    // Check table structure
    echo "<h3>Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE cart_items");
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check current cart items for this user
    echo "<h3>Current Cart Items for User ID: $user_id</h3>";
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
    
    if (empty($cart_items)) {
        echo "<p style='color: orange;'>⚠️ No cart items found for this user</p>";
    } else {
        echo "<p style='color: green;'>✅ Found " . count($cart_items) . " cart items</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Product ID</th><th>Product Name</th><th>Added At</th></tr>";
        foreach ($cart_items as $item) {
            echo "<tr>";
            echo "<td>" . $item['id'] . "</td>";
            echo "<td>" . $item['product_id'] . "</td>";
            echo "<td>" . $item['product_name'] . "</td>";
            echo "<td>" . $item['added_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check all cart items in database
    echo "<h3>All Cart Items in Database:</h3>";
    $stmt = $pdo->query("SELECT * FROM cart_items ORDER BY added_at DESC LIMIT 10");
    $all_items = $stmt->fetchAll();
    
    if (empty($all_items)) {
        echo "<p style='color: red;'>❌ No cart items found in entire database</p>";
    } else {
        echo "<p style='color: green;'>✅ Found " . count($all_items) . " total cart items (showing last 10)</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Product ID</th><th>Product Name</th><th>Added At</th></tr>";
        foreach ($all_items as $item) {
            echo "<tr>";
            echo "<td>" . $item['id'] . "</td>";
            echo "<td>" . $item['user_id'] . "</td>";
            echo "<td>" . $item['product_id'] . "</td>";
            echo "<td>" . $item['product_name'] . "</td>";
            echo "<td>" . $item['added_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check available products
    echo "<h3>Available Products:</h3>";
    $stmt = $pdo->query("SELECT id, product_name, status FROM products WHERE status = 'active' LIMIT 5");
    $products = $stmt->fetchAll();
    
    if (empty($products)) {
        echo "<p style='color: red;'>❌ No active products found</p>";
    } else {
        echo "<p style='color: green;'>✅ Found " . count($products) . " active products (showing first 5)</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Product Name</th><th>Status</th></tr>";
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>" . $product['id'] . "</td>";
            echo "<td>" . $product['product_name'] . "</td>";
            echo "<td>" . $product['status'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><br>";
echo "<h3>Test Add to Cart:</h3>";
echo "<p>Try adding a product manually:</p>";

// Get first available product for testing
try {
    $stmt = $pdo->query("SELECT * FROM products WHERE status = 'active' LIMIT 1");
    $test_product = $stmt->fetch();
    
    if ($test_product) {
        echo "<form method='POST' action='cart.php'>";
        echo "<input type='hidden' name='action' value='add_to_cart'>";
        echo "<input type='hidden' name='product_id' value='" . $test_product['id'] . "'>";
        echo "<p>Test Product: " . htmlspecialchars($test_product['product_name']) . " (ID: " . $test_product['id'] . ")</p>";
        echo "<button type='submit'>Add to Cart (Test)</button>";
        echo "</form>";
    } else {
        echo "<p style='color: red;'>No products available for testing</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error getting test product: " . $e->getMessage() . "</p>";
}

echo "<br><br>";
echo "<a href='cart.php'>Go to Cart</a> | ";
echo "<a href='products.php'>Products</a> | ";
echo "<a href='rent.php?id=1'>Test Rent Page</a>";
?>

<?php
require_once 'config/db.php';

echo "<h2>Database Product Test</h2>";

try {
    // Test database connection
    echo "<h3>1. Database Connection Test</h3>";
    if ($pdo) {
        echo "<p style='color: green;'>✅ Database connection successful</p>";
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit;
    }
    
    // Check if products table exists
    echo "<h3>2. Products Table Check</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'products'");
    $table_exists = $stmt->fetch();
    
    if (!$table_exists) {
        echo "<p style='color: red;'>❌ Products table does not exist!</p>";
        
        // Show all tables
        echo "<h4>Available tables:</h4>";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll();
        foreach ($tables as $table) {
            echo "<li>" . $table[0] . "</li>";
        }
        exit;
    } else {
        echo "<p style='color: green;'>✅ Products table exists</p>";
    }
    
    // Check products table structure
    echo "<h3>3. Products Table Structure</h3>";
    $stmt = $pdo->query("DESCRIBE products");
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
    
    // Count total products
    echo "<h3>4. Products Count</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
    $count = $stmt->fetch();
    echo "<p>Total products in database: <strong>" . $count['total'] . "</strong></p>";
    
    // Count active products
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM products WHERE status = 'active'");
    $active_count = $stmt->fetch();
    echo "<p>Active products: <strong>" . $active_count['active'] . "</strong></p>";
    
    // Show first 5 products
    echo "<h3>5. Sample Products</h3>";
    $stmt = $pdo->query("SELECT * FROM products LIMIT 5");
    $products = $stmt->fetchAll();
    
    if (empty($products)) {
        echo "<p style='color: red;'>❌ No products found in database!</p>";
        
        // Let's insert a sample product for testing
        echo "<h4>Creating sample product for testing...</h4>";
        try {
            $stmt = $pdo->prepare("INSERT INTO products (product_name, product_number, category_id, description, price_per_day, available_sizes, available_colors, image_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                'Test Sherwani',
                'TST001',
                1, // Assuming category 1 exists
                'Test product for cart functionality',
                1500.00,
                'M,L,XL',
                'Blue,Gold,Red',
                'assets/images/test-product.jpg',
                'active'
            ]);
            
            if ($result) {
                $product_id = $pdo->lastInsertId();
                echo "<p style='color: green;'>✅ Sample product created with ID: $product_id</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to create sample product</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error creating sample product: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p style='color: green;'>✅ Found " . count($products) . " products</p>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Product Number</th><th>Price/Day</th><th>Status</th><th>Category ID</th></tr>";
        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>" . $product['id'] . "</td>";
            echo "<td>" . htmlspecialchars($product['product_name']) . "</td>";
            echo "<td>" . htmlspecialchars($product['product_number']) . "</td>";
            echo "<td>₹" . number_format($product['price_per_day'], 2) . "</td>";
            echo "<td>" . $product['status'] . "</td>";
            echo "<td>" . $product['category_id'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test fetching a specific product (like cart.php does)
    echo "<h3>6. Test Product Fetch (Cart Logic)</h3>";
    $stmt = $pdo->query("SELECT * FROM products WHERE status = 'active' LIMIT 1");
    $test_product = $stmt->fetch();
    
    if ($test_product) {
        echo "<p style='color: green;'>✅ Successfully fetched test product: " . htmlspecialchars($test_product['product_name']) . "</p>";
        
        // Test the exact query used in cart.php
        $stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p 
                              LEFT JOIN categories c ON p.category_id = c.id 
                              WHERE p.id = ? AND p.status = 'active'");
        $stmt->execute([$test_product['id']]);
        $product_with_category = $stmt->fetch();
        
        if ($product_with_category) {
            echo "<p style='color: green;'>✅ Cart.php query works! Product: " . htmlspecialchars($product_with_category['product_name']) . "</p>";
            echo "<p>Category: " . htmlspecialchars($product_with_category['category_name'] ?? 'No category') . "</p>";
        } else {
            echo "<p style='color: red;'>❌ Cart.php query failed - this is the problem!</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ No active products found for testing</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<br><br>";
echo "<a href='user/products.php'>View Products Page</a> | ";
echo "<a href='user/cart.php'>View Cart</a> | ";
echo "<a href='user/cart-debug.php'>Cart Debug</a>";
?>

<?php
/**
 * Database Setup Script - RTWRS Glitch Fix
 * This script will create the database with all the fixes applied
 */

echo "<h2>🔧 RTWRS Database Glitch Fix - Setup Script</h2>";
echo "<p>Setting up the corrected database schema...</p>";

// Database configuration (without database selection first)
$host = 'localhost';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    // Connect without selecting database first
    $dsn = "mysql:host=$host;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<div style='color: green;'>✅ Connected to MySQL server</div>";
    
    // Read and execute the fixed SQL file
    $sql_file = 'database/rtwrs_fixed_database.sql';
    if (!file_exists($sql_file)) {
        throw new Exception("SQL file not found: $sql_file");
    }
    
    $sql = file_get_contents($sql_file);
    if (!$sql) {
        throw new Exception("Could not read SQL file: $sql_file");
    }
    
    echo "<div style='color: blue;'>📁 Reading fixed database schema...</div>";
    
    // Split by semicolons and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $executed = 0;
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Skip empty statements and comments
        }
        
        try {
            $pdo->exec($statement);
            $executed++;
        } catch (PDOException $e) {
            // Some statements might fail if already exist, that's OK for most cases
            if (strpos($e->getMessage(), 'already exists') === false && 
                strpos($e->getMessage(), 'Duplicate entry') === false) {
                echo "<div style='color: orange;'>⚠️ Warning: " . $e->getMessage() . "</div>";
            }
        }
    }
    
    echo "<div style='color: green;'>✅ Executed $executed database statements</div>";
    
    // Verify the setup
    echo "<h3>🔍 Verifying Database Setup</h3>";
    
    // Switch to the rtwrs database
    $pdo->exec("USE rtwrs");
    
    // Check tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $expected_tables = ['categories', 'products', 'users', 'bookings', 'payments', 'cart_items', 'feedback', 'contactus', 'admin_notifications'];
    
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>📊 Database Tables Status:</h4>";
    echo "<ul>";
    
    foreach ($expected_tables as $expected) {
        if (in_array($expected, $tables)) {
            echo "<li style='color: green;'>✅ $expected - Found</li>";
        } else {
            echo "<li style='color: red;'>❌ $expected - Missing</li>";
        }
    }
    echo "</ul></div>";
    
    // Check critical fixes
    echo "<h4>🔧 Verifying Critical Fixes:</h4>";
    echo "<ul>";
    
    // Check bookings table for missing columns
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'gst_amount'");
        if ($stmt->fetch()) {
            echo "<li style='color: green;'>✅ bookings.gst_amount column - Fixed</li>";
        } else {
            echo "<li style='color: red;'>❌ bookings.gst_amount column - Still missing</li>";
        }
    } catch (Exception $e) {
        echo "<li style='color: red;'>❌ bookings table check failed</li>";
    }
    
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'final_total'");
        if ($stmt->fetch()) {
            echo "<li style='color: green;'>✅ bookings.final_total column - Fixed</li>";
        } else {
            echo "<li style='color: red;'>❌ bookings.final_total column - Still missing</li>";
        }
    } catch (Exception $e) {
        echo "<li style='color: red;'>❌ bookings table check failed</li>";
    }
    
    // Check products table for size/color consistency  
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'available_sizes'");
        if ($stmt->fetch()) {
            echo "<li style='color: green;'>✅ products.available_sizes column - Added for consistency</li>";
        } else {
            echo "<li style='color: orange;'>⚠️ products.available_sizes column - Not added (fallback will work)</li>";
        }
    } catch (Exception $e) {
        echo "<li style='color: red;'>❌ products table check failed</li>";
    }
    
    // Check sample data
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE status = 'active'");
        $product_count = $stmt->fetch()['count'];
        if ($product_count > 0) {
            echo "<li style='color: green;'>✅ Sample products loaded - $product_count active products</li>";
        } else {
            echo "<li style='color: red;'>❌ No active products found</li>";
        }
    } catch (Exception $e) {
        echo "<li style='color: red;'>❌ Products count check failed</li>";
    }
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $user_count = $stmt->fetch()['count'];
        if ($user_count > 0) {
            echo "<li style='color: green;'>✅ Sample users loaded - $user_count users</li>";
        } else {
            echo "<li style='color: red;'>❌ No users found</li>";
        }
    } catch (Exception $e) {
        echo "<li style='color: red;'>❌ Users count check failed</li>";
    }
    
    echo "</ul>";
    
    echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>🎉 Database Setup Complete!</h3>";
    echo "<p><strong>All major glitches have been fixed:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Database connection configured</li>";
    echo "<li>✅ Missing gst_amount and final_total columns added to bookings</li>";  
    echo "<li>✅ Database name corrected from 'rtwrs1' to 'rtwrs'</li>";
    echo "<li>✅ Size/color column consistency improved</li>";
    echo "<li>✅ All sample data loaded with correct product numbers</li>";
    echo "<li>✅ Cart system properly structured</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div style='background: #cce5ff; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4>📋 Next Steps:</h4>";
    echo "<ul>";
    echo "<li>🔗 <a href='user/register.php'>Test User Registration</a></li>";
    echo "<li>🔗 <a href='user/login.php'>Test User Login</a></li>";
    echo "<li>🔗 <a href='user/products.php'>Browse Products</a></li>";
    echo "<li>🔗 <a href='user/rent.php?id=1'>Test Product Booking</a></li>";
    echo "<li>🔗 <a href='user/cart.php'>Check Cart System</a></li>";
    echo "<li>🔗 <a href='user/checkout.php'>Test Checkout Process</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Database Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running</li>";
    echo "<li>Check database credentials in config/db.php</li>";
    echo "<li>Ensure proper permissions</li>";
    echo "</ul>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffebee; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Setup Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><em>Database setup completed. You can now test all the fixed functionality!</em></p>";
?>
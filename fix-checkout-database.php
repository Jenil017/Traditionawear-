<?php
require_once 'config/db.php';

echo "<h2>Fixing Checkout Database Issues</h2>";

try {
    // Fix 1: Add gst_amount column to bookings table if it doesn't exist
    echo "<h3>1. Adding gst_amount column to bookings table</h3>";
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'gst_amount'");
    $column_exists = $stmt->fetch();
    
    if (!$column_exists) {
        $pdo->exec("ALTER TABLE bookings ADD COLUMN gst_amount DECIMAL(10,2) DEFAULT 0.00 AFTER total_price");
        echo "<p style='color: green;'>✅ Added gst_amount column to bookings table</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ gst_amount column already exists</p>";
    }
    
    // Fix 2: Check if final_total column exists (might be needed too)
    echo "<h3>2. Checking final_total column</h3>";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'final_total'");
    $final_total_exists = $stmt->fetch();
    
    if (!$final_total_exists) {
        $pdo->exec("ALTER TABLE bookings ADD COLUMN final_total DECIMAL(10,2) DEFAULT 0.00 AFTER gst_amount");
        echo "<p style='color: green;'>✅ Added final_total column to bookings table</p>";
    } else {
        echo "<p style='color: blue;'>ℹ️ final_total column already exists</p>";
    }
    
    // Show current bookings table structure
    echo "<h3>3. Current bookings table structure</h3>";
    $stmt = $pdo->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll();
    
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='color: green;'>✅ Database schema fixes completed!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error fixing database: " . $e->getMessage() . "</p>";
}

echo "<br><br>";
echo "<h3>Next Steps:</h3>";
echo "<p>1. Database schema is now fixed</p>";
echo "<p>2. Need to fix the cart system to include start_date and end_date</p>";
echo "<p>3. Then checkout should work properly</p>";

echo "<br>";
echo "<a href='user/debug-checkout-detailed.php'>Test Checkout Again</a> | ";
echo "<a href='user/checkout.php'>Try Real Checkout</a>";
?>

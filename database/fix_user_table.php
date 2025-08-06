<?php
/**
 * Database Fix Script for Users Table
 * This script will fix the users table structure and data to match the new requirements
 */

require_once '../config/db.php';

echo "<h2>Users Table Fix Script</h2>";
echo "<p>Fixing users table structure and data...</p>";

try {
    // Check current table structure
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Current Table Structure:</h3>";
    echo "<ul>";
    foreach ($columns as $column) {
        echo "<li>$column</li>";
    }
    echo "</ul>";
    
    $user_name_exists = in_array('user_name', $columns);
    
    if (!$user_name_exists) {
        echo "<h3>Step 1: Adding user_name column...</h3>";
        $pdo->exec("ALTER TABLE users ADD COLUMN user_name VARCHAR(25) NOT NULL DEFAULT ''");
        echo "<p>✅ Added user_name column</p>";
        
        // Update existing users with default usernames
        echo "<h3>Step 2: Updating existing users with usernames...</h3>";
        $stmt = $pdo->query("SELECT id, name, email FROM users WHERE user_name = ''");
        $users = $stmt->fetchAll();
        
        foreach ($users as $user) {
            // Generate username from name (lowercase, no spaces)
            $username = strtolower(str_replace(' ', '', $user['name']));
            // Add number if username exists
            $counter = 1;
            $original_username = $username;
            
            while (true) {
                $check_stmt = $pdo->prepare("SELECT id FROM users WHERE user_name = ? AND id != ?");
                $check_stmt->execute([$username, $user['id']]);
                if (!$check_stmt->fetch()) {
                    break;
                }
                $username = $original_username . $counter;
                $counter++;
            }
            
            $update_stmt = $pdo->prepare("UPDATE users SET user_name = ? WHERE id = ?");
            $update_stmt->execute([$username, $user['id']]);
            echo "<p>Updated user '{$user['name']}' with username: $username</p>";
        }
    }
    
    // Add unique constraint if it doesn't exist
    echo "<h3>Step 3: Adding unique constraints...</h3>";
    try {
        $pdo->exec("ALTER TABLE users ADD UNIQUE KEY `user_name` (`user_name`)");
        echo "<p>✅ Added unique constraint for user_name</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "<p>ℹ️ Unique constraint for user_name already exists</p>";
        } else {
            echo "<p>⚠️ Could not add unique constraint: " . $e->getMessage() . "</p>";
        }
    }
    
    // Show final table structure
    echo "<h3>Final Table Structure:</h3>";
    $stmt = $pdo->query("DESCRIBE users");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th style='padding: 8px;'>Column</th><th style='padding: 8px;'>Type</th><th style='padding: 8px;'>Null</th><th style='padding: 8px;'>Key</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . $row['Field'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['Type'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['Null'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show sample users
    echo "<h3>Current Users:</h3>";
    $stmt = $pdo->query("SELECT id, user_name, name, email FROM users LIMIT 10");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th style='padding: 8px;'>ID</th><th style='padding: 8px;'>Username</th><th style='padding: 8px;'>Name</th><th style='padding: 8px;'>Email</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td style='padding: 8px;'>" . $row['id'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['user_name'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['name'] . "</td>";
        echo "<td style='padding: 8px;'>" . $row['email'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div style='color: green; padding: 10px; border: 1px solid green; margin: 10px 0;'>";
    echo "🎉 <strong>Database fix completed successfully!</strong><br>";
    echo "Your users table now supports username/email login.";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; margin: 10px 0;'>";
    echo "❌ <strong>Error:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>✅ Database structure fixed</li>";
echo "<li>🔗 <a href='../user/register.php'>Test Registration</a></li>";
echo "<li>🔗 <a href='../user/login.php'>Test Login</a></li>";
echo "</ul>";

echo "<p><em>You can safely delete this fix script after running it successfully.</em></p>";
?>

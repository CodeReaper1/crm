<?php
// Include functions file for database configuration
require_once 'functions.php';

// Test database connection and check tables
echo "<h1>Database Tables Check</h1>";

try {
    // Try connecting to the database
    $conn = connectDB();
    
    if ($conn) {
        echo "<p style='color: green;'>Connected to database successfully!</p>";
        echo "<p>Database Host: " . DB_HOST . "</p>";
        echo "<p>Database User: " . DB_USER . "</p>";
        echo "<p>Database Name: " . DB_NAME . "</p>";
        
        // Check if tables exist
        $tables = ['users', 'leads', 'user_activity'];
        echo "<h2>Table Status:</h2>";
        echo "<ul>";
        
        foreach ($tables as $table) {
            $result = $conn->query("SHOW TABLES LIKE '$table'");
            if ($result->num_rows > 0) {
                echo "<li style='color: green;'>Table '$table' exists</li>";
                
                // Show table structure
                echo "<ul>";
                $columns = $conn->query("SHOW COLUMNS FROM $table");
                while ($column = $columns->fetch_assoc()) {
                    echo "<li>" . $column['Field'] . " (" . $column['Type'] . ")</li>";
                }
                echo "</ul>";
            } else {
                echo "<li style='color: red;'>Table '$table' does not exist</li>";
            }
        }
        
        echo "</ul>";
        
        $conn->close();
    } else {
        echo "<p style='color: red;'>Failed to connect to database.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>

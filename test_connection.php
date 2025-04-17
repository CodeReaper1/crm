<?php
// Include functions file for database configuration
require_once 'functions.php';
require_once 'debug_functions.php';

// Test database connection
echo "<h1>Database Connection Test</h1>";

try {
    // Try connecting to the database
    $conn = connectDB();

    if ($conn) {
        echo "<p style='color: green;'>Connected to database successfully!</p>";
        echo "<p>Database Host: " . DB_HOST . "</p>";
        echo "<p>Database User: " . DB_USER . "</p>";
        echo "<p>Database Name: " . DB_NAME . "</p>";

        // Check if we can query the database
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "<h2>Tables in database:</h2>";
            echo "<ul>";
            $found_activities_table = false;
            while ($row = $result->fetch_row()) {
                echo "<li>" . $row[0] . "</li>";
                if ($row[0] === 'user_activities') {
                    $found_activities_table = true;
                }
            }
            echo "</ul>";

            // Specific check for user_activities table
            if ($found_activities_table) {
                echo "<h2>User Activities Table Check</h2>";
                echo "<p style='color: green;'>The user_activities table exists!</p>";

                // Check the structure of the table
                $structure_result = $conn->query("DESCRIBE user_activities");
                if ($structure_result) {
                    echo "<h3>Table Structure:</h3>";
                    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
                    echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

                    while ($row = $structure_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['Field'] . "</td>";
                        echo "<td>" . $row['Type'] . "</td>";
                        echo "<td>" . $row['Null'] . "</td>";
                        echo "<td>" . $row['Key'] . "</td>";
                        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
                        echo "<td>" . $row['Extra'] . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";

                    // Count records
                    $count_result = $conn->query("SELECT COUNT(*) as count FROM user_activities");
                    if ($count_result) {
                        $count_row = $count_result->fetch_assoc();
                        echo "<p>Total Records: " . $count_row['count'] . "</p>";
                    }
                } else {
                    echo "<p style='color: red;'>Error querying table structure: " . $conn->error . "</p>";
                }
            } else {
                echo "<h2>User Activities Table Check</h2>";
                echo "<p style='color: red;'>The user_activities table does not exist!</p>";
                echo "<p>Please run <a href='setup_activity_table.php'>setup_activity_table.php</a> to create it.</p>";
            }
        } else {
            echo "<p style='color: red;'>Error querying database: " . $conn->error . "</p>";
        }

        // Test creating a temporary table to check permissions
        echo "<h2>Testing Database Permissions</h2>";
        $test_sql = "CREATE TEMPORARY TABLE test_permissions (id INT)";
        if ($conn->query($test_sql)) {
            echo "<p style='color: green;'>Successfully created a temporary table. CREATE TABLE permission confirmed.</p>";
            $conn->query("DROP TEMPORARY TABLE test_permissions");
        } else {
            echo "<p style='color: red;'>Failed to create a temporary table. You may not have CREATE TABLE permission: " . $conn->error . "</p>";
        }

        $conn->close();
    } else {
        echo "<p style='color: red;'>Failed to connect to database.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='profile.php'>Go to Profile Page</a> | <a href='setup_activity_table.php'>Run Setup Script</a></p>";
?>

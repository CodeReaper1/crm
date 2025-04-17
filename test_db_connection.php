<?php
// Start session to get user ID if logged in
session_start();

// Include functions file for database configuration
require_once 'functions.php';
require_once 'debug_functions.php';
require_once 'user_activity.php';

// Database configuration
$host = DB_HOST;
$user = DB_USER;
$password = DB_PASS;
$database = DB_NAME;

// Create connection
$conn = connectDB();

// Check connection
if (!$conn) {
    die("<h1>Connection failed:</h1> <p>Database connection error</p>");
}

echo "<h1>Database Connection Test</h1>";
echo "<p>Connected successfully to the database: <strong>$database</strong></p>";

// Check if leads table exists
$result = $conn->query("SHOW TABLES LIKE 'leads'");
if ($result->num_rows > 0) {
    echo "Leads table exists<br>";

    // Check if leads table has the category column
    $result = $conn->query("SHOW COLUMNS FROM leads LIKE 'category'");
    if ($result->num_rows > 0) {
        echo "Category column exists in leads table<br>";
    } else {
        echo "Category column does not exist in leads table<br>";

        // Add category column if it doesn't exist
        $sql = "ALTER TABLE leads ADD COLUMN category VARCHAR(50) DEFAULT 'callStack'";
        if ($conn->query($sql) === TRUE) {
            echo "Category column added to leads table<br>";
        } else {
            echo "Error adding category column: " . $conn->error . "<br>";
        }
    }

    // Check if there are any leads in the table
    $result = $conn->query("SELECT COUNT(*) as count FROM leads");
    $row = $result->fetch_assoc();
    echo "Number of leads in the table: " . $row['count'] . "<br>";

    // Show sample of leads
    $result = $conn->query("SELECT id, business_name, category FROM leads LIMIT 5");
    if ($result->num_rows > 0) {
        echo "Sample leads:<br>";
        while($row = $result->fetch_assoc()) {
            echo "ID: " . $row["id"] . " - Name: " . $row["business_name"] . " - Category: " . ($row["category"] ?? "NULL") . "<br>";
        }
    }
} else {
    echo "Leads table does not exist<br>";
}

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "<h2>Users Table</h2>";
    echo "<p>The 'users' table exists.</p>";

    // Check users table structure
    $result = $conn->query("DESCRIBE users");
    if ($result) {
        echo "<h3>Table Structure</h3>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] === NULL ? 'NULL' : $row['Default']) . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>Error getting table structure: " . $conn->error . "</p>";
    }

    // Check if there are any users in the table
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Number of users in the database: <strong>" . $row['count'] . "</strong></p>";

        // Show sample of users
        $result = $conn->query("SELECT * FROM users LIMIT 1");
        if ($result && $result->num_rows > 0) {
            echo "<h3>Sample User Data</h3>";
            echo "<table border='1' cellpadding='5' cellspacing='0'>";
            echo "<tr style='background-color: #f2f2f2;'>";

            $user = $result->fetch_assoc();
            foreach ($user as $field => $value) {
                echo "<th>" . htmlspecialchars($field) . "</th>";
            }
            echo "</tr><tr>";

            foreach ($user as $value) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
            echo "</tr></table>";

            // Test update function
            echo "<h3>Test Update Function</h3>";
            echo "<form method='post' action='test_profile_update.php'>";
            echo "<input type='hidden' name='user_id' value='" . $user['id'] . "'>";
            echo "<button type='submit' name='test_update'>Test Profile Update Function</button>";
            echo "</form>";
        } else {
            echo "<p>No users found in the database.</p>";
        }
    } else {
        echo "<p>Error counting users: " . $conn->error . "</p>";
    }
} else {
    echo "<p>The 'users' table does not exist.</p>";
}

// Check if user_activities table exists
$result = $conn->query("SHOW TABLES LIKE 'user_activities'");
if ($result->num_rows > 0) {
    echo "<h2>User Activities Table</h2>";
    echo "<p style='color: green;'>The 'user_activities' table exists.</p>";

    // Check table structure
    $result = $conn->query("DESCRIBE user_activities");
    if ($result) {
        echo "<h3>Table Structure</h3>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . ($row['Default'] === NULL ? 'NULL' : $row['Default']) . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";

        // Check if there are any activities in the table
        $result = $conn->query("SELECT COUNT(*) as count FROM user_activities");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>Number of activities in the database: <strong>" . $row['count'] . "</strong></p>";

            // If user is logged in, try to create a test activity
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                echo "<h3>Test Activity Creation</h3>";

                // Create a test activity
                $test_data = [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'test_id' => uniqid(),
                    'message' => 'This is a test activity created by test_db_connection.php'
                ];

                $result = logUserActivity($user_id, 'test_activity', $test_data);
                if ($result) {
                    echo "<p style='color: green;'>Test activity created successfully!</p>";
                    echo "<p>You can now check the Recent Activity tab in your profile.</p>";
                    echo "<p><a href='profile.php'>Go to Profile Page</a></p>";
                } else {
                    echo "<p style='color: red;'>Failed to create test activity.</p>";
                    echo "<p>Please check the error log for details.</p>";
                }
            } else {
                echo "<p>You are not logged in. Cannot test activity creation.</p>";
            }
        } else {
            echo "<p>Error counting activities: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Error getting table structure: " . $conn->error . "</p>";
    }
} else {
    echo "<h2>User Activities Table</h2>";
    echo "<p style='color: red;'>The 'user_activities' table does not exist.</p>";
    echo "<p>Please run <a href='setup_activity_table.php'>setup_activity_table.php</a> to create it.</p>";
}

// Close connection
$conn->close();

echo "<p><a href='profile.php'>Go to Profile Page</a> | <a href='setup_activity_table.php'>Run Setup Script</a></p>";
?>

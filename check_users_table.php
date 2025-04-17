<?php
require_once 'functions.php';
require_once 'debug_functions.php';

// Connect to the database
$conn = connectDB();
if (!$conn) {
    echo "<h1>Failed to connect to the database.</h1>";
    exit;
}

echo "<h1>Users Table Check and Repair</h1>";

// Get the users table structure
$sql = "DESCRIBE users";
$result = $conn->query($sql);

if ($result) {
    echo "<h2>Users Table Structure</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
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

    // Check for missing columns
    $required_columns = ['job_title', 'location', 'profile_picture'];
    $missing_columns = [];

    foreach ($required_columns as $column) {
        if (!in_array($column, $columns)) {
            $missing_columns[] = $column;
        }
    }

    if (!empty($missing_columns)) {
        echo "<h2>Adding Missing Columns</h2>";
        echo "<p>The following columns are missing and will be added:</p>";
        echo "<ul>";
        foreach ($missing_columns as $column) {
            echo "<li>$column</li>";
        }
        echo "</ul>";

        foreach ($missing_columns as $column) {
            $sql = "ALTER TABLE users ADD COLUMN $column VARCHAR(255)";
            debug_log("Adding column $column to users table");

            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: green;'>Column '$column' added successfully!</p>";
                debug_log("Column $column added successfully");
            } else {
                echo "<p style='color: red;'>Error adding column '$column': " . $conn->error . "</p>";
                debug_log("Error adding column $column: " . $conn->error, 'ERROR');
            }
        }

        // Show updated table structure
        $sql = "DESCRIBE users";
        $result = $conn->query($sql);

        if ($result) {
            echo "<h2>Updated Table Structure</h2>";
            echo "<table border='1' cellpadding='5'>";
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
        }
    } else {
        echo "<p>All required columns exist in the users table.</p>";
    }
} else {
    echo "<p style='color: red;'>Error getting table structure: " . $conn->error . "</p>";
    debug_log("Error getting users table structure: " . $conn->error, 'ERROR');
}

// Get a sample user
$sql = "SELECT * FROM users LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    debug_log("Sample user data: " . json_encode($user));

    echo "<h2>Sample User Data</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr style='background-color: #f2f2f2;'>";

    foreach ($user as $field => $value) {
        echo "<th>" . htmlspecialchars($field) . "</th>";
    }
    echo "</tr><tr>";

    foreach ($user as $value) {
        echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
    }
    echo "</tr></table>";

    // Add a test update button
    echo "<h2>Test Profile Update</h2>";
    echo "<form method='post' action='direct_profile_update.php'>";
    echo "<input type='hidden' name='user_id' value='" . $user['id'] . "'>";
    echo "<button type='submit' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Test Direct Profile Update</button>";
    echo "</form>";

    echo "<p><a href='profile.php'>Go to Profile Page</a></p>";
} else {
    echo "<p>No users found in the database.</p>";
    debug_log("No users found in the database", 'WARNING');
}

// Close the connection
$conn->close();
?>

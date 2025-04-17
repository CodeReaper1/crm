<?php
/**
 * Setup script for user_activities table
 *
 * This script creates the user_activities table if it doesn't exist
 * and adds some sample activities for demonstration purposes.
 */

require_once 'functions.php';
require_once 'debug_functions.php';
require_once 'user_activity.php';

// Start session to get user ID
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user is logged in
if (!$user_id) {
    echo "<h1>Error</h1>";
    echo "<p>You must be logged in to run this setup script.</p>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

// Connect to database
$conn = connectDB();
if (!$conn) {
    die("<h1>Connection failed:</h1> <p>Database connection error</p>");
}

echo "<h1>User Activities Table Setup</h1>";

// Check if the user_activities table exists
$result = $conn->query("SHOW TABLES LIKE 'user_activities'");
if ($result->num_rows === 0) {
    // Create the table if it doesn't exist
    echo "<p>Creating user_activities table...</p>";
    $sql = "CREATE TABLE IF NOT EXISTS user_activities (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        activity_type VARCHAR(50) NOT NULL,
        activity_data TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql)) {
        echo "<p style='color: green;'>Table created successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error creating table: " . $conn->error . "</p>";

        // Try an alternative approach with simpler SQL
        echo "<p>Attempting alternative approach...</p>";
        $alt_sql = "CREATE TABLE IF NOT EXISTS user_activities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            activity_type VARCHAR(50) NOT NULL,
            activity_data TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if ($conn->query($alt_sql)) {
            echo "<p style='color: green;'>Table created successfully with alternative approach!</p>";
        } else {
            echo "<p style='color: red;'>Error creating table with alternative approach: " . $conn->error . "</p>";
            $conn->close();
            exit;
        }
    }
} else {
    echo "<p>The user_activities table already exists.</p>";
}

// Check if there are any activities
$sql = "SELECT COUNT(*) as count FROM user_activities WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$activity_count = $row['count'];
$stmt->close();

echo "<p>Found $activity_count activities for your user.</p>";

// Add sample activities if none exist
if ($activity_count === 0) {
    echo "<h2>Adding Sample Activities</h2>";

    // Sample activities
    $sample_activities = [
        [
            'type' => 'profile_update',
            'data' => [
                'fields_updated' => [
                    'name' => 'Your Name',
                    'email' => 'your.email@example.com',
                    'job_title' => 'Your Job Title',
                    'location' => 'Your Location'
                ]
            ]
        ],
        [
            'type' => 'password_change',
            'data' => [
                'timestamp' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ]
        ],
        [
            'type' => 'lead_move',
            'data' => [
                'business_id' => 1,
                'business_name' => 'ABC Company',
                'from_category' => 'warmLeads',
                'to_category' => 'currentlyWorkingWith',
                'from_display' => 'Warm Leads',
                'to_display' => 'Currently Working With',
                'from_color' => 'warning',
                'to_color' => 'success'
            ]
        ],
        [
            'type' => 'note_add',
            'data' => [
                'business_id' => 2,
                'business_name' => 'XYZ Corporation',
                'note_preview' => 'This is a sample note for demonstration purposes.'
            ]
        ],
        [
            'type' => 'lead_claim',
            'data' => [
                'business_id' => 3,
                'business_name' => '123 Industries',
                'from_category' => 'callStack',
                'from_display' => 'Call Stack',
                'from_color' => 'primary'
            ]
        ]
    ];

    // Add sample activities with different timestamps
    foreach ($sample_activities as $index => $activity) {
        // Create the activity
        $result = logUserActivity($user_id, $activity['type'], $activity['data']);

        if ($result) {
            echo "<p style='color: green;'>Added sample {$activity['type']} activity.</p>";

            // Update the timestamp to create a history
            $days_ago = $index;
            $timestamp = date('Y-m-d H:i:s', strtotime("-$days_ago days"));

            $update_sql = "UPDATE user_activities SET created_at = ? ORDER BY id DESC LIMIT 1";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("s", $timestamp);
            $update_stmt->execute();
            $update_stmt->close();
        } else {
            echo "<p style='color: red;'>Failed to add sample {$activity['type']} activity.</p>";
        }
    }

    echo "<p>Sample activities have been added with various timestamps.</p>";
}

// Display all activities
$sql = "SELECT * FROM user_activities WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Your Activities</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background-color: #f2f2f2;'><th>ID</th><th>Type</th><th>Date</th><th>Formatted</th></tr>";

    while ($row = $result->fetch_assoc()) {
        $formatted = formatActivity($row);
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['activity_type'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "<td>" . $formatted['html'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>No activities found.</p>";
}

$stmt->close();
$conn->close();

echo "<p><a href='profile.php'>Go to Profile Page</a></p>";
?>

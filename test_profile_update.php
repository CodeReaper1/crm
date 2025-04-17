<?php
require_once 'functions.php';

// Check if we have a user ID
if (!isset($_POST['user_id'])) {
    echo "<h1>Error</h1>";
    echo "<p>No user ID provided.</p>";
    echo "<p><a href='test_db_connection.php'>Go back to database test</a></p>";
    exit;
}

$user_id = (int)$_POST['user_id'];

// Get the current user data
$user = getUserProfile($user_id);
if (!$user) {
    echo "<h1>Error</h1>";
    echo "<p>User not found with ID: $user_id</p>";
    echo "<p><a href='test_db_connection.php'>Go back to database test</a></p>";
    exit;
}

echo "<h1>Test Profile Update</h1>";
echo "<p>Testing profile update for user: <strong>" . htmlspecialchars($user['name']) . "</strong> (ID: $user_id)</p>";

// Display current user data
echo "<h2>Current User Data</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Value</th></tr>";
foreach ($user as $field => $value) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($field) . "</td>";
    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test the update function
$test_data = [
    'name' => $user['name'] . ' (Updated)',
    'email' => $user['email'],
    'job_title' => isset($user['job_title']) ? $user['job_title'] . ' (Updated)' : 'Test Job Title',
    'location' => isset($user['location']) ? $user['location'] . ' (Updated)' : 'Test Location'
];

echo "<h2>Test Update Data</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Value</th></tr>";
foreach ($test_data as $field => $value) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($field) . "</td>";
    echo "<td>" . htmlspecialchars($value) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Perform the update
echo "<h2>Update Result</h2>";
$result = updateUserProfile($user_id, $test_data);

if ($result) {
    echo "<p style='color: green;'>Profile updated successfully!</p>";
    
    // Get the updated user data
    $updated_user = getUserProfile($user_id);
    
    echo "<h2>Updated User Data</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Value</th></tr>";
    foreach ($updated_user as $field => $value) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($field) . "</td>";
        echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Restore original data
    echo "<h2>Restoring Original Data</h2>";
    $restore_result = updateUserProfile($user_id, $user);
    
    if ($restore_result) {
        echo "<p style='color: green;'>Original data restored successfully!</p>";
    } else {
        echo "<p style='color: red;'>Failed to restore original data.</p>";
    }
} else {
    echo "<p style='color: red;'>Failed to update profile.</p>";
    echo "<p>Check the error log for more details.</p>";
}

// Add links to navigate
echo "<p><a href='test_db_connection.php'>Go back to database test</a></p>";
echo "<p><a href='profile.php'>Go to profile page</a></p>";
?>

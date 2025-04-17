<?php
session_start();
require_once 'debug_functions.php';
require_once 'user_activity.php';

// Debug request information
debug_log("=== UPDATE PROFILE HANDLER ====");
debug_log("Request method: " . $_SERVER['REQUEST_METHOD']);
debug_log("Request URI: " . $_SERVER['REQUEST_URI']);
debug_log("POST data: " . json_encode($_POST));
debug_log("===========================");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$redirect_url = 'profile.php';
$success = false;
$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_log("Processing profile update for user ID: $user_id");

    // Get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $job_title = isset($_POST['job_title']) ? trim($_POST['job_title']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';

    debug_log("Form data - Name: $name, Email: $email, Job Title: $job_title, Location: $location");

    // Validate data
    if (empty($name)) {
        $error_message = 'Name is required';
        debug_log("Validation error: Name is required");
    } else {
        // Include functions file for database connection if not already included
        if (!function_exists('connectDB')) {
            require_once 'functions.php';
        }

        // Database connection
        $conn = connectDB();

        if (!$conn) {
            $error_message = 'Database connection failed';
            debug_log("Database connection failed", 'ERROR');
        } else {
            debug_log("Database connection successful");

            // Update user profile
            $sql = "UPDATE users SET name = ?, email = ?, job_title = ?, location = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                $error_message = 'Database error';
                debug_log("Prepare statement failed: " . $conn->error, 'ERROR');
            } else {
                $stmt->bind_param("ssssi", $name, $email, $job_title, $location, $user_id);
                $result = $stmt->execute();

                debug_log("SQL execution result: " . ($result ? 'true' : 'false'));
                debug_log("Affected rows: " . $stmt->affected_rows);

                if ($result) {
                    $success = true;
                    debug_log("Profile updated successfully");

                    // Log the profile update activity
                    $activity_data = [
                        'fields_updated' => [
                            'name' => $name,
                            'email' => $email,
                            'job_title' => $job_title,
                            'location' => $location
                        ]
                    ];
                    logUserActivity($user_id, 'profile_update', $activity_data);
                    debug_log("Profile update activity logged");
                } else {
                    $error_message = 'Failed to update profile: ' . $stmt->error;
                    debug_log("Update failed: " . $stmt->error, 'ERROR');
                }

                $stmt->close();
            }

            $conn->close();
        }
    }
}

// Set session messages
if ($success) {
    $_SESSION['profile_success'] = 'Profile updated successfully';
} else if (!empty($error_message)) {
    $_SESSION['profile_error'] = $error_message;
}

// Redirect back to profile page
header("Location: $redirect_url");
exit;
?>

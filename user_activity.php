<?php
/**
 * User Activity Tracking Functions
 *
 * This file contains functions for logging and retrieving user activities
 */

require_once 'debug_functions.php';

/**
 * Log a user activity
 *
 * @param int $user_id The user ID
 * @param string $activity_type The type of activity (e.g., 'lead_move', 'note_add', 'profile_update')
 * @param array $activity_data Additional data about the activity
 * @return bool Success status
 */
function logUserActivity($user_id, $activity_type, $activity_data = []) {
    debug_log("Logging user activity - User ID: $user_id, Type: $activity_type");

    // Include functions file for database connection if not already included
    if (!function_exists('connectDB')) {
        require_once 'functions.php';
    }

    // Connect to database
    $conn = connectDB();
    if (!$conn) {
        debug_log("Database connection failed", 'ERROR');
        return false;
    }

    // Check if the user_activities table exists
    $result = $conn->query("SHOW TABLES LIKE 'user_activities'");
    if ($result->num_rows === 0) {
        // Create the table if it doesn't exist
        debug_log("Creating user_activities table");
        $sql = "CREATE TABLE IF NOT EXISTS user_activities (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            activity_type VARCHAR(50) NOT NULL,
            activity_data TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if (!$conn->query($sql)) {
            debug_log("Failed to create user_activities table: " . $conn->error, 'ERROR');
            // Instead of returning false, we'll try to continue with a fallback approach
            debug_log("Attempting to use alternative approach for activity logging");

            // Try to create a simpler version of the table in case there are permission issues
            $simple_sql = "CREATE TABLE IF NOT EXISTS user_activities (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                activity_type VARCHAR(50) NOT NULL,
                activity_data TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            if (!$conn->query($simple_sql)) {
                debug_log("Failed to create simplified user_activities table: " . $conn->error, 'ERROR');
                $conn->close();
                return false;
            }
        }
    }

    // Prepare and execute the insert statement
    $sql = "INSERT INTO user_activities (user_id, activity_type, activity_data) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        debug_log("Failed to prepare statement: " . $conn->error, 'ERROR');
        $conn->close();
        return false;
    }

    // Convert activity data to JSON
    $activity_data_json = json_encode($activity_data);
    if ($activity_data_json === false) {
        debug_log("Failed to encode activity data to JSON", 'ERROR');
        debug_log("Activity data: " . print_r($activity_data, true), 'ERROR');
        $stmt->close();
        $conn->close();
        return false;
    }

    // Bind parameters
    $bind_result = $stmt->bind_param("iss", $user_id, $activity_type, $activity_data_json);
    if (!$bind_result) {
        debug_log("Failed to bind parameters: " . $stmt->error, 'ERROR');
        $stmt->close();
        $conn->close();
        return false;
    }

    // Execute the statement
    $result = $stmt->execute();

    if (!$result) {
        debug_log("Failed to log user activity: " . $stmt->error, 'ERROR');
    } else {
        debug_log("User activity logged successfully - ID: " . $stmt->insert_id);
    }

    $stmt->close();
    $conn->close();

    return $result;
}

/**
 * Get recent activities for a user
 *
 * @param int $user_id The user ID
 * @param int $limit Maximum number of activities to retrieve
 * @return array Array of user activities
 */
function getUserActivities($user_id, $limit = 10) {
    debug_log("Getting user activities - User ID: $user_id, Limit: $limit");

    $activities = [];

    // Include functions file for database connection if not already included
    if (!function_exists('connectDB')) {
        require_once 'functions.php';
    }

    // Connect to database
    $conn = connectDB();
    if (!$conn) {
        debug_log("Database connection failed", 'ERROR');
        return $activities;
    }

    // Check if the user_activities table exists
    $result = $conn->query("SHOW TABLES LIKE 'user_activities'");
    if ($result->num_rows === 0) {
        debug_log("user_activities table does not exist, attempting to create it", 'WARNING');

        // Try to create the table
        $sql = "CREATE TABLE IF NOT EXISTS user_activities (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            activity_type VARCHAR(50) NOT NULL,
            activity_data TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if (!$conn->query($sql)) {
            debug_log("Failed to create user_activities table: " . $conn->error, 'ERROR');
            $conn->close();
            return $activities;
        }

        debug_log("Successfully created user_activities table");
        // Continue execution since we've created the table
    }

    // Prepare and execute the select statement
    $sql = "SELECT * FROM user_activities WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Parse the activity data JSON
        $row['activity_data'] = json_decode($row['activity_data'], true);
        $activities[] = $row;
    }

    $stmt->close();
    $conn->close();

    debug_log("Retrieved " . count($activities) . " user activities");
    return $activities;
}

/**
 * Format an activity for display
 *
 * @param array $activity The activity data
 * @return array Formatted activity with human-readable text and date
 */
function formatActivity($activity) {
    $formatted = [
        'id' => $activity['id'],
        'date' => date('M j, Y, g:i A', strtotime($activity['created_at'])),
        'relative_date' => getRelativeTime($activity['created_at']),
        'text' => '',
        'html' => ''
    ];

    $data = $activity['activity_data'];

    switch ($activity['activity_type']) {
        case 'lead_move':
            $formatted['text'] = "You moved {$data['business_name']} from {$data['from_category']} to {$data['to_category']}";
            $formatted['html'] = "You moved <strong>{$data['business_name']}</strong> from <span class=\"badge bg-light-{$data['from_color']} text-{$data['from_color']}\">{$data['from_display']}</span> to <span class=\"badge bg-light-{$data['to_color']} text-{$data['to_color']}\">{$data['to_display']}</span>";
            break;

        case 'lead_claim':
            $formatted['text'] = "You claimed {$data['business_name']} from {$data['from_category']}";
            $formatted['html'] = "You claimed <strong>{$data['business_name']}</strong> from <span class=\"badge bg-light-{$data['from_color']} text-{$data['from_color']}\">{$data['from_display']}</span>";
            break;

        case 'note_add':
            $formatted['text'] = "You added a note to {$data['business_name']}";
            $formatted['html'] = "You added a note to <strong>{$data['business_name']}</strong>";
            break;

        case 'profile_update':
            $formatted['text'] = "You updated your profile information";
            $formatted['html'] = "You updated your profile information";
            break;

        case 'password_change':
            $formatted['text'] = "You changed your password";
            $formatted['html'] = "You changed your password";
            break;

        default:
            $formatted['text'] = "Unknown activity";
            $formatted['html'] = "Unknown activity";
    }

    return $formatted;
}

/**
 * Get a human-readable relative time string (e.g., "Today", "Yesterday", "2 days ago")
 *
 * @param string $datetime The datetime string
 * @return string Human-readable relative time
 */
function getRelativeTime($datetime) {
    $timestamp = strtotime($datetime);
    $current = time();
    $diff = $current - $timestamp;

    $day_diff = floor($diff / 86400);

    if ($day_diff == 0) {
        // Today
        return "Today, " . date('g:i A', $timestamp);
    } else if ($day_diff == 1) {
        // Yesterday
        return "Yesterday, " . date('g:i A', $timestamp);
    } else if ($day_diff < 7) {
        // Within a week
        return date('l', $timestamp) . ", " . date('g:i A', $timestamp);
    } else {
        // More than a week ago
        return date('M j, Y, g:i A', $timestamp);
    }
}
?>

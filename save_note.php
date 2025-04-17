<?php
require_once 'functions.php';
require_once 'debug_functions.php';
require_once 'user_activity.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session to get user ID
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Initialize response
$response = ['success' => false, 'message' => '', 'debug' => []];

// Check if user is logged in
if (!$user_id) {
    $response['message'] = 'You must be logged in to add notes';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Log the request data
$response['debug']['request_method'] = $_SERVER['REQUEST_METHOD'];
$response['debug']['post_data'] = $_POST;

// Check if required parameters are provided
if (isset($_POST['lead_id']) && isset($_POST['note_content'])) {
    $lead_id = intval($_POST['lead_id']);
    $note_content = trim($_POST['note_content']);

    $response['debug']['lead_id'] = $lead_id;
    $response['debug']['note_content_length'] = strlen($note_content);

    // Validate note content
    if (empty($note_content)) {
        $response['message'] = 'Note content cannot be empty';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Get the current lead
    $lead = getLeadById($lead_id);

    if (!$lead) {
        $response['message'] = 'Lead not found';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    // Get user name
    $user = getUserProfile($user_id);
    $user_name = $user ? $user['name'] : 'Unknown User';

    // Format the timestamp
    $timestamp = date('Y-m-d H:i');

    // Update the lead notes
    $current_notes = $lead['notes'];
    if ($current_notes == 'No Notes' || $current_notes == 'noValue') {
        $new_notes = $timestamp . " by " . $user_name . ":\n" . $note_content;
    } else {
        $new_notes = $current_notes . "\n\n" . $timestamp . " by " . $user_name . ":\n" . $note_content;
    }

    // Save the updated notes
    $response['debug']['use_database'] = USE_DATABASE;
    $response['debug']['new_notes'] = substr($new_notes, 0, 50) . '...'; // First 50 chars for debugging

    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) {
            $response['message'] = 'Database connection failed';
            $response['debug']['conn_error'] = 'Connection failed';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $response['debug']['connected'] = true;

        $sql = "UPDATE leads SET notes = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $response['message'] = 'Failed to prepare statement';
            $response['debug']['prepare_error'] = $conn->error;
            header('Content-Type: application/json');
            echo json_encode($response);
            $conn->close();
            exit;
        }

        $bind_result = $stmt->bind_param("si", $new_notes, $lead_id);

        if (!$bind_result) {
            $response['message'] = 'Failed to bind parameters';
            $response['debug']['bind_error'] = $stmt->error;
            header('Content-Type: application/json');
            echo json_encode($response);
            $stmt->close();
            $conn->close();
            exit;
        }

        $result = $stmt->execute();
        $affected = $stmt->affected_rows;

        $response['debug']['execute_result'] = $result;
        $response['debug']['affected_rows'] = $affected;
        $response['debug']['execute_error'] = $stmt->error;

        $stmt->close();
        $conn->close();

        if ($result && $affected > 0) {
            $response['success'] = true;
            $response['message'] = 'Note added successfully';

            // Log the note add activity
            $activity_data = [
                'business_id' => $lead_id,
                'business_name' => $lead['business_name'],
                'note_preview' => substr($note_content, 0, 50) . (strlen($note_content) > 50 ? '...' : '')
            ];

            logUserActivity($user_id, 'note_add', $activity_data);
            debug_log("Note add activity logged - User ID: $user_id, Business: {$lead['business_name']}");
        } else {
            $response['message'] = 'Failed to add note - no rows affected';
        }
    } else {
        // Update notes in the JSON file
        if (!file_exists(COMBINED_DATA_FILE)) {
            $response['message'] = 'Data file not found';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $json_content = file_get_contents(COMBINED_DATA_FILE);
        $businesses = json_decode($json_content, true);

        if (!is_array($businesses)) {
            $response['message'] = 'Invalid data file format';
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $found = false;
        foreach ($businesses as $key => $business) {
            if (isset($business['id']) && $business['id'] == $lead_id) {
                $businesses[$key]['notes'] = $new_notes;
                $found = true;
                break;
            }
        }

        if ($found) {
            $json_content = json_encode($businesses, JSON_PRETTY_PRINT);
            $result = file_put_contents(COMBINED_DATA_FILE, $json_content) !== false;

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Note added successfully';

                // Log the note add activity
                $activity_data = [
                    'business_id' => $lead_id,
                    'business_name' => $lead['business_name'],
                    'note_preview' => substr($note_content, 0, 50) . (strlen($note_content) > 50 ? '...' : '')
                ];

                logUserActivity($user_id, 'note_add', $activity_data);
                debug_log("Note add activity logged (static mode) - User ID: $user_id, Business: {$lead['business_name']}");
            } else {
                $response['message'] = 'Failed to save data file';
            }
        } else {
            $response['message'] = 'Lead not found in data file';
        }
    }
} else {
    $response['message'] = 'Missing required parameters';
}

// Check if this is a direct form submission (not AJAX)
$is_direct_submission = !empty($_SERVER['HTTP_REFERER']) &&
                        strpos($_SERVER['HTTP_REFERER'], 'lead-profile.php') !== false &&
                        empty($_SERVER['HTTP_X_REQUESTED_WITH']);

$response['debug']['is_direct_submission'] = $is_direct_submission;

if ($is_direct_submission && $response['success']) {
    // Redirect back to the lead profile page
    header('Location: lead-profile.php?id=' . $lead_id . '&note_added=1');
    exit;
} else if ($is_direct_submission && !$response['success']) {
    // Redirect back with error
    header('Location: lead-profile.php?id=' . $lead_id . '&note_error=' . urlencode($response['message']));
    exit;
} else {
    // Return JSON response for AJAX requests
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>

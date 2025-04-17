<?php
require_once 'functions.php';
require_once 'debug_functions.php';
require_once 'user_activity.php';

// Start session to get user ID
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Initialize response
$response = ['success' => false, 'message' => '', 'debug' => []];

// Add request data to debug info
$response['debug']['post'] = $_POST;
$response['debug']['user_id'] = $user_id;

if (isset($_POST['id']) && isset($_POST['from_category']) && isset($_POST['to_category'])) {
    $id = intval($_POST['id']);
    $from_category = $_POST['from_category'];
    $to_category = $_POST['to_category'];

    // Add parameters to debug info
    $response['debug']['id'] = $id;
    $response['debug']['from_category'] = $from_category;
    $response['debug']['to_category'] = $to_category;

    // Move the business from one category to another
    try {
        // Check if the lead exists before attempting to move it
        $lead = getLeadById($id);
        if (!$lead) {
            $response['debug']['lead_exists'] = false;
            $response['message'] = "Lead with ID $id not found";
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $response['debug']['lead_exists'] = true;
        $response['debug']['lead_data'] = $lead;

        // Check if the lead is in the expected category
        if ($lead['category'] !== $from_category) {
            $response['debug']['category_match'] = false;
            $response['message'] = "Lead is not in the expected category. Expected: $from_category, Actual: {$lead['category']}";
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        $response['debug']['category_match'] = true;

        // Attempt to move the lead
        $result = moveBusinessCategory($id, $from_category, $to_category);
        $response['debug']['move_result'] = $result;
    } catch (Exception $e) {
        $response['debug']['exception'] = $e->getMessage();
        $response['message'] = "Error: " . $e->getMessage();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Business successfully moved from ' . formatCategoryName($from_category) . ' to ' . formatCategoryName($to_category);

        // Get business details for the activity log
        $business = getLeadById($id);
        $business_name = $business ? $business['business_name'] : "Business #$id";

        // Log the lead move activity
        try {
            $activity_data = [
                'business_id' => $id,
                'business_name' => $business_name,
                'from_category' => $from_category,
                'to_category' => $to_category,
                'from_display' => formatCategoryName($from_category),
                'to_display' => formatCategoryName($to_category),
                'from_color' => getCategoryColor($from_category),
                'to_color' => getCategoryColor($to_category)
            ];

            $log_result = logUserActivity($user_id, 'lead_move', $activity_data);
            $response['debug']['activity_log_result'] = $log_result;

            if ($log_result) {
                debug_log("Lead move activity logged - User ID: $user_id, Business: $business_name, From: $from_category, To: $to_category");
            } else {
                debug_log("Failed to log lead move activity - User ID: $user_id, Business: $business_name", 'WARNING');
            }
        } catch (Exception $e) {
            debug_log("Exception while logging activity: " . $e->getMessage(), 'ERROR');
            $response['debug']['activity_log_exception'] = $e->getMessage();
            // Continue execution even if activity logging fails
        }
    } else {
        $response['message'] = 'Failed to move business. Please try again.';
    }
} else {
    $response['message'] = 'Missing required parameters';
    $response['debug']['missing'] = [
        'id' => !isset($_POST['id']),
        'from_category' => !isset($_POST['from_category']),
        'to_category' => !isset($_POST['to_category'])
    ];
}

// Helper function to format category names
function formatCategoryName($category) {
    switch($category) {
        case 'callStack': return 'Call Stack';
        case 'coldLeads': return 'Cold Leads';
        case 'warmLeads': return 'Warm Leads';
        case 'currentlyWorkingWith': return 'Currently Working With';
        default: return $category;
    }
}

// Helper function to get category color for badges
function getCategoryColor($category) {
    switch($category) {
        case 'callStack': return 'primary';
        case 'coldLeads': return 'info';
        case 'warmLeads': return 'warning';
        case 'currentlyWorkingWith': return 'success';
        default: return 'secondary';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>

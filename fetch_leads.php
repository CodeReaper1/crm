<?php
// Start session and check login before ANY output
session_start();

// Check if user is logged in and get user ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Redirect if not logged in
if (!$user_id) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

require_once 'functions.php';

// Determine which category to fetch based on the referring page
$category = 'callStack'; // Default
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

// For debugging
file_put_contents('debug_referer.txt', date('Y-m-d H:i:s') . " - Referer: $referer\n", FILE_APPEND);

// Allow explicit category parameter to override referer detection
if (isset($_POST['category']) && in_array($_POST['category'], ['callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith'])) {
    $category = $_POST['category'];
    file_put_contents('debug_referer.txt', date('Y-m-d H:i:s') . " - Explicit category: $category\n", FILE_APPEND);
} else if (strpos($referer, 'page-cold-leads.php') !== false) {
    $category = 'coldLeads';
} elseif (strpos($referer, 'page-warm-leads.php') !== false) {
    $category = 'warmLeads';
} elseif (strpos($referer, 'currently-working-with.php') !== false) {
    $category = 'currentlyWorkingWith';
}

// Get the request parameters
$limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
$offset = isset($_POST['start']) ? intval($_POST['start']) : 0;
$order_column_index = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
$search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
$has_notes = isset($_POST['hasNotes']) && $_POST['hasNotes'] === 'true';

// Define the columns
$columns = ['niche', 'business_name', 'email', 'phone_numbers', 'website', 'notes'];
$order_column = isset($columns[$order_column_index]) ? $columns[$order_column_index] : 'business_name';

// Get the total count for this category directly from the database
$conn = connectDB();
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql = "SELECT COUNT(*) as count FROM leads WHERE category = ?";

// Add filter for leads with notes if requested
if ($has_notes) {
    $sql .= " AND notes IS NOT NULL AND notes != ''";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$total_count = $row['count'];
$stmt->close();

// Get filtered results count if search is provided
if (!empty($search) || $has_notes) {
    // Use a direct SQL query to get the filtered count
    $sql = "SELECT COUNT(*) as count FROM leads WHERE category = ?";

    // Add filter for leads with notes if requested
    if ($has_notes) {
        $sql .= " AND notes IS NOT NULL AND notes != ''";
    }

    // Add search conditions if search is provided
    if (!empty($search)) {
        $sql .= " AND (niche LIKE ? OR business_name LIKE ? OR email LIKE ? OR
                phone_numbers LIKE ? OR website LIKE ? OR notes LIKE ?)";
    }

    $stmt = $conn->prepare($sql);

    if (!empty($search)) {
        $search_param = "%$search%";
        $stmt->bind_param("sssssss", $category, $search_param, $search_param, $search_param, $search_param, $search_param, $search_param);
    } else {
        $stmt->bind_param("s", $category);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $filtered_count = $row['count'];
    $stmt->close();
} else {
    $filtered_count = $total_count;
}

// Fetch the leads with sorting, pagination, and search
$leads = getLeads(
    $category,
    $limit,  // limit
    $offset,   // offset
    $order_column, // order column
    $order_dir, // order direction
    $search, // search value
    $user_id, // user ID for filtering user-specific categories
    $has_notes // filter for leads with notes
);

// Prepare the data for DataTables
$data = [];
foreach ($leads as $lead) {
    // Process phone numbers
    if (isset($lead['phone_numbers'])) {
        if (is_array($lead['phone_numbers'])) {
            $lead['phone_numbers'] = implode(', ', $lead['phone_numbers']);
        } elseif (is_string($lead['phone_numbers']) && substr($lead['phone_numbers'], 0, 1) === '[') {
            // It's a JSON string, decode and join
            $phones = json_decode($lead['phone_numbers'], true);
            if (is_array($phones)) {
                $lead['phone_numbers'] = implode(', ', $phones);
            }
        }
    }

    $data[] = $lead;
}

// Add debugging information
$debug = [
    'category' => $category,
    'limit' => $limit,
    'offset' => $offset,
    'order_column' => $order_column,
    'order_dir' => $order_dir,
    'has_notes' => $has_notes,
    'total_count' => $total_count,
    'filtered_count' => $filtered_count,
    'lead_count' => count($leads),
    'referer' => $referer
];

// Close the database connection
$conn->close();

// Return in DataTables expected format
header('Content-Type: application/json');
echo json_encode([
    "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
    "recordsTotal" => intval($total_count),    // Total records before filtering
    "recordsFiltered" => intval($filtered_count), // Total records after filtering
    "data" => $data,
    "debug" => $debug
]);
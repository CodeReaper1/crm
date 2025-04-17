<?php
require_once 'functions.php';

// Start session to get user ID
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 for testing

echo "<h1>Test Move Business</h1>";

// Get a lead from the callStack category
$leads = getLeads('callStack', 1, 0);
if (empty($leads)) {
    echo "<p>No leads found in callStack category</p>";
    exit;
}

$lead = $leads[0];
$lead_id = $lead['id'];
$from_category = 'callStack';
$to_category = 'coldLeads';

echo "<h2>Lead Information</h2>";
echo "<p>ID: {$lead_id}</p>";
echo "<p>Business Name: {$lead['business_name']}</p>";
echo "<p>Current Category: {$lead['category']}</p>";

echo "<h2>Moving Lead</h2>";
echo "<p>From: $from_category</p>";
echo "<p>To: $to_category</p>";

// Try to move the lead
$result = moveBusinessCategory($lead_id, $from_category, $to_category);

echo "<h2>Result</h2>";
echo "<p>" . ($result ? "Success" : "Failed") . "</p>";

// Check if the lead was actually moved
$moved_lead = getLeads($to_category, 1, 0, 'id', 'DESC', $lead_id);
$original_lead = getLeads($from_category, 1, 0, 'id', 'DESC', $lead_id);

echo "<h2>Verification</h2>";
echo "<p>Lead found in target category: " . (!empty($moved_lead) ? "Yes" : "No") . "</p>";
echo "<p>Lead still in original category: " . (!empty($original_lead) ? "Yes" : "No") . "</p>";

// Display the log file contents
echo "<h2>Log File Contents</h2>";
if (file_exists('move_business_log.txt')) {
    echo "<pre>" . htmlspecialchars(file_get_contents('move_business_log.txt')) . "</pre>";
} else {
    echo "<p>No log file found</p>";
}
?>

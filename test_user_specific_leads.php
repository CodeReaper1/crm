<?php
session_start();
require_once 'functions.php';

// Check if user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id) {
    echo "<h1>Please log in first</h1>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

// Get user profile
$user = getUserProfile($user_id);
if (!$user) {
    echo "<h1>User not found</h1>";
    exit;
}

echo "<h1>Testing User-Specific Lead Categories</h1>";
echo "<p>Logged in as: " . htmlspecialchars($user['name']) . " (ID: $user_id)</p>";

// Get lead statistics
$lead_stats = getUserLeadStats($user_id);

echo "<h2>Lead Statistics</h2>";
echo "<ul>";
echo "<li>Call Stack: " . $lead_stats['callStack'] . " (shared among all users)</li>";
echo "<li>Cold Leads: " . $lead_stats['coldLeads'] . " (specific to this user)</li>";
echo "<li>Warm Leads: " . $lead_stats['warmLeads'] . " (specific to this user)</li>";
echo "<li>Currently Working With: " . $lead_stats['currentlyWorkingWith'] . " (specific to this user)</li>";
echo "<li>Total: " . $lead_stats['total'] . "</li>";
echo "</ul>";

// Test getting leads from each category
$categories = ['callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith'];

foreach ($categories as $category) {
    echo "<h2>$category</h2>";
    
    // Get leads for this category
    $leads = getLeads($category, 10, 0, 'business_name', 'ASC', '', $user_id);
    
    echo "<p>Found " . count($leads) . " leads</p>";
    
    if (count($leads) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Business Name</th><th>Category</th><th>Assigned To</th></tr>";
        
        foreach ($leads as $lead) {
            $assigned_to = isset($lead['assigned_to']) ? $lead['assigned_to'] : 'None';
            $assigned_to_name = $assigned_to !== 'None' ? getUserName($assigned_to) : 'None';
            
            echo "<tr>";
            echo "<td>" . $lead['id'] . "</td>";
            echo "<td>" . htmlspecialchars($lead['business_name']) . "</td>";
            echo "<td>" . $lead['category'] . "</td>";
            echo "<td>" . $assigned_to_name . " (ID: $assigned_to)</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
}

// Test moving a lead from Call Stack to Cold Leads
echo "<h2>Test Moving Lead from Call Stack to Cold Leads</h2>";

// Get a lead from Call Stack
$call_stack_leads = getLeads('callStack', 1, 0, 'id', 'DESC', '', null);

if (count($call_stack_leads) > 0) {
    $lead = $call_stack_leads[0];
    $lead_id = $lead['id'];
    
    echo "<p>Selected lead: " . htmlspecialchars($lead['business_name']) . " (ID: $lead_id)</p>";
    
    // Try to move the lead
    $result = moveBusinessCategory($lead_id, 'callStack', 'coldLeads', $user_id);
    
    echo "<p>Move result: " . ($result ? "Success" : "Failed") . "</p>";
    
    // Check if the lead was moved and assigned to the user
    $moved_lead = getLeadById($lead_id);
    
    if ($moved_lead) {
        echo "<p>Lead after move:</p>";
        echo "<ul>";
        echo "<li>Category: " . $moved_lead['category'] . "</li>";
        echo "<li>Assigned To: " . (isset($moved_lead['assigned_to']) ? $moved_lead['assigned_to'] : 'None') . "</li>";
        echo "</ul>";
        
        // If the lead was successfully moved, move it back to Call Stack for testing purposes
        if ($moved_lead['category'] === 'coldLeads') {
            $move_back = moveBusinessCategory($lead_id, 'coldLeads', 'callStack', $user_id);
            echo "<p>Moved lead back to Call Stack: " . ($move_back ? "Success" : "Failed") . "</p>";
        }
    } else {
        echo "<p>Could not find lead after move</p>";
    }
} else {
    echo "<p>No leads found in Call Stack</p>";
}

echo "<p><a href='index.php'>Return to Dashboard</a></p>";
?>

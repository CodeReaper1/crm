<?php
/**
 * Verify Leads Script
 *
 * This script verifies that the leads in the database match those in the combined_businesses.json file.
 */

require_once 'functions.php';
require_once 'debug_functions.php';

// Start session to get user ID
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user is logged in
if (!$user_id) {
    echo "<h1>Error</h1>";
    echo "<p>You must be logged in to run this verification script.</p>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

// Initialize variables
$json_file = 'combined_businesses.json';
$db_leads = [];
$json_leads = [];
$mismatches = [];
$missing_in_db = [];
$missing_in_json = [];

// Connect to database
$conn = connectDB();
if (!$conn) {
    die("<h1>Connection failed:</h1> <p>Database connection error</p>");
}

// Start HTML output
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verify Leads</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .mismatch { background-color: #fff3cd; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Verify Leads</h1>";

// Get leads from database
echo "<div class='card'>";
echo "<h2>Fetching Leads from Database</h2>";

$sql = "SELECT * FROM leads";
$result = $conn->query($sql);

if (!$result) {
    echo "<div class='alert alert-danger'>Error fetching leads from database: " . $conn->error . "</div>";
} else {
    $db_count = $result->num_rows;
    echo "<div class='alert alert-info'>Found $db_count leads in the database.</div>";

    while ($row = $result->fetch_assoc()) {
        $db_leads[$row['id']] = $row;
    }
}

echo "</div>";

// Get leads from JSON file
echo "<div class='card'>";
echo "<h2>Fetching Leads from JSON File</h2>";

if (!file_exists($json_file)) {
    echo "<div class='alert alert-danger'>Error: The file $json_file does not exist.</div>";
} else {
    $json_content = file_get_contents($json_file);
    $businesses = json_decode($json_content, true);

    if (!is_array($businesses)) {
        echo "<div class='alert alert-danger'>Error: Invalid JSON data in $json_file.</div>";
    } else {
        $json_count = count($businesses);
        echo "<div class='alert alert-info'>Found $json_count leads in the JSON file.</div>";

        foreach ($businesses as $index => $business) {
            $id = isset($business['id']) ? $business['id'] : ($index + 1);
            $json_leads[$id] = $business;
        }
    }
}

echo "</div>";

// Compare leads
echo "<div class='card'>";
echo "<h2>Comparing Leads</h2>";

if (empty($db_leads) || empty($json_leads)) {
    echo "<div class='alert alert-danger'>Cannot compare leads: One or both sources are empty.</div>";
} else {
    // Check for leads in JSON but not in DB
    foreach ($json_leads as $id => $json_lead) {
        if (!isset($db_leads[$id])) {
            $missing_in_db[] = $id;
        }
    }

    // Check for leads in DB but not in JSON
    foreach ($db_leads as $id => $db_lead) {
        if (!isset($json_leads[$id])) {
            $missing_in_json[] = $id;
        }
    }

    // Check for mismatches in common leads
    foreach ($json_leads as $id => $json_lead) {
        if (isset($db_leads[$id])) {
            $db_lead = $db_leads[$id];

            // Compare key fields
            $business_name_json = isset($json_lead['business_name']) ? $json_lead['business_name'] :
                                (isset($json_lead['business name']) ? $json_lead['business name'] : 'Unknown Business');
            $business_name_db = $db_lead['business_name'];

            $category_json = isset($json_lead['category']) ? $json_lead['category'] : 'callStack';
            $category_db = $db_lead['category'];

            $assigned_to_json = isset($json_lead['assigned_to']) ? $json_lead['assigned_to'] : null;
            $assigned_to_db = $db_lead['assigned_to'];

            // Check for mismatches
            if ($business_name_json !== $business_name_db ||
                $category_json !== $category_db ||
                $assigned_to_json != $assigned_to_db) {

                $mismatches[] = [
                    'id' => $id,
                    'field' => [
                        'business_name' => [
                            'json' => $business_name_json,
                            'db' => $business_name_db,
                            'match' => $business_name_json === $business_name_db
                        ],
                        'category' => [
                            'json' => $category_json,
                            'db' => $category_db,
                            'match' => $category_json === $category_db
                        ],
                        'assigned_to' => [
                            'json' => $assigned_to_json,
                            'db' => $assigned_to_db,
                            'match' => $assigned_to_json == $assigned_to_db
                        ]
                    ]
                ];
            }
        }
    }

    // Display results
    echo "<div class='alert " . (empty($missing_in_db) && empty($missing_in_json) && empty($mismatches) ? "alert-success" : "alert-danger") . "'>";
    echo "<p>Comparison complete:</p>";
    echo "<ul>";
    echo "<li>Leads in JSON but missing in DB: " . count($missing_in_db) . "</li>";
    echo "<li>Leads in DB but missing in JSON: " . count($missing_in_json) . "</li>";
    echo "<li>Leads with mismatches: " . count($mismatches) . "</li>";
    echo "</ul>";
    echo "</div>";

    // Display missing leads
    if (!empty($missing_in_db)) {
        echo "<h3>Leads in JSON but Missing in DB (" . count($missing_in_db) . ")</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Business Name</th><th>Category</th></tr>";

        $count = 0;
        foreach ($missing_in_db as $id) {
            if ($count++ < 10) {
                $lead = $json_leads[$id];
                $business_name = isset($lead['business_name']) ? $lead['business_name'] :
                                (isset($lead['business name']) ? $lead['business name'] : 'Unknown Business');
                $category = isset($lead['category']) ? $lead['category'] : 'callStack';

                echo "<tr>";
                echo "<td>" . $id . "</td>";
                echo "<td>" . htmlspecialchars($business_name) . "</td>";
                echo "<td>" . htmlspecialchars($category) . "</td>";
                echo "</tr>";
            }
        }

        if (count($missing_in_db) > 10) {
            echo "<tr><td colspan='3'>... and " . (count($missing_in_db) - 10) . " more</td></tr>";
        }

        echo "</table>";
    }

    if (!empty($missing_in_json)) {
        echo "<h3>Leads in DB but Missing in JSON (" . count($missing_in_json) . ")</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Business Name</th><th>Category</th></tr>";

        $count = 0;
        foreach ($missing_in_json as $id) {
            if ($count++ < 10) {
                $lead = $db_leads[$id];

                echo "<tr>";
                echo "<td>" . $id . "</td>";
                echo "<td>" . htmlspecialchars($lead['business_name']) . "</td>";
                echo "<td>" . htmlspecialchars($lead['category']) . "</td>";
                echo "</tr>";
            }
        }

        if (count($missing_in_json) > 10) {
            echo "<tr><td colspan='3'>... and " . (count($missing_in_json) - 10) . " more</td></tr>";
        }

        echo "</table>";
    }

    if (!empty($mismatches)) {
        echo "<h3>Leads with Mismatches (" . count($mismatches) . ")</h3>";
        echo "<table>";
        echo "<tr><th>ID</th><th>Field</th><th>JSON Value</th><th>DB Value</th></tr>";

        $count = 0;
        foreach ($mismatches as $mismatch) {
            if ($count++ < 10) {
                $id = $mismatch['id'];
                $fields = $mismatch['field'];

                foreach ($fields as $field_name => $field_data) {
                    if (!$field_data['match']) {
                        echo "<tr class='mismatch'>";
                        echo "<td>" . $id . "</td>";
                        echo "<td>" . htmlspecialchars($field_name) . "</td>";
                        echo "<td>" . htmlspecialchars($field_data['json'] ?? 'NULL') . "</td>";
                        echo "<td>" . htmlspecialchars($field_data['db'] ?? 'NULL') . "</td>";
                        echo "</tr>";
                    }
                }
            }
        }

        if (count($mismatches) > 10) {
            echo "<tr><td colspan='4'>... and more mismatches</td></tr>";
        }

        echo "</table>";
    }
}

echo "</div>";

// Close the database connection
$conn->close();

// End HTML output
echo "
        <p><a href='import_leads.php'>Back to Import Leads</a></p>
        <p><a href='index.php'>Back to Dashboard</a></p>
    </div>
</body>
</html>";
?>

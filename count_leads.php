<?php
/**
 * Count Leads
 *
 * This script counts the total number of leads and breaks them down by category.
 */

// Include functions file for database connection
require_once 'functions.php';

// Connect to database
$conn = connectDB();
if (!$conn) {
    die("<h1>Connection failed:</h1> <p>Database connection error</p>");
}

// Get total lead count
$result = $conn->query("SELECT COUNT(*) as total FROM leads");
$row = $result->fetch_assoc();
$total_leads = $row['total'];

// Get lead counts by category
$result = $conn->query("SELECT category, COUNT(*) as count FROM leads GROUP BY category ORDER BY count DESC");
$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[$row['category']] = $row['count'];
}

// Start HTML output
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lead Count</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .progress-bar { height: 20px; background-color: #e9ecef; border-radius: 3px; margin-top: 5px; overflow: hidden; }
        .progress-bar-fill { height: 100%; background-color: #007bff; border-radius: 3px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Lead Count</h1>

        <div class='card'>
            <h2>Total Leads</h2>
            <div class='alert alert-info'>
                <p>You have <strong>{$total_leads}</strong> total leads in your database.</p>
            </div>
        </div>

        <div class='card'>
            <h2>Leads by Category</h2>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Count</th>
                        <th>Percentage</th>
                        <th>Distribution</th>
                    </tr>
                </thead>
                <tbody>";

foreach ($categories as $category => $count) {
    $percentage = round(($count / $total_leads) * 100, 2);
    $display_name = '';

    switch ($category) {
        case 'callStack':
            $display_name = 'Call Stack';
            $color = '#0d6efd';
            break;
        case 'coldLeads':
            $display_name = 'Cold Leads';
            $color = '#0dcaf0';
            break;
        case 'warmLeads':
            $display_name = 'Warm Leads';
            $color = '#fd7e14';
            break;
        case 'currentlyWorkingWith':
            $display_name = 'Currently Working With';
            $color = '#198754';
            break;
        default:
            $display_name = $category;
            $color = '#6c757d';
    }

    echo "<tr>
            <td>{$display_name}</td>
            <td>{$count}</td>
            <td>{$percentage}%</td>
            <td>
                <div class='progress-bar'>
                    <div class='progress-bar-fill' style='width: {$percentage}%; background-color: {$color};'></div>
                </div>
            </td>
          </tr>";
}

echo "      </tbody>
            </table>
        </div>

        <p><a href='index.php'>Back to Dashboard</a></p>
    </div>
</body>
</html>";

// Close the database connection
$conn->close();
?>

<?php
/**
 * Check Database Limits
 *
 * This script checks if there are any limits in the database query that might be
 * causing the pagination issue.
 */

// Include functions file for database connection
require_once 'functions.php';

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
    <title>Database Limits Check</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
        .alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeeba; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        pre { background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Database Limits Check</h1>

        <div class='card'>
            <h2>Total Lead Count</h2>";

// Get total lead count
$result = $conn->query("SELECT COUNT(*) as total FROM leads");
$row = $result->fetch_assoc();
$total_leads = $row['total'];

echo "<div class='alert alert-info'>
        <p>Total leads in database: <strong>{$total_leads}</strong></p>
      </div>";

// Get lead counts by category
$result = $conn->query("SELECT category, COUNT(*) as count FROM leads GROUP BY category ORDER BY count DESC");
echo "<h3>Leads by Category</h3>
      <table border='1' style='width: 100%; border-collapse: collapse;'>
        <tr>
            <th>Category</th>
            <th>Count</th>
        </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['category']}</td>
            <td>{$row['count']}</td>
          </tr>";
}

echo "</table>";

// Check MySQL variables that might affect result limits
echo "</div>

      <div class='card'>
        <h2>MySQL Variables</h2>
        <p>Checking MySQL variables that might affect query results:</p>";

$variables = [
    'max_allowed_packet',
    'net_buffer_length',
    'max_connections',
    'wait_timeout',
    'interactive_timeout',
    'connect_timeout',
    'max_execution_time',
    'group_concat_max_len'
];

echo "<table border='1' style='width: 100%; border-collapse: collapse;'>
        <tr>
            <th>Variable</th>
            <th>Value</th>
        </tr>";

foreach ($variables as $var) {
    $result = $conn->query("SHOW VARIABLES LIKE '$var'");
    if ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['Variable_name']}</td>
                <td>{$row['Value']}</td>
              </tr>";
    }
}

echo "</table>";

// Test direct SQL query with different limits
echo "</div>

      <div class='card'>
        <h2>Direct SQL Query Tests</h2>";

// Test with different LIMIT values
$limits = [10, 50, 100, 500, 1000, 5000, 10000];

foreach ($limits as $limit) {
    echo "<h3>Testing LIMIT {$limit}</h3>";

    $start_time = microtime(true);
    $result = $conn->query("SELECT COUNT(*) as count FROM (SELECT id FROM leads WHERE category = 'callStack' LIMIT {$limit}) as subquery");
    $end_time = microtime(true);

    $row = $result->fetch_assoc();
    $count = $row['count'];
    $duration = round(($end_time - $start_time) * 1000, 2);

    echo "<div class='alert " . ($count == $limit || $count == $total_leads ? "alert-success" : "alert-warning") . "'>
            <p>Retrieved <strong>{$count}</strong> records in {$duration}ms</p>
          </div>";
}

// Test with PHP_INT_MAX
echo "<h3>Testing with PHP_INT_MAX</h3>";

$start_time = microtime(true);
$result = $conn->query("SELECT COUNT(*) as count FROM (SELECT id FROM leads WHERE category = 'callStack' LIMIT " . PHP_INT_MAX . ") as subquery");
$end_time = microtime(true);

$row = $result->fetch_assoc();
$count = $row['count'];
$duration = round(($end_time - $start_time) * 1000, 2);

echo "<div class='alert " . ($count == $total_leads ? "alert-success" : "alert-warning") . "'>
        <p>Retrieved <strong>{$count}</strong> records in {$duration}ms</p>
      </div>";

// Check if there's a limit in the PHP configuration
echo "</div>

      <div class='card'>
        <h2>PHP Configuration</h2>
        <p>Checking PHP configuration values that might affect query results:</p>";

$php_vars = [
    'memory_limit',
    'max_execution_time',
    'post_max_size',
    'upload_max_filesize',
    'max_input_time',
    'default_socket_timeout'
];

echo "<table border='1' style='width: 100%; border-collapse: collapse;'>
        <tr>
            <th>Setting</th>
            <th>Value</th>
        </tr>";

foreach ($php_vars as $var) {
    echo "<tr>
            <td>{$var}</td>
            <td>" . ini_get($var) . "</td>
          </tr>";
}

echo "</table>";

// Close the connection
$conn->close();

echo "</div>

        <p><a href='page-call-stack.php'>Go to Call Stack</a></p>
        <p><a href='index.php'>Back to Dashboard</a></p>
    </div>
</body>
</html>";
?>

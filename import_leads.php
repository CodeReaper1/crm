<?php
/**
 * Import Leads Script
 *
 * This script imports all leads from the combined_businesses.json file into the database,
 * replacing any existing leads.
 */

require_once 'functions.php';
require_once 'debug_functions.php';

// Start session to get user ID
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user is logged in
if (!$user_id) {
    echo "<h1>Error</h1>";
    echo "<p>You must be logged in to run this import script.</p>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

// Initialize variables
$start_time = microtime(true);
$total_leads = 0;
$imported_leads = 0;
$errors = [];
$json_file = 'combined_businesses.json';

// Connect to database
$conn = connectDB();
if (!$conn) {
    die("<h1>Connection failed:</h1> <p>Database connection error</p>");
}

// Function to display progress
function displayProgress($current, $total, $start_time) {
    $elapsed = microtime(true) - $start_time;
    $percent = ($total > 0) ? round(($current / $total) * 100, 2) : 0;
    $remaining = ($current > 0) ? ($elapsed / $current) * ($total - $current) : 0;

    echo "<div style='margin: 10px 0; padding: 10px; background-color: #f8f9fa; border-radius: 5px;'>";
    echo "<div style='margin-bottom: 5px;'>Progress: $current / $total ($percent%)</div>";
    echo "<div style='height: 20px; background-color: #e9ecef; border-radius: 3px;'>";
    echo "<div style='height: 100%; width: $percent%; background-color: #007bff; border-radius: 3px;'></div>";
    echo "</div>";
    echo "<div style='margin-top: 5px;'>Elapsed time: " . gmdate("H:i:s", $elapsed) . "</div>";
    echo "<div>Estimated time remaining: " . gmdate("H:i:s", $remaining) . "</div>";
    echo "</div>";

    // Flush output to show progress in real-time
    ob_flush();
    flush();
}

// Start HTML output
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Import Leads</title>
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
    </style>
</head>
<body>
    <div class='container'>
        <h1>Import Leads from JSON</h1>";

// Process form submission
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'import') {
    echo "<div class='card'>";
    echo "<h2>Importing Leads</h2>";

    // Check if the JSON file exists
    if (!file_exists($json_file)) {
        echo "<div class='alert alert-danger'>Error: The file $json_file does not exist.</div>";
    } else {
        // Read the JSON file
        $json_content = file_get_contents($json_file);
        $businesses = json_decode($json_content, true);

        if (!is_array($businesses)) {
            echo "<div class='alert alert-danger'>Error: Invalid JSON data in $json_file.</div>";
        } else {
            $total_leads = count($businesses);
            echo "<div class='alert alert-info'>Found $total_leads leads in the JSON file.</div>";

            // Begin transaction
            $conn->begin_transaction();

            try {
                // Check if the leads table exists
                $result = $conn->query("SHOW TABLES LIKE 'leads'");
                if ($result->num_rows === 0) {
                    // Create the leads table if it doesn't exist
                    $sql = "CREATE TABLE leads (
                        id INT(11) AUTO_INCREMENT PRIMARY KEY,
                        business_name VARCHAR(255) NOT NULL,
                        niche VARCHAR(255),
                        base_url VARCHAR(255),
                        image VARCHAR(255),
                        email VARCHAR(255),
                        phone_numbers TEXT,
                        business_description TEXT,
                        website VARCHAR(255),
                        notes TEXT,
                        status VARCHAR(50),
                        category VARCHAR(50) DEFAULT 'callStack',
                        assigned_to INT(11),
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )";

                    if (!$conn->query($sql)) {
                        throw new Exception("Failed to create leads table: " . $conn->error);
                    }

                    echo "<div class='alert alert-success'>Created leads table.</div>";
                } else {
                    // Truncate the leads table to remove all existing leads
                    if (!$conn->query("TRUNCATE TABLE leads")) {
                        throw new Exception("Failed to truncate leads table: " . $conn->error);
                    }

                    echo "<div class='alert alert-success'>Cleared existing leads from the database.</div>";
                }

                // Prepare the insert statement
                $sql = "INSERT INTO leads (id, business_name, niche, base_url, image, email, phone_numbers, business_description, website, notes, status, category, assigned_to)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);

                if (!$stmt) {
                    throw new Exception("Failed to prepare statement: " . $conn->error);
                }

                // Enable output buffering for progress updates
                ob_start();

                // Process each business
                foreach ($businesses as $index => $business) {
                    // Extract data from the business
                    $id = isset($business['id']) ? $business['id'] : ($index + 1);
                    $business_name = isset($business['business_name']) ? $business['business_name'] :
                                    (isset($business['business name']) ? $business['business name'] : 'Unknown Business');
                    $niche = isset($business['niche']) ? $business['niche'] : 'Unknown Niche';
                    $base_url = isset($business['base_url']) ? $business['base_url'] : '';
                    $image = isset($business['image']) ? $business['image'] : '';
                    $email = isset($business['email']) ? $business['email'] : '';

                    // Handle phone numbers
                    $phone_numbers = isset($business['phone_numbers']) ? $business['phone_numbers'] :
                                    (isset($business['phones']) ? $business['phones'] : []);
                    $phone_numbers_json = is_array($phone_numbers) ? json_encode($phone_numbers) : $phone_numbers;

                    $business_description = isset($business['business_description']) ? $business['business_description'] :
                                          (isset($business['business-description']) ? $business['business-description'] : '');
                    $website = isset($business['website']) ? $business['website'] : '';
                    $notes = isset($business['notes']) ? $business['notes'] : '';
                    $status = isset($business['status']) ? $business['status'] : 'active';
                    $category = isset($business['category']) ? $business['category'] : 'callStack';
                    $assigned_to = isset($business['assigned_to']) ? $business['assigned_to'] : null;

                    // Bind parameters
                    $stmt->bind_param("isssssssssssi",
                        $id, $business_name, $niche, $base_url, $image, $email, $phone_numbers_json,
                        $business_description, $website, $notes, $status, $category, $assigned_to
                    );

                    // Execute the statement
                    if ($stmt->execute()) {
                        $imported_leads++;
                    } else {
                        $errors[] = "Error importing lead ID $id: " . $stmt->error;
                    }

                    // Update progress every 100 leads
                    if ($index % 100 === 0 || $index === $total_leads - 1) {
                        displayProgress($index + 1, $total_leads, $start_time);
                    }
                }

                $stmt->close();

                // Commit the transaction
                $conn->commit();

                echo "<div class='alert alert-success'>Successfully imported $imported_leads out of $total_leads leads.</div>";

                if (!empty($errors)) {
                    echo "<div class='alert alert-danger'>";
                    echo "<h3>Errors (" . count($errors) . ")</h3>";
                    echo "<ul>";
                    foreach (array_slice($errors, 0, 10) as $error) {
                        echo "<li>" . htmlspecialchars($error) . "</li>";
                    }
                    if (count($errors) > 10) {
                        echo "<li>... and " . (count($errors) - 10) . " more errors</li>";
                    }
                    echo "</ul>";
                    echo "</div>";
                }
            } catch (Exception $e) {
                // Rollback the transaction on error
                $conn->rollback();
                echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
            }
        }
    }

    echo "</div>";

    // Show database statistics
    echo "<div class='card'>";
    echo "<h2>Database Statistics</h2>";

    $result = $conn->query("SELECT COUNT(*) as count FROM leads");
    $row = $result->fetch_assoc();
    echo "<p>Total leads in database: " . $row['count'] . "</p>";

    $result = $conn->query("SELECT category, COUNT(*) as count FROM leads GROUP BY category ORDER BY count DESC");
    echo "<table>";
    echo "<tr><th>Category</th><th>Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "</div>";
} else {
    // Show the import form
    echo "<div class='card'>";
    echo "<h2>Import Options</h2>";
    echo "<div class='alert alert-info'>";
    echo "<p>This script will import all leads from the $json_file file into the database.</p>";
    echo "<p><strong>Warning:</strong> This will delete all existing leads in the database!</p>";
    echo "</div>";

    echo "<form method='post' action=''>";
    echo "<input type='hidden' name='action' value='import'>";
    echo "<button type='submit' style='padding: 10px 20px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;'>Delete Existing Leads and Import from JSON</button>";
    echo "</form>";
    echo "</div>";

    // Show database statistics
    echo "<div class='card'>";
    echo "<h2>Current Database Statistics</h2>";

    $result = $conn->query("SELECT COUNT(*) as count FROM leads");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>Total leads in database: " . $row['count'] . "</p>";

        $result = $conn->query("SELECT category, COUNT(*) as count FROM leads GROUP BY category ORDER BY count DESC");
        if ($result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Category</th><th>Count</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                echo "<td>" . $row['count'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No leads found in the database.</p>";
        }
    } else {
        echo "<div class='alert alert-info'>The leads table does not exist yet. It will be created during import.</div>";
    }

    echo "</div>";

    // Show JSON file statistics
    echo "<div class='card'>";
    echo "<h2>JSON File Statistics</h2>";

    if (file_exists($json_file)) {
        $file_size = filesize($json_file);
        echo "<p>File size: " . round($file_size / 1024 / 1024, 2) . " MB</p>";

        // Try to get a count of leads without loading the entire file
        $json_content = file_get_contents($json_file);
        $businesses = json_decode($json_content, true);

        if (is_array($businesses)) {
            $total_leads = count($businesses);
            echo "<p>Total leads in JSON file: $total_leads</p>";

            // Count leads by category
            $categories = [];
            foreach ($businesses as $business) {
                $category = isset($business['category']) ? $business['category'] : 'callStack';
                if (!isset($categories[$category])) {
                    $categories[$category] = 0;
                }
                $categories[$category]++;
            }

            echo "<table>";
            echo "<tr><th>Category</th><th>Count</th></tr>";
            foreach ($categories as $category => $count) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($category) . "</td>";
                echo "<td>" . $count . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='alert alert-danger'>Error: Invalid JSON data in $json_file.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error: The file $json_file does not exist.</div>";
    }

    echo "</div>";
}

// Close the database connection
$conn->close();

// End HTML output
echo "
        <p><a href='index.php'>Back to Dashboard</a></p>
    </div>
</body>
</html>";
?>

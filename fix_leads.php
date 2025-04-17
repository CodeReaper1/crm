<?php
/**
 * Fix Leads Script
 *
 * This script fixes common issues with the imported leads.
 */

require_once 'functions.php';
require_once 'debug_functions.php';

// Start session to get user ID
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user is logged in
if (!$user_id) {
    echo "<h1>Error</h1>";
    echo "<p>You must be logged in to run this fix script.</p>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

// Initialize variables
$fixes_applied = [];
$errors = [];

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
    <title>Fix Leads</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
        .alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeeba; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .btn { display: inline-block; padding: 8px 16px; margin: 5px; background-color: #007bff; color: white;
               text-decoration: none; border-radius: 4px; border: none; cursor: pointer; }
        .btn-danger { background-color: #dc3545; }
        .btn-warning { background-color: #ffc107; color: #212529; }
        .btn-success { background-color: #28a745; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Fix Leads</h1>";

// Process form submission
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action) {
    echo "<div class='card'>";
    echo "<h2>Applying Fixes</h2>";

    // Begin transaction
    $conn->begin_transaction();

    try {
        switch ($action) {
            case 'fix_categories':
                // Fix invalid categories
                $sql = "SELECT id, category FROM leads WHERE category NOT IN ('callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith')";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<div class='alert alert-info'>Found " . $result->num_rows . " leads with invalid categories.</div>";

                    $update_sql = "UPDATE leads SET category = 'callStack' WHERE category NOT IN ('callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith')";
                    if ($conn->query($update_sql)) {
                        $fixes_applied[] = "Fixed " . $conn->affected_rows . " leads with invalid categories.";
                    } else {
                        $errors[] = "Failed to fix invalid categories: " . $conn->error;
                    }
                } else {
                    echo "<div class='alert alert-success'>No leads with invalid categories found.</div>";
                }
                break;

            case 'fix_phone_numbers':
                // Fix phone numbers that are not in JSON format
                $sql = "SELECT id, phone_numbers FROM leads";
                $result = $conn->query($sql);

                $fixed_count = 0;
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $phone_numbers = $row['phone_numbers'];

                    // Check if phone_numbers is not valid JSON
                    if ($phone_numbers && $phone_numbers !== 'null' && !is_null($phone_numbers)) {
                        $decoded = json_decode($phone_numbers, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            // Not valid JSON, convert to JSON array
                            $phones_array = [$phone_numbers];
                            $phones_json = json_encode($phones_array);

                            $update_sql = "UPDATE leads SET phone_numbers = ? WHERE id = ?";
                            $stmt = $conn->prepare($update_sql);
                            $stmt->bind_param("si", $phones_json, $id);

                            if ($stmt->execute()) {
                                $fixed_count++;
                            } else {
                                $errors[] = "Failed to fix phone numbers for lead ID $id: " . $stmt->error;
                            }

                            $stmt->close();
                        }
                    }
                }

                if ($fixed_count > 0) {
                    $fixes_applied[] = "Fixed phone numbers for $fixed_count leads.";
                } else {
                    echo "<div class='alert alert-success'>No leads with invalid phone numbers found.</div>";
                }
                break;

            case 'fix_null_fields':
                // Fix NULL fields that should be empty strings
                $fields = ['business_name', 'niche', 'email', 'website', 'notes', 'business_description'];
                $fixed_count = 0;

                foreach ($fields as $field) {
                    $update_sql = "UPDATE leads SET $field = '' WHERE $field IS NULL";
                    if ($conn->query($update_sql)) {
                        $fixed_count += $conn->affected_rows;
                    } else {
                        $errors[] = "Failed to fix NULL $field values: " . $conn->error;
                    }
                }

                if ($fixed_count > 0) {
                    $fixes_applied[] = "Fixed $fixed_count NULL field values.";
                } else {
                    echo "<div class='alert alert-success'>No NULL fields that need fixing found.</div>";
                }
                break;

            case 'fix_assigned_leads':
                // Fix leads that are assigned but still in callStack
                $sql = "SELECT id FROM leads WHERE category = 'callStack' AND assigned_to IS NOT NULL";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<div class='alert alert-info'>Found " . $result->num_rows . " leads in Call Stack that are assigned to users.</div>";

                    $update_sql = "UPDATE leads SET category = 'coldLeads' WHERE category = 'callStack' AND assigned_to IS NOT NULL";
                    if ($conn->query($update_sql)) {
                        $fixes_applied[] = "Moved " . $conn->affected_rows . " assigned leads from Call Stack to Cold Leads.";
                    } else {
                        $errors[] = "Failed to fix assigned leads: " . $conn->error;
                    }
                } else {
                    echo "<div class='alert alert-success'>No assigned leads in Call Stack found.</div>";
                }
                break;

            case 'fix_all':
                // Apply all fixes

                // 1. Fix invalid categories
                $update_sql = "UPDATE leads SET category = 'callStack' WHERE category NOT IN ('callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith')";
                if ($conn->query($update_sql)) {
                    $affected = $conn->affected_rows;
                    if ($affected > 0) {
                        $fixes_applied[] = "Fixed $affected leads with invalid categories.";
                    }
                } else {
                    $errors[] = "Failed to fix invalid categories: " . $conn->error;
                }

                // 2. Fix NULL fields
                $fields = ['business_name', 'niche', 'email', 'website', 'notes', 'business_description'];
                $fixed_count = 0;

                foreach ($fields as $field) {
                    $update_sql = "UPDATE leads SET $field = '' WHERE $field IS NULL";
                    if ($conn->query($update_sql)) {
                        $fixed_count += $conn->affected_rows;
                    } else {
                        $errors[] = "Failed to fix NULL $field values: " . $conn->error;
                    }
                }

                if ($fixed_count > 0) {
                    $fixes_applied[] = "Fixed $fixed_count NULL field values.";
                }

                // 3. Fix assigned leads in callStack
                $update_sql = "UPDATE leads SET category = 'coldLeads' WHERE category = 'callStack' AND assigned_to IS NOT NULL";
                if ($conn->query($update_sql)) {
                    $affected = $conn->affected_rows;
                    if ($affected > 0) {
                        $fixes_applied[] = "Moved $affected assigned leads from Call Stack to Cold Leads.";
                    }
                } else {
                    $errors[] = "Failed to fix assigned leads: " . $conn->error;
                }

                // 4. Fix phone numbers (more complex, needs row-by-row processing)
                $sql = "SELECT id, phone_numbers FROM leads";
                $result = $conn->query($sql);

                $fixed_count = 0;
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $phone_numbers = $row['phone_numbers'];

                    // Check if phone_numbers is not valid JSON
                    if ($phone_numbers && $phone_numbers !== 'null' && !is_null($phone_numbers)) {
                        $decoded = json_decode($phone_numbers, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            // Not valid JSON, convert to JSON array
                            $phones_array = [$phone_numbers];
                            $phones_json = json_encode($phones_array);

                            $update_sql = "UPDATE leads SET phone_numbers = ? WHERE id = ?";
                            $stmt = $conn->prepare($update_sql);
                            $stmt->bind_param("si", $phones_json, $id);

                            if ($stmt->execute()) {
                                $fixed_count++;
                            } else {
                                $errors[] = "Failed to fix phone numbers for lead ID $id: " . $stmt->error;
                            }

                            $stmt->close();
                        }
                    }
                }

                if ($fixed_count > 0) {
                    $fixes_applied[] = "Fixed phone numbers for $fixed_count leads.";
                }

                break;
        }

        // Commit the transaction
        $conn->commit();

        // Display results
        if (!empty($fixes_applied)) {
            echo "<div class='alert alert-success'>";
            echo "<h3>Fixes Applied</h3>";
            echo "<ul>";
            foreach ($fixes_applied as $fix) {
                echo "<li>" . htmlspecialchars($fix) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }

        if (!empty($errors)) {
            echo "<div class='alert alert-danger'>";
            echo "<h3>Errors</h3>";
            echo "<ul>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        }
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }

    echo "</div>";
}

// Show database statistics
echo "<div class='card'>";
echo "<h2>Database Statistics</h2>";

$result = $conn->query("SELECT COUNT(*) as count FROM leads");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<p>Total leads in database: " . $row['count'] . "</p>";

    // Check for potential issues
    $issues = [];

    // 1. Check for invalid categories
    $sql = "SELECT COUNT(*) as count FROM leads WHERE category NOT IN ('callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith')";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $issues[] = $row['count'] . " leads have invalid categories.";
    }

    // 2. Check for assigned leads in callStack
    $sql = "SELECT COUNT(*) as count FROM leads WHERE category = 'callStack' AND assigned_to IS NOT NULL";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $issues[] = $row['count'] . " leads are assigned to users but still in Call Stack.";
    }

    // 3. Check for NULL business_name
    $sql = "SELECT COUNT(*) as count FROM leads WHERE business_name IS NULL";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $issues[] = $row['count'] . " leads have NULL business_name.";
    }

    // Display issues if any
    if (!empty($issues)) {
        echo "<div class='alert alert-warning'>";
        echo "<h3>Potential Issues</h3>";
        echo "<ul>";
        foreach ($issues as $issue) {
            echo "<li>" . htmlspecialchars($issue) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div class='alert alert-success'>No potential issues found.</div>";
    }

    // Show lead counts by category
    $result = $conn->query("SELECT category, COUNT(*) as count FROM leads GROUP BY category ORDER BY count DESC");
    echo "<h3>Leads by Category</h3>";
    echo "<table>";
    echo "<tr><th>Category</th><th>Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['category']) . "</td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Show assigned vs unassigned leads
    $result = $conn->query("SELECT
                            SUM(CASE WHEN assigned_to IS NULL THEN 1 ELSE 0 END) as unassigned,
                            SUM(CASE WHEN assigned_to IS NOT NULL THEN 1 ELSE 0 END) as assigned
                            FROM leads");
    $row = $result->fetch_assoc();

    echo "<h3>Lead Assignment</h3>";
    echo "<table>";
    echo "<tr><th>Status</th><th>Count</th></tr>";
    echo "<tr><td>Unassigned</td><td>" . $row['unassigned'] . "</td></tr>";
    echo "<tr><td>Assigned</td><td>" . $row['assigned'] . "</td></tr>";
    echo "</table>";
} else {
    echo "<div class='alert alert-danger'>Error fetching lead statistics: " . $conn->error . "</div>";
}

echo "</div>";

// Show fix options
echo "<div class='card'>";
echo "<h2>Fix Options</h2>";
echo "<p>Select a fix to apply:</p>";

echo "<form method='post' action='' style='display: flex; flex-wrap: wrap;'>";
echo "<button type='submit' name='action' value='fix_categories' class='btn'>Fix Invalid Categories</button>";
echo "<button type='submit' name='action' value='fix_phone_numbers' class='btn'>Fix Phone Numbers</button>";
echo "<button type='submit' name='action' value='fix_null_fields' class='btn'>Fix NULL Fields</button>";
echo "<button type='submit' name='action' value='fix_assigned_leads' class='btn'>Fix Assigned Leads</button>";
echo "<button type='submit' name='action' value='fix_all' class='btn btn-warning'>Apply All Fixes</button>";
echo "</form>";

echo "</div>";

// Close the database connection
$conn->close();

// End HTML output
echo "
        <p><a href='verify_leads.php'>Verify Leads</a></p>
        <p><a href='import_leads.php'>Back to Import Leads</a></p>
        <p><a href='index.php'>Back to Dashboard</a></p>
    </div>
</body>
</html>";
?>

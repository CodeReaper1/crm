<?php
require_once 'functions.php';

// Test database connection
$conn = connectDB();
if (!$conn) {
    echo "Database connection failed!<br>";
    exit;
} else {
    echo "Database connection successful!<br>";
}

// Test retrieving leads from callStack category
$leads = getLeads('callStack', 5, 0);
echo "Retrieved " . count($leads) . " leads from callStack category<br>";

// Display the first lead
if (count($leads) > 0) {
    echo "<h3>Sample Lead:</h3>";
    echo "<pre>";
    print_r($leads[0]);
    echo "</pre>";
}

// Test retrieving leads from all categories
$categories = ['callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith'];
foreach ($categories as $category) {
    $result = getCombinedBusinesses($category, PHP_INT_MAX, 0);
    echo "Category: $category - Total leads: " . $result['total'] . "<br>";
}

// Check if the leads table exists and has the category column
$sql = "SHOW COLUMNS FROM leads LIKE 'category'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "Category column exists in leads table<br>";
} else {
    echo "Category column does not exist in leads table<br>";
}

// Check the distribution of leads across categories
$sql = "SELECT category, COUNT(*) as count FROM leads GROUP BY category";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<h3>Category Distribution:</h3>";
    while ($row = $result->fetch_assoc()) {
        echo $row['category'] . ": " . $row['count'] . " leads<br>";
    }
}

// Close connection
$conn->close();
?>

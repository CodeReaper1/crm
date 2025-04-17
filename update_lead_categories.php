<?php
// Include functions file for database connection
require_once 'functions.php';

// Create connection
$conn = connectDB();

// Check connection
if (!$conn) {
    die("Database connection failed");
}

// Categories
$categories = ['callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith'];

// Update leads with random categories
$sql = "UPDATE leads SET category = CASE
            WHEN id % 4 = 0 THEN 'callStack'
            WHEN id % 4 = 1 THEN 'coldLeads'
            WHEN id % 4 = 2 THEN 'warmLeads'
            WHEN id % 4 = 3 THEN 'currentlyWorkingWith'
        END
        WHERE category = 'callStack' OR category IS NULL";

if ($conn->query($sql) === TRUE) {
    echo "Leads categories updated successfully<br>";
} else {
    echo "Error updating leads categories: " . $conn->error . "<br>";
}

// Check the distribution
$result = $conn->query("SELECT category, COUNT(*) as count FROM leads GROUP BY category");
if ($result->num_rows > 0) {
    echo "Category distribution:<br>";
    while($row = $result->fetch_assoc()) {
        echo $row["category"] . ": " . $row["count"] . " leads<br>";
    }
}

// Close connection
$conn->close();
?>

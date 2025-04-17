<?php
// Include functions file for database connection
require_once 'functions.php';

// Create connection
$conn = connectDB();

// Check connection
if (!$conn) {
    die("Database connection failed");
}

// Categories to clear
$categories_to_clear = ['coldLeads', 'warmLeads', 'currentlyWorkingWith'];

// Move all leads from these categories to callStack
$sql = "UPDATE leads SET category = 'callStack' WHERE category IN ('coldLeads', 'warmLeads', 'currentlyWorkingWith')";

if ($conn->query($sql) === TRUE) {
    $affected = $conn->affected_rows;
    echo "Successfully moved $affected leads to Call Stack<br>";
} else {
    echo "Error updating leads: " . $conn->error . "<br>";
}

// Check the distribution after update
$result = $conn->query("SELECT category, COUNT(*) as count FROM leads GROUP BY category");
if ($result->num_rows > 0) {
    echo "Category distribution after update:<br>";
    while($row = $result->fetch_assoc()) {
        echo $row["category"] . ": " . $row["count"] . " leads<br>";
    }
}

// Close connection
$conn->close();
echo "Operation completed!";
?>

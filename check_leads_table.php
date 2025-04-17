<?php
require_once 'functions.php';

// Connect to the database
$conn = connectDB();
if (!$conn) {
    echo "Failed to connect to the database.";
    exit;
}

// Get the leads table structure
$sql = "DESCRIBE leads";
$result = $conn->query($sql);

if ($result) {
    echo "<h2>Leads Table Structure</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "Error getting table structure: " . $conn->error;
}

// Close the connection
$conn->close();
?>

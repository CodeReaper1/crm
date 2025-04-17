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

// Get a sample lead with notes
$sql = "SELECT id, business_name, notes FROM leads WHERE notes IS NOT NULL AND notes != 'No Notes' AND notes != 'noValue' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $lead = $result->fetch_assoc();
    echo "<h2>Sample Lead Notes</h2>";
    echo "<p><strong>ID:</strong> " . $lead['id'] . "</p>";
    echo "<p><strong>Business Name:</strong> " . htmlspecialchars($lead['business_name']) . "</p>";
    echo "<p><strong>Notes:</strong></p>";
    echo "<pre>" . htmlspecialchars($lead['notes']) . "</pre>";
} else {
    echo "<h2>No leads with notes found</h2>";
}

// Close the connection
$conn->close();
?>

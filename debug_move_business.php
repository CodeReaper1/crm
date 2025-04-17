<?php
require_once 'functions.php';
require_once 'debug_functions.php';
require_once 'user_activity.php';

// Start session to get user ID
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user is logged in
if (!$user_id) {
    echo "<h1>Error</h1>";
    echo "<p>You must be logged in to run this debug script.</p>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

echo "<h1>Debug Move Business</h1>";

// Get all leads
$leads = getLeads(null, 100, 0);

echo "<h2>Available Leads</h2>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr style='background-color: #f2f2f2;'><th>ID</th><th>Business Name</th><th>Category</th><th>Assigned To</th><th>Actions</th></tr>";

foreach ($leads as $lead) {
    echo "<tr>";
    echo "<td>" . $lead['id'] . "</td>";
    echo "<td>" . htmlspecialchars($lead['business_name']) . "</td>";
    echo "<td>" . formatCategoryName($lead['category']) . "</td>";
    echo "<td>" . ($lead['assigned_to'] ? $lead['assigned_to'] : 'Not Assigned') . "</td>";
    echo "<td>";
    
    // Add test move buttons
    $categories = ['callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith'];
    foreach ($categories as $category) {
        if ($category !== $lead['category']) {
            echo "<button onclick='testMove(" . $lead['id'] . ", \"" . $lead['category'] . "\", \"" . $category . "\")' style='margin: 2px;'>";
            echo "Move to " . formatCategoryName($category);
            echo "</button>";
        }
    }
    
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Add a form for manual testing
echo "<h2>Manual Test</h2>";
echo "<form id='manualTestForm'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label for='lead_id'>Lead ID:</label><br>";
echo "<input type='number' id='lead_id' name='lead_id' required style='width: 100px;'>";
echo "</div>";

echo "<div style='margin-bottom: 10px;'>";
echo "<label for='from_category'>From Category:</label><br>";
echo "<select id='from_category' name='from_category' required>";
echo "<option value='callStack'>Call Stack</option>";
echo "<option value='coldLeads'>Cold Leads</option>";
echo "<option value='warmLeads'>Warm Leads</option>";
echo "<option value='currentlyWorkingWith'>Currently Working With</option>";
echo "</select>";
echo "</div>";

echo "<div style='margin-bottom: 10px;'>";
echo "<label for='to_category'>To Category:</label><br>";
echo "<select id='to_category' name='to_category' required>";
echo "<option value='callStack'>Call Stack</option>";
echo "<option value='coldLeads'>Cold Leads</option>";
echo "<option value='warmLeads'>Warm Leads</option>";
echo "<option value='currentlyWorkingWith'>Currently Working With</option>";
echo "</select>";
echo "</div>";

echo "<button type='button' onclick='manualTest()'>Test Move</button>";
echo "</form>";

echo "<div id='result' style='margin-top: 20px; padding: 10px; border: 1px solid #ccc; display: none;'></div>";

// Helper function to format category names
function formatCategoryName($category) {
    switch($category) {
        case 'callStack': return 'Call Stack';
        case 'coldLeads': return 'Cold Leads';
        case 'warmLeads': return 'Warm Leads';
        case 'currentlyWorkingWith': return 'Currently Working With';
        default: return $category;
    }
}
?>

<script>
// Function to test moving a lead
function testMove(id, fromCategory, toCategory) {
    console.log('Testing move:', { id, fromCategory, toCategory });
    
    // Show loading message
    document.getElementById('result').style.display = 'block';
    document.getElementById('result').innerHTML = '<p>Moving lead...</p>';
    
    // Make AJAX request
    fetch('move_business.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}&from_category=${fromCategory}&to_category=${toCategory}`
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response:', data);
        
        // Display result
        let resultHtml = '<h3>Move Result</h3>';
        resultHtml += `<p>Success: ${data.success ? 'Yes' : 'No'}</p>`;
        resultHtml += `<p>Message: ${data.message}</p>`;
        
        if (data.debug) {
            resultHtml += '<h4>Debug Info</h4>';
            resultHtml += '<pre>' + JSON.stringify(data.debug, null, 2) + '</pre>';
        }
        
        document.getElementById('result').innerHTML = resultHtml;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('result').innerHTML = `<p style="color: red;">Error: ${error.message}</p>`;
    });
}

// Function for manual testing
function manualTest() {
    const id = document.getElementById('lead_id').value;
    const fromCategory = document.getElementById('from_category').value;
    const toCategory = document.getElementById('to_category').value;
    
    if (!id) {
        alert('Please enter a Lead ID');
        return;
    }
    
    if (fromCategory === toCategory) {
        alert('From and To categories must be different');
        return;
    }
    
    testMove(id, fromCategory, toCategory);
}
</script>

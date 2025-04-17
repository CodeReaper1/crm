<?php
require_once 'functions.php';

// Start session to get user ID
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user is logged in
if (!$user_id) {
    echo "<h1>Please log in first</h1>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

// Get user profile
$user = getUserProfile($user_id);
if (!$user) {
    echo "<h1>User not found</h1>";
    exit;
}

echo "<h1>Debug Note Saving</h1>";
echo "<p>Logged in as: " . htmlspecialchars($user['name']) . " (ID: $user_id)</p>";

// Get a lead to test with
$lead_id = isset($_GET['lead_id']) ? intval($_GET['lead_id']) : 1;
$lead = getLeadById($lead_id);

if (!$lead) {
    echo "<p>Lead not found with ID: $lead_id</p>";
    exit;
}

echo "<h2>Lead Information</h2>";
echo "<p><strong>ID:</strong> " . $lead['id'] . "</p>";
echo "<p><strong>Business Name:</strong> " . htmlspecialchars($lead['business_name']) . "</p>";
echo "<p><strong>Current Notes:</strong></p>";
echo "<pre>" . htmlspecialchars($lead['notes'] ?? 'No notes') . "</pre>";

// Test form
echo "<h2>Test Note Saving</h2>";
echo "<form method='post' action='save_note.php' id='testNoteForm'>";
echo "<input type='hidden' name='lead_id' value='$lead_id'>";
echo "<div class='mb-3'>";
echo "<label for='note_content'>Note Content:</label>";
echo "<textarea name='note_content' id='note_content' rows='5' style='width: 100%;' required>Test note from debug script</textarea>";
echo "</div>";
echo "<button type='submit'>Save Note</button>";
echo "</form>";

// Add JavaScript to handle form submission
echo "<script>
document.getElementById('testNoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const leadId = formData.get('lead_id');
    const noteContent = formData.get('note_content');
    
    // Log the data being sent
    console.log('Sending data:', {
        lead_id: leadId,
        note_content: noteContent
    });
    
    // Create an XMLHttpRequest
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'save_note.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        console.log('Response status:', xhr.status);
        console.log('Response text:', xhr.responseText);
        
        try {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert('Note saved successfully!');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        } catch (e) {
            console.error('Error parsing JSON:', e);
            alert('Error: Could not parse server response');
        }
    };
    
    xhr.onerror = function() {
        console.error('Request error');
        alert('An error occurred while saving the note');
    };
    
    // Send the request
    xhr.send('lead_id=' + encodeURIComponent(leadId) + '&note_content=' + encodeURIComponent(noteContent));
});
</script>";

// Add direct form submission option
echo "<h2>Direct Form Submission</h2>";
echo "<p>If the AJAX method doesn't work, try this direct form submission:</p>";
echo "<form method='post' action='save_note.php'>";
echo "<input type='hidden' name='lead_id' value='$lead_id'>";
echo "<div class='mb-3'>";
echo "<label for='direct_note_content'>Note Content:</label>";
echo "<textarea name='note_content' id='direct_note_content' rows='5' style='width: 100%;' required>Test note from direct form</textarea>";
echo "</div>";
echo "<button type='submit'>Save Note Directly</button>";
echo "</form>";

// Add a link back to the lead profile
echo "<p><a href='lead-profile.php?id=$lead_id'>Return to Lead Profile</a></p>";
?>

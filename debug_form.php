<?php
session_start();
require_once 'debug_functions.php';

// Log all POST data
debug_log("POST data received: " . json_encode($_POST));

echo "<h1>Form Debug</h1>";
echo "<h2>POST Data</h2>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

echo "<h2>Test Form</h2>";
echo "<form method='post' action='debug_form.php'>";
echo "<div>";
echo "<label for='name'>Name:</label><br>";
echo "<input type='text' id='name' name='name' value='Test User'>";
echo "</div><br>";
echo "<div>";
echo "<label for='email'>Email:</label><br>";
echo "<input type='email' id='email' name='email' value='test@example.com'>";
echo "</div><br>";
echo "<div>";
echo "<label for='job_title'>Job Title:</label><br>";
echo "<input type='text' id='job_title' name='job_title' value='Developer'>";
echo "</div><br>";
echo "<div>";
echo "<label for='location'>Location:</label><br>";
echo "<input type='text' id='location' name='location' value='Test Location'>";
echo "</div><br>";
echo "<div>";
echo "<button type='submit' name='update_profile' value='1'>Update Profile</button>";
echo "</div>";
echo "</form>";

echo "<h2>Links</h2>";
echo "<p><a href='profile.php'>Go to Profile Page</a></p>";
echo "<p><a href='direct_sql_update.php'>Go to Direct SQL Update</a></p>";
?>

<?php
// Test script for debug functions
require_once 'debug_functions.php';

debug_log("Testing debug log functionality");
debug_log("This is a warning message", "WARNING");
debug_log("This is an error message", "ERROR");

$test_data = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'job_title' => 'Developer',
    'location' => 'Test Location'
];

debug_dump($test_data, "Test user data");

echo "<h1>Debug Test</h1>";
echo "<p>Debug log entries have been written. Check the debug.log file.</p>";
?>

<?php
session_start();
require_once 'functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h1>Please log in first</h1>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

$user_id = $_SESSION['user_id'];
$user = getUserProfile($user_id);

if (!$user) {
    echo "<h1>User not found</h1>";
    exit;
}

echo "<h1>Debug Profile Update</h1>";
echo "<p>Current user: " . htmlspecialchars($user['name']) . " (ID: $user_id)</p>";

// Display current user data
echo "<h2>Current User Data</h2>";
echo "<pre>";
print_r($user);
echo "</pre>";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Form Submitted</h2>";
    echo "<p>POST data:</p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $job_title = trim($_POST['job_title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    
    echo "<p>Processed data:</p>";
    echo "<ul>";
    echo "<li>Name: " . htmlspecialchars($name) . "</li>";
    echo "<li>Email: " . htmlspecialchars($email) . "</li>";
    echo "<li>Job Title: " . htmlspecialchars($job_title) . "</li>";
    echo "<li>Location: " . htmlspecialchars($location) . "</li>";
    echo "</ul>";
    
    if (empty($name)) {
        echo "<p style='color: red;'>Error: Name is required</p>";
    } else {
        $result = updateUserProfile($user_id, [
            'name' => $name,
            'email' => $email,
            'job_title' => $job_title,
            'location' => $location
        ]);
        
        if ($result) {
            echo "<p style='color: green;'>Profile updated successfully</p>";
            // Refresh user data
            $user = getUserProfile($user_id);
            echo "<h2>Updated User Data</h2>";
            echo "<pre>";
            print_r($user);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>Failed to update profile</p>";
        }
    }
}

// Display test form
?>
<h2>Test Update Form</h2>
<form method="post" action="">
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
    </div>
    <div>
        <label for="job_title">Job Title:</label>
        <input type="text" id="job_title" name="job_title" value="<?php echo htmlspecialchars($user['job_title'] ?? ''); ?>">
    </div>
    <div>
        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
    </div>
    <div>
        <button type="submit">Update Profile</button>
    </div>
</form>

<p><a href="profile.php">Go to Profile Page</a></p>

<?php
session_start();
require_once 'debug_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h1>Error</h1>";
    echo "<p>You must be logged in to access this page.</p>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Include functions file for database connection
require_once 'functions.php';

// Database connection
$conn = connectDB();
if (!$conn) {
    die("<h1>Connection failed:</h1> <p>Database connection error</p>");
}

// Get current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<h1>Error</h1>";
    echo "<p>User not found with ID: $user_id</p>";
    exit;
}

echo "<h1>Direct SQL Update</h1>";
echo "<p>Current user: " . htmlspecialchars($user['name']) . " (ID: $user_id)</p>";

// Display current user data
echo "<h2>Current User Data</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Value</th></tr>";
foreach ($user as $field => $value) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($field) . "</td>";
    echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $job_title = trim($_POST['job_title']);
    $location = trim($_POST['location']);

    debug_log("Direct SQL update attempt - User ID: $user_id, Name: $name, Email: $email, Job Title: $job_title, Location: $location");

    // Validate data
    if (empty($name)) {
        echo "<p style='color: red;'>Name is required</p>";
    } else {
        // Direct SQL update
        $sql = "UPDATE users SET
                name = ?,
                email = ?,
                job_title = ?,
                location = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $job_title, $location, $user_id);
        $result = $stmt->execute();

        if ($result) {
            echo "<p style='color: green;'>Profile updated successfully!</p>";
            debug_log("Direct SQL update successful for user ID: $user_id");

            // Get the updated user data
            $stmt->close();
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $updated_user = $result->fetch_assoc();
            $stmt->close();

            echo "<h2>Updated User Data</h2>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Value</th></tr>";
            foreach ($updated_user as $field => $value) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($field) . "</td>";
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>Update failed: " . $stmt->error . "</p>";
            debug_log("Direct SQL update failed for user ID: $user_id - Error: " . $stmt->error, 'ERROR');
        }
    }
}

// Display the update form
?>

<h2>Update Profile</h2>
<form method="post" action="">
    <div style="margin-bottom: 10px;">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required style="width: 300px; padding: 5px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" style="width: 300px; padding: 5px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="job_title">Job Title:</label><br>
        <input type="text" id="job_title" name="job_title" value="<?php echo htmlspecialchars($user['job_title'] ?? ''); ?>" style="width: 300px; padding: 5px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label for="location">Location:</label><br>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" style="width: 300px; padding: 5px;">
    </div>
    <div>
        <button type="submit" name="update" style="padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">Update Profile</button>
    </div>
</form>

<div style="margin-top: 20px;">
    <p><a href="profile.php">Back to Profile</a></p>
    <p><a href="check_users_table.php">Check Users Table</a></p>
</div>

<?php
// Close connection
$conn->close();
?>

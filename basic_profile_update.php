<?php
session_start();
require_once 'debug_functions.php';

// Debug request information
debug_log("=== BASIC PROFILE UPDATE PAGE REQUEST ====");
debug_log("Request method: " . $_SERVER['REQUEST_METHOD']);
debug_log("Request URI: " . $_SERVER['REQUEST_URI']);
debug_log("POST data: " . json_encode($_POST));
debug_log("GET data: " . json_encode($_GET));
debug_log("===========================");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Include functions file for database connection
require_once 'functions.php';

// Database connection
$conn = connectDB();
if (!$conn) {
    die("Database connection failed");
}

// Get current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Process form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_log("Processing POST request in basic_profile_update.php");

    // Update user data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $job_title = isset($_POST['job_title']) ? trim($_POST['job_title']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';

    debug_log("Form data - Name: $name, Email: $email, Job Title: $job_title, Location: $location");

    if (empty($name)) {
        $message = '<div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 20px;">Name is required</div>';
    } else {
        // Update user profile
        $sql = "UPDATE users SET name = ?, email = ?, job_title = ?, location = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $job_title, $location, $user_id);
        $result = $stmt->execute();

        if ($result) {
            $message = '<div style="color: green; padding: 10px; border: 1px solid green; margin-bottom: 20px;">Profile updated successfully</div>';
            debug_log("Profile updated successfully in basic_profile_update.php");

            // Refresh user data
            $stmt->close();
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();
        } else {
            $message = '<div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 20px;">Failed to update profile: ' . $stmt->error . '</div>';
            debug_log("Update failed in basic_profile_update.php: " . $stmt->error, 'ERROR');
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basic Profile Update</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Basic Profile Update</h1>

        <?php echo $message; ?>

        <h2>Current User Data</h2>
        <table>
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
            <?php foreach ($user as $field => $value): ?>
            <tr>
                <td><?php echo htmlspecialchars($field); ?></td>
                <td><?php echo htmlspecialchars($value ?? 'NULL'); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Update Profile</h2>
        <form method="post" action="basic_profile_update.php">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" name="job_title" value="<?php echo htmlspecialchars($user['job_title'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>">
            </div>

            <button type="submit">Update Profile</button>
        </form>

        <p style="margin-top: 20px;"><a href="profile.php">Back to Main Profile Page</a></p>
    </div>
</body>
</html>

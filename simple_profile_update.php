<?php
session_start();
require_once 'functions.php';
require_once 'debug_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Include functions file for database connection
require_once 'functions.php';

// Get user data directly from database
$conn = connectDB();
if (!$conn) {
    die("Database connection failed");
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Process form submission
$success_message = '';
$error_message = '';

debug_log("Request method: " . $_SERVER['REQUEST_METHOD']);
debug_log("POST data: " . json_encode($_POST));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_log("Form submitted");

    if (isset($_POST['update_profile'])) {
        debug_log("Update profile form submitted");

        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $job_title = trim($_POST['job_title']);
        $location = trim($_POST['location']);

        debug_log("Form data - Name: $name, Email: $email, Job Title: $job_title, Location: $location");

        if (empty($name)) {
            $error_message = 'Name is required';
            debug_log("Validation error: Name is required");
        } else {
            // Update user profile
            $sql = "UPDATE users SET name = ?, email = ?, job_title = ?, location = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $name, $email, $job_title, $location, $user_id);
            $result = $stmt->execute();

            if ($result) {
                $success_message = 'Profile updated successfully';
                debug_log("Profile updated successfully");

                // Refresh user data
                $stmt->close();
                $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();
            } else {
                $error_message = 'Failed to update profile: ' . $stmt->error;
                debug_log("Update failed: " . $stmt->error, 'ERROR');
            }
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
    <title>Simple Profile Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background-color: #4CAF50; color: white; }
        .alert { padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .alert-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Simple Profile Update</h1>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Current User Data</h2>
            <table border="1" cellpadding="5" style="width: 100%; border-collapse: collapse;">
                <tr style="background-color: #f2f2f2;">
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
        </div>

        <div class="card">
            <h2>Update Profile</h2>
            <form method="post" action="">
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

                <button type="submit" name="update_profile" value="1" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <p><a href="profile.php">Back to Main Profile Page</a></p>
    </div>
</body>
</html>

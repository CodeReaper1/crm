<?php
session_start();
require_once 'functions.php';
require_once 'debug_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<h1>Error</h1>";
    echo "<p>You must be logged in to access this page.</p>";
    echo "<p><a href='login.php'>Go to login page</a></p>";
    exit;
}

$user_id = $_SESSION['user_id'];
$user = getUserProfile($user_id);

if (!$user) {
    echo "<h1>Error</h1>";
    echo "<p>User not found with ID: $user_id</p>";
    exit;
}

echo "<h1>Direct Profile Update</h1>";
echo "<p>Current user: " . htmlspecialchars($user['name']) . " (ID: $user_id)</p>";

// Display current user data
echo "<h2>Current User Data</h2>";
echo "<pre>";
print_r($user);
echo "</pre>";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $job_title = trim($_POST['job_title']);
    $location = trim($_POST['location']);

    // Validate data
    if (empty($name)) {
        echo "<p style='color: red;'>Name is required</p>";
    } else {
        // Include functions file for database connection
        require_once 'functions.php';

        // Connect to database
        $conn = connectDB();

        if (!$conn) {
            echo "<p style='color: red;'>Database connection failed</p>";
            exit;
        }

        // Check if the users table has the required columns
        $result = $conn->query("SHOW COLUMNS FROM users");
        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }

        echo "<h3>Available Columns</h3>";
        echo "<pre>";
        print_r($columns);
        echo "</pre>";

        // Build the SQL query
        $sql = "UPDATE users SET name = ?";
        $params = [$name];
        $types = "s";

        if (in_array('email', $columns)) {
            $sql .= ", email = ?";
            $params[] = $email;
            $types .= "s";
        }

        if (in_array('job_title', $columns)) {
            $sql .= ", job_title = ?";
            $params[] = $job_title;
            $types .= "s";
        }

        if (in_array('location', $columns)) {
            $sql .= ", location = ?";
            $params[] = $location;
            $types .= "s";
        }

        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";

        echo "<h3>SQL Query</h3>";
        echo "<p>" . htmlspecialchars($sql) . "</p>";

        // Execute the query
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo "<p style='color: red;'>Prepare failed: " . $conn->error . "</p>";
            $conn->close();
            exit;
        }

        $stmt->bind_param($types, ...$params);
        $result = $stmt->execute();

        if ($result) {
            echo "<p style='color: green;'>Profile updated successfully!</p>";

            // Check affected rows
            if ($stmt->affected_rows > 0) {
                echo "<p>Rows affected: " . $stmt->affected_rows . "</p>";
            } else {
                echo "<p style='color: orange;'>No rows were affected. The data might be the same as before.</p>";
            }

            // Get the updated user data
            $stmt->close();
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $updated_user = $result->fetch_assoc();

            echo "<h3>Updated User Data</h3>";
            echo "<pre>";
            print_r($updated_user);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>Update failed: " . $stmt->error . "</p>";
        }

        $stmt->close();
        $conn->close();
    }
}

// Display the update form
?>

<h2>Update Profile</h2>
<form method="post" action="">
    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
    </div>
    <div>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>">
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
        <button type="submit" name="update">Update Profile</button>
    </div>
</form>

<p><a href="profile.php">Back to Profile</a></p>

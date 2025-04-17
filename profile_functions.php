<?php
/**
 * Enhanced profile functions with debugging
 */
require_once 'functions.php';
require_once 'debug_functions.php';

/**
 * Enhanced version of updateUserProfile with detailed debugging
 *
 * @param int $user_id User's ID
 * @param array $data User data to update
 * @return bool Success status
 */
function enhanced_updateUserProfile($user_id, $data) {
    debug_log("Starting enhanced_updateUserProfile for user ID: $user_id");
    debug_log("Update data: " . json_encode($data));
    
    // Check database connection
    $conn = connectDB();
    if (!$conn) {
        debug_log("Database connection failed", 'ERROR');
        return false;
    }
    debug_log("Database connection successful");
    
    // Check if the user exists
    $sql = "SELECT id FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        debug_log("Failed to prepare statement: " . $conn->error, 'ERROR');
        $conn->close();
        return false;
    }
    
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        debug_log("User not found with ID: $user_id", 'ERROR');
        $stmt->close();
        $conn->close();
        return false;
    }
    debug_log("User found with ID: $user_id");
    $stmt->close();
    
    // Check table columns
    $columns = [];
    $result = $conn->query("SHOW COLUMNS FROM users");
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
    debug_log("Available columns in users table: " . implode(", ", $columns));
    
    // Prepare the SQL statement
    $sql = "UPDATE users SET ";
    $params = [];
    $types = "";
    
    // Add each field to the SQL statement if the column exists
    if (isset($data['name'])) {
        $sql .= "name = ?, ";
        $params[] = $data['name'];
        $types .= "s";
        debug_log("Adding name field to update: " . $data['name']);
    }
    
    if (isset($data['email']) && in_array('email', $columns)) {
        $sql .= "email = ?, ";
        $params[] = $data['email'];
        $types .= "s";
        debug_log("Adding email field to update: " . $data['email']);
    }
    
    if (isset($data['job_title']) && in_array('job_title', $columns)) {
        $sql .= "job_title = ?, ";
        $params[] = $data['job_title'];
        $types .= "s";
        debug_log("Adding job_title field to update: " . $data['job_title']);
    }
    
    if (isset($data['location']) && in_array('location', $columns)) {
        $sql .= "location = ?, ";
        $params[] = $data['location'];
        $types .= "s";
        debug_log("Adding location field to update: " . $data['location']);
    }
    
    // If no fields to update, return true (no changes needed)
    if (count($params) === 0) {
        debug_log("No fields to update, returning success");
        $conn->close();
        return true;
    }
    
    // Remove trailing comma and space
    $sql = rtrim($sql, ", ");
    
    // Add WHERE clause
    $sql .= " WHERE id = ?";
    $params[] = $user_id;
    $types .= "i";
    
    debug_log("Final SQL: $sql");
    debug_log("Parameter types: $types");
    debug_log("Parameters: " . json_encode($params));
    
    // Execute the statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        debug_log("Failed to prepare update statement: " . $conn->error, 'ERROR');
        $conn->close();
        return false;
    }
    
    $stmt->bind_param($types, ...$params);
    $result = $stmt->execute();
    
    if (!$result) {
        debug_log("Execute failed: " . $stmt->error, 'ERROR');
    } else {
        debug_log("Execute successful, affected rows: " . $stmt->affected_rows);
    }
    
    $stmt->close();
    $conn->close();
    
    debug_log("Update result: " . ($result ? 'true' : 'false'));
    return $result;
}
?>

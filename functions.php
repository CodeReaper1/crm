<?php
/**
 * CRM System Functions
 *
 * This file contains all the core functions for the CRM system.
 * It is designed to work both with static data and a database.
 */

/******************************
 * CONFIGURATIONS
 ******************************/

define('USE_DATABASE', true); // Set to true when ready to switch to database
define('DB_HOST', 'localhost');
define('DB_USER', 'apexcai8_gemo');
define('DB_PASS', 'nLE$oxqcmP8-');
define('DB_NAME', 'apexcai8_gemo');
define('STATIC_DATA_FILE', 'data/leads.json');
define('COMBINED_DATA_FILE', 'combined_businesses.json');
define('ASSET_URL', 'https://crm2.apexdigital.dev/assets/');

/******************************
 * ASSET MANAGEMENT
 ******************************/

$registered_scripts = [];
$registered_styles = [];
$enqueued_scripts = [];
$enqueued_styles = [];

/**
 * Registers a script with its source, dependencies, and position.
 *
 * @param string $handle Script identifier
 * @param string $src Script file path
 * @param array $deps Dependencies array
 * @param bool $in_footer Whether to load in footer
 */
function register_script($handle, $src, $deps = [], $in_footer = false) {
    global $registered_scripts;
    $registered_scripts[$handle] = [
        'src' => ASSET_URL . $src,
        'deps' => $deps,
        'in_footer' => $in_footer
    ];
}

/**
 * Registers a stylesheet with its source and dependencies.
 *
 * @param string $handle Style identifier
 * @param string $src Style file path
 * @param array $deps Dependencies array
 */
function register_style($handle, $src, $deps = []) {
    global $registered_styles;
    $registered_styles[$handle] = [
        'src' => ASSET_URL . $src,
        'deps' => $deps
    ];
}

/**
 * Enqueues a script for loading, ensuring dependencies are included.
 *
 * @param string $handle Script identifier
 * @return bool Success status
 */
function enqueue_script($handle) {
    global $registered_scripts, $enqueued_scripts;
    if (!isset($registered_scripts[$handle])) {
        return false;
    }
    foreach ($registered_scripts[$handle]['deps'] as $dep) {
        enqueue_script($dep);
    }
    if (!in_array($handle, $enqueued_scripts)) {
        $enqueued_scripts[] = $handle;
    }
    return true;
}

/**
 * Enqueues a stylesheet for loading, ensuring dependencies are included.
 *
 * @param string $handle Style identifier
 * @return bool Success status
 */
function enqueue_style($handle) {
    global $registered_styles, $enqueued_styles;
    if (!isset($registered_styles[$handle])) {
        return false;
    }
    foreach ($registered_styles[$handle]['deps'] as $dep) {
        enqueue_style($dep);
    }
    if (!in_array($handle, $enqueued_styles)) {
        $enqueued_styles[] = $handle;
    }
    return true;
}

/**
 * Prints all enqueued styles in the HTML head.
 */
function print_styles() {
    global $enqueued_styles, $registered_styles;
    foreach ($enqueued_styles as $handle) {
        $style = $registered_styles[$handle];
        echo '<link href="' . $style['src'] . '" rel="stylesheet">' . "\n";
    }
}

/**
 * Prints all enqueued scripts designated for the head.
 */
function print_head_scripts() {
    global $enqueued_scripts, $registered_scripts;
    foreach ($enqueued_scripts as $handle) {
        $script = $registered_scripts[$handle];
        if (!$script['in_footer']) {
            echo '<script src="' . $script['src'] . '"></script>' . "\n";
        }
    }
}

/**
 * Prints all enqueued scripts designated for the footer.
 */
function print_footer_scripts() {
    global $enqueued_scripts, $registered_scripts;
    foreach ($enqueued_scripts as $handle) {
        $script = $registered_scripts[$handle];
        if ($script['in_footer']) {
            echo '<script src="' . $script['src'] . '"></script>' . "\n";
        }
    }
}

/**
 * Enqueues all CRM-specific assets (styles and scripts).
 */
function enqueue_crm_assets() {
    // Register Styles
    register_style('pace', 'css/pace.min.css');
    register_style('simplebar', 'plugins/simplebar/css/simplebar.css');
    register_style('perfect-scrollbar', 'plugins/perfect-scrollbar/css/perfect-scrollbar.css');
    register_style('metismenu', 'plugins/metismenu/css/metisMenu.min.css');
    register_style('bootstrap', 'css/bootstrap.min.css');
    register_style('bootstrap-extended', 'css/bootstrap-extended.css', ['bootstrap']);
    register_style('style', 'css/style.css');
    register_style('icons', 'css/icons.css');
    register_style('dark-theme', 'css/dark-theme.css');
    register_style('semi-dark', 'css/semi-dark.css');
    register_style('header-colors', 'css/header-colors.css');
    register_style('datatable-custom', 'css/datatable-custom.css');
    register_style('button-fix', 'css/button-fix.css');

    // Register Scripts
    register_script('pace', 'js/pace.min.js', [], false);
    register_script('jquery', 'js/jquery.min.js', [], false);
    register_script('bootstrap', 'js/bootstrap.bundle.min.js', ['jquery'], true);
    register_script('simplebar', 'plugins/simplebar/js/simplebar.min.js', ['jquery'], true);
    register_script('metismenu', 'plugins/metismenu/js/metisMenu.min.js', ['jquery'], true);
    register_script('perfect-scrollbar', 'plugins/perfect-scrollbar/js/perfect-scrollbar.js', ['jquery'], true);
    register_script('apexcharts', 'plugins/apexcharts-bundle/js/apexcharts.min.js', ['jquery'], true);
    register_script('main', 'js/main.js', ['jquery', 'bootstrap'], true);

    // Enqueue Styles
    enqueue_style('pace');
    enqueue_style('simplebar');
    enqueue_style('perfect-scrollbar');
    enqueue_style('metismenu');
    enqueue_style('bootstrap');
    enqueue_style('bootstrap-extended');
    enqueue_style('style');
    enqueue_style('icons');
    enqueue_style('dark-theme');
    enqueue_style('semi-dark');
    enqueue_style('header-colors');
    enqueue_style('datatable-custom');
    enqueue_style('button-fix');

    // Enqueue Scripts
    enqueue_script('pace');
    enqueue_script('jquery');
    enqueue_script('bootstrap');
    enqueue_script('simplebar');
    enqueue_script('metismenu');
    enqueue_script('perfect-scrollbar');
    enqueue_script('apexcharts');
    enqueue_script('main');
}

/******************************
 * DATABASE CONNECTION
 ******************************/

/**
 * Establishes a connection to the database if enabled.
 *
 * @return mysqli|null Database connection or null if disabled/failed
 */
function connectDB() {
    if (!USE_DATABASE) {
        return null;
    }
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        return null;
    }
    return $conn;
}

/******************************
 * USER MANAGEMENT
 ******************************/

/**
 * Registers a new user with a name, email, and password.
 *
 * @param string $name User's name
 * @param string $email User's email
 * @param string $password User's password
 * @return bool Success status
 */
function registerUser($name, $email, $password) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return false;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    } else {
        // Static mode not implemented for users
        return false;
    }
}

/**
 * Authenticates a user and returns their ID if successful.
 *
 * @param string $email User's email
 * @param string $password User's password
 * @return int|bool User ID or false if login fails
 */
function loginUser($email, $password) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return false;
        $sql = "SELECT id, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                return $row['id'];
            }
        }
        $stmt->close();
        $conn->close();
        return false;
    } else {
        // Static mode not implemented for users
        return false;
    }
}

/**
 * Retrieves a user's profile by their ID.
 *
 * @param int $user_id User's ID
 * @return array|null User data or null if not found
 */
function getUserProfile($user_id) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return null;

        // First, check if the columns exist in the table
        $columns = [];
        $result = $conn->query("SHOW COLUMNS FROM users");
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }

        // Build the SQL query based on existing columns
        $select_columns = ['id', 'name'];
        if (in_array('email', $columns)) $select_columns[] = 'email';
        if (in_array('job_title', $columns)) $select_columns[] = 'job_title';
        if (in_array('location', $columns)) $select_columns[] = 'location';
        if (in_array('profile_picture', $columns)) $select_columns[] = 'profile_picture';

        $sql = "SELECT " . implode(', ', $select_columns) . " FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            // If prepare fails, try a simpler query
            $sql = "SELECT id, name FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                $conn->close();
                return null;
            }
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user;
    } else {
        // Static mode not implemented for users
        return null;
    }
}

/**
 * Updates a user's profile details.
 *
 * @param int $user_id User's ID
 * @param array $data User data to update
 * @return bool Success status
 */
function updateUserProfile($user_id, $data) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return false;

        // First, check if the columns exist in the table
        $columns = [];
        $result = $conn->query("SHOW COLUMNS FROM users");
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }

        // Prepare the SQL statement
        $sql = "UPDATE users SET ";
        $params = [];
        $types = "";

        // Add each field to the SQL statement if the column exists
        if (isset($data['name'])) {
            $sql .= "name = ?, ";
            $params[] = $data['name'];
            $types .= "s";
        }

        if (isset($data['email']) && in_array('email', $columns)) {
            $sql .= "email = ?, ";
            $params[] = $data['email'];
            $types .= "s";
        }

        if (isset($data['job_title']) && in_array('job_title', $columns)) {
            $sql .= "job_title = ?, ";
            $params[] = $data['job_title'];
            $types .= "s";
        }

        if (isset($data['location']) && in_array('location', $columns)) {
            $sql .= "location = ?, ";
            $params[] = $data['location'];
            $types .= "s";
        }

        // If no fields to update, return true (no changes needed)
        if (count($params) === 0) {
            $conn->close();
            return true;
        }

        // Remove trailing comma and space
        $sql = rtrim($sql, ", ");

        // Add WHERE clause
        $sql .= " WHERE id = ?";
        $params[] = $user_id;
        $types .= "i";

        // Execute the statement
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $conn->close();
            return false;
        }

        $stmt->bind_param($types, ...$params);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();

        return $result;
    } else {
        // Static mode not implemented for users
        return false;
    }
}

/**
 * Updates a user's avatar.
 *
 * @param int $user_id User's ID
 * @param string $avatar_path New avatar path
 * @return bool Success status
 */
function updateUserAvatar($user_id, $avatar_path) {
    $conn = connectDB();
    if (!$conn) return false;

    // Check if profile_picture column exists
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'profile_picture'");
    if ($result->num_rows === 0) {
        // Column doesn't exist, try to add it
        $conn->query("ALTER TABLE users ADD COLUMN profile_picture VARCHAR(255)");
    }

    $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $conn->close();
        return false;
    }

    $stmt->bind_param("si", $avatar_path, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

/**
 * Changes a user's password.
 *
 * @param int $user_id User's ID
 * @param string $current_password Current password
 * @param string $new_password New password
 * @return bool|string True on success, error message on failure
 */
function changeUserPassword($user_id, $current_password, $new_password) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return 'Database connection failed';

        // First, verify the current password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $conn->close();
            return 'Database error: ' . $conn->error;
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            $conn->close();
            return 'User not found';
        }

        $user = $result->fetch_assoc();
        $stmt->close();

        // Verify the current password
        if (!password_verify($current_password, $user['password'])) {
            $conn->close();
            return 'Current password is incorrect';
        }

        // Update the password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $conn->close();
            return 'Database error: ' . $conn->error;
        }

        $stmt->bind_param("si", $hashed_password, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();

        return $result;
    } else {
        // Static mode not implemented for users
        return 'Password change not supported in static mode';
    }
}


/**
 * Gets lead statistics for a user.
 * Call Stack is shared among all users, while other categories are user-specific.
 *
 * @param int $user_id User's ID
 * @return array Lead statistics
 */
function getUserLeadStats($user_id) {
    $stats = [
        'callStack' => 0,
        'coldLeads' => 0,
        'warmLeads' => 0,
        'currentlyWorkingWith' => 0,
        'total' => 0
    ];

    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return $stats;

        // Check if the leads table exists
        $result = $conn->query("SHOW TABLES LIKE 'leads'");
        if ($result->num_rows == 0) {
            // Table doesn't exist, return default stats
            $conn->close();
            return $stats;
        }

        // Get count for Call Stack (shared among all users)
        $sql = "SELECT COUNT(*) as count FROM leads WHERE category = 'callStack'";
        $result = $conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            $stats['callStack'] = $row['count'];
            $stats['total'] += $row['count'];
        }

        // Get counts for user-specific categories
        $sql = "SELECT category, COUNT(*) as count FROM leads WHERE assigned_to = ? AND category != 'callStack' GROUP BY category";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                if (isset($stats[$row['category']])) {
                    $stats[$row['category']] = $row['count'];
                    $stats['total'] += $row['count'];
                }
            }

            $stmt->close();
        }
        $conn->close();
    } else {
        // Static mode - count leads from the combined file
        if (file_exists(COMBINED_DATA_FILE)) {
            $json_content = file_get_contents(COMBINED_DATA_FILE);
            $businesses = json_decode($json_content, true);

            if (is_array($businesses)) {
                foreach ($businesses as $business) {
                    if (isset($business['category'])) {
                        // For Call Stack, count all leads
                        if ($business['category'] === 'callStack') {
                            $stats['callStack']++;
                            $stats['total']++;
                        }
                        // For other categories, only count leads assigned to the user
                        else if (isset($business['assigned_to']) && $business['assigned_to'] == $user_id) {
                            if (isset($stats[$business['category']])) {
                                $stats[$business['category']]++;
                                $stats['total']++;
                            }
                        }
                    }
                }
            }
        }
    }

    return $stats;
}

/******************************
 * LEAD MANAGEMENT
 ******************************/

/**
 * Adds a new lead with optional assignment to a user.
 *
 * @param array $data Lead data
 * @param int|null $assigned_to User ID to assign to
 * @return int|bool New lead ID or false on failure
 */
function addLead($data, $assigned_to = null) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return false;
        $sql = "INSERT INTO leads (business_name, niche, base_url, image, email, phone_numbers, business_description, website, notes, status, assigned_to)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Convert empty strings to NULL and serialize phone_numbers array
        foreach ($data as &$value) {
            if ($value === '') {
                $value = null;
            }
        }
        $data['phone_numbers'] = json_encode($data['phones']);

        $stmt->bind_param("ssssssssssi",
            $data['business_name'], $data['niche'], $data['base_url'], $data['image'],
            $data['email'], $data['phone_numbers'], $data['business_description'],
            $data['website'], $data['notes'], $data['status'], $assigned_to);
        $result = $stmt->execute();
        $new_id = $conn->insert_id;
        $stmt->close();
        $conn->close();
        return $result ? $new_id : false;
    } else {
        $leads = getStaticLeads();
        if (empty($leads)) {
            $max_id = 0;
        } else {
            $max_id = max(array_column($leads, 'id'));
        }
        $max_id++;
        $data['phone_numbers'] = json_encode($data['phones']);
        $new_lead = $data + ['id' => $max_id, 'assigned_to' => $assigned_to];
        $leads[] = $new_lead;
        return saveStaticLeads($leads) ? $max_id : false;
    }
}

/**
 * Assigns an unassigned lead to a user and sets category to 'cold'.
 *
 * @param int $lead_id Lead ID
 * @param int $user_id User ID
 * @return bool Success status
 */
function assignLeadToUser($lead_id, $user_id) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return false;
        $sql = "UPDATE leads SET assigned_to = ? WHERE id = ? AND assigned_to IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $user_id, $lead_id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    } else {
        $leads = getStaticLeads();
        foreach ($leads as &$lead) {
            if ($lead['id'] == $lead_id && !isset($lead['assigned_to'])) {
                $lead['assigned_to'] = $user_id;
                return saveStaticLeads($leads);
            }
        }
        return false;
    }
}

/**
 * Updates a lead with the provided data.
 *
 * @param int $id The lead ID
 * @param array $data The lead data to update
 * @return bool True if the update was successful, false otherwise
 */
function updateLead($id, $data) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return false;

        // Prepare the SQL statement
        $sql = "UPDATE leads SET ";
        $params = [];
        $types = "";

        // Add each field to the SQL statement
        if (isset($data['business_name'])) {
            $sql .= "business_name = ?, ";
            $params[] = $data['business_name'];
            $types .= "s";
        }

        if (isset($data['niche'])) {
            $sql .= "niche = ?, ";
            $params[] = $data['niche'];
            $types .= "s";
        }

        if (isset($data['email'])) {
            $sql .= "email = ?, ";
            $params[] = $data['email'];
            $types .= "s";
        }

        if (isset($data['phone_numbers'])) {
            $sql .= "phone_numbers = ?, ";
            $params[] = json_encode($data['phone_numbers']);
            $types .= "s";
        }

        if (isset($data['website'])) {
            $sql .= "website = ?, ";
            $params[] = $data['website'];
            $types .= "s";
        }

        if (isset($data['notes'])) {
            $sql .= "notes = ?, ";
            $params[] = $data['notes'];
            $types .= "s";
        }

        if (isset($data['category'])) {
            $sql .= "category = ?, ";
            $params[] = $data['category'];
            $types .= "s";
        }

        // Add updated_at timestamp
        $sql .= "updated_at = NOW() ";

        // Add WHERE clause
        $sql .= "WHERE id = ?";
        $params[] = $id;
        $types .= "i";

        // Execute the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        $conn->close();

        return $result && $affected > 0;
    } else {
        // Update lead in the JSON file
        if (!file_exists(COMBINED_DATA_FILE)) {
            return false;
        }

        $json_content = file_get_contents(COMBINED_DATA_FILE);
        $businesses = json_decode($json_content, true);

        if (!is_array($businesses)) {
            return false;
        }

        $found = false;
        foreach ($businesses as $key => $business) {
            if (isset($business['id']) && $business['id'] == $id) {
                // Update each field
                if (isset($data['business_name'])) {
                    $businesses[$key]['business_name'] = $data['business_name'];
                    $businesses[$key]['business name'] = $data['business_name'];
                }

                if (isset($data['niche'])) {
                    $businesses[$key]['niche'] = $data['niche'];
                }

                if (isset($data['email'])) {
                    $businesses[$key]['email'] = $data['email'];
                }

                if (isset($data['phone_numbers'])) {
                    $businesses[$key]['phone_numbers'] = $data['phone_numbers'];
                    $businesses[$key]['phones'] = $data['phone_numbers'];
                }

                if (isset($data['website'])) {
                    $businesses[$key]['website'] = $data['website'];
                }

                if (isset($data['notes'])) {
                    $businesses[$key]['notes'] = $data['notes'];
                }

                if (isset($data['category'])) {
                    $businesses[$key]['category'] = $data['category'];
                }

                $found = true;
                break;
            }
        }

        if ($found) {
            $json_content = json_encode($businesses, JSON_PRETTY_PRINT);
            return file_put_contents(COMBINED_DATA_FILE, $json_content) !== false;
        }

        return false;
    }
}

/**
 * Gets a user's name by their ID.
 *
 * @param int $user_id The user ID
 * @return string The user's name or 'Unknown User' if not found
 */
function getUserName($user_id) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return 'Unknown User';

        $sql = "SELECT name FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            $conn->close();
            return 'Unknown User';
        }

        $user = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $user['name'];
    } else {
        // For now, just return a placeholder
        return 'User ' . $user_id;
    }
}

/**
 * Retrieves a lead by its ID.
 *
 * @param int $id The lead ID
 * @return array|null The lead data or null if not found
 */
function getLeadById($id) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return null;

        $sql = "SELECT * FROM leads WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            $conn->close();
            return null;
        }

        $lead = $result->fetch_assoc();

        // Process phone numbers if it's a JSON string
        if (isset($lead['phone_numbers']) && is_string($lead['phone_numbers'])) {
            $lead['phone_numbers'] = json_decode($lead['phone_numbers'], true);
        }

        $stmt->close();
        $conn->close();
        return $lead;
    } else {
        // Use the combined businesses JSON file
        if (!file_exists(COMBINED_DATA_FILE)) {
            return null;
        }

        $json_content = file_get_contents(COMBINED_DATA_FILE);
        $businesses = json_decode($json_content, true);

        if (!is_array($businesses)) {
            return null;
        }

        foreach ($businesses as $business) {
            if (isset($business['id']) && $business['id'] == $id) {
                // Ensure all required fields exist
                $business['business_name'] = isset($business['business name']) ? $business['business name'] : 'Unknown Business';
                $business['niche'] = isset($business['niche']) ? $business['niche'] : 'Unknown Niche';
                $business['email'] = isset($business['email']) ? $business['email'] : 'no-email@example.com';
                $business['phone_numbers'] = isset($business['phones']) ? $business['phones'] : ['No Phone'];
                $business['website'] = isset($business['website']) ? $business['website'] : 'No Website';
                $business['notes'] = isset($business['notes']) ? $business['notes'] : 'No Notes';
                $business['business_description'] = isset($business['business-description']) ? $business['business-description'] : 'No Description';

                // Assign a category if not set
                if (!isset($business['category'])) {
                    $business['category'] = 'callStack';
                }

                return $business;
            }
        }

        return null;
    }
}

/**
 * Retrieves leads based on category and search filters.
 * Call Stack is shared among all users, while other categories are user-specific.
 *
 * @param string $category Category to filter by (callStack, coldLeads, warmLeads, currentlyWorkingWith)
 * @param int $limit Number of leads to retrieve
 * @param int $offset Offset for pagination
 * @param string $order_column Column to order by
 * @param string $order_dir Direction to order (ASC or DESC)
 * @param string $search_value Search term to filter by
 * @param int $user_id User ID for filtering user-specific categories (optional)
 * @param bool $has_notes Filter to only show leads with notes (optional)
 * @return array Leads array
 */
function getLeads($category = null, $limit = 10, $offset = 0, $order_column = 'business_name', $order_dir = 'ASC', $search_value = '', $user_id = null, $has_notes = false) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return [];

        // If user_id is not provided, try to get it from the session
        if ($user_id === null && isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
        }

        // Validate order column to prevent SQL injection
        $allowed_columns = ['id', 'business_name', 'niche', 'email', 'website', 'notes', 'category', 'created_at', 'updated_at'];
        if (!in_array($order_column, $allowed_columns)) {
            $order_column = 'business_name'; // Default to business_name if invalid column
        }

        // Validate order direction
        $order_dir = strtoupper($order_dir);
        if ($order_dir !== 'ASC' && $order_dir !== 'DESC') {
            $order_dir = 'ASC'; // Default to ASC if invalid direction
        }

        $sql = "SELECT * FROM leads WHERE 1=1";
        $params = [];
        $types = "";

        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
            $types .= "s";

            // For categories other than callStack, filter by user_id
            if ($category !== 'callStack' && $user_id !== null) {
                $sql .= " AND assigned_to = ?";
                $params[] = $user_id;
                $types .= "i";
            }
        }

        // Filter for leads with notes if requested
        if ($has_notes) {
            $sql .= " AND notes IS NOT NULL AND notes != ''";
        }

        if (!empty($search_value)) {
            $sql .= " AND (niche LIKE ? OR business_name LIKE ? OR email LIKE ? OR phone_numbers LIKE ? OR website LIKE ? OR notes LIKE ?)";
            $search_value = "%$search_value%";
            $params = array_merge($params, array_fill(0, 6, $search_value));
            $types .= str_repeat("s", 6);
        }

        // Add pagination only if limit is not -1 (which means get all)
        if ($limit > 0) {
            $sql .= " ORDER BY $order_column $order_dir LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";
        } else {
            $sql .= " ORDER BY $order_column $order_dir";
        }

        $stmt = $conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $leads = [];
        while ($row = $result->fetch_assoc()) {
            // Process phone numbers if it's a JSON string
            if (isset($row['phone_numbers']) && is_string($row['phone_numbers'])) {
                $row['phone_numbers'] = json_decode($row['phone_numbers'], true);
            }
            $leads[] = $row;
        }
        $stmt->close();
        $conn->close();
        return $leads;
    } else {
        // Use the combined businesses function
        $result = getCombinedBusinesses($category, $limit, $offset, $search_value, $order_column, $order_dir, $user_id, $has_notes);
        return $result['data'];
    }
}


// This function was removed to avoid duplication with the updateLead function above

/**
 * Changes a lead's category, restricted to the assigned user.
 *
 * @param int $id Lead ID
 * @param string $new_category New category
 * @param int $user_id User ID
 * @return bool Success status
 */
function changeLeadCategory($id, $new_category, $user_id) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return false;
        $sql = "UPDATE leads SET category = ? WHERE id = ? AND assigned_to = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $new_category, $id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    } else {
        $leads = getStaticLeads();
        foreach ($leads as &$lead) {
            if ($lead['id'] == $id && $lead['assigned_to'] == $user_id) {
                $lead['category'] = $new_category;
                return saveStaticLeads($leads);
            }
        }
        return false;
    }
}

/**
 * Deletes a lead, restricted to the assigned user.
 *
 * @param int $id Lead ID
 * @param int $user_id User ID
 * @return bool Success status
 */
function deleteLead($id, $user_id) {
    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return false;
        $sql = "DELETE FROM leads WHERE id = ? AND assigned_to = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $result;
    } else {
        $leads = getStaticLeads();
        $leads = array_filter($leads, function($lead) use ($id, $user_id) {
            return !($lead['id'] == $id && $lead['assigned_to'] == $user_id);
        });
        return saveStaticLeads(array_values($leads));
    }
}

/******************************
 * STATIC DATA FUNCTIONS
 ******************************/

/**
 * Retrieves leads from the combined businesses JSON file.
 * Call Stack is shared among all users, while other categories are user-specific.
 *
 * @param string $category The category to filter by (callStack, coldLeads, warmLeads, currentlyWorkingWith)
 * @param int $limit Number of leads to retrieve
 * @param int $offset Offset for pagination
 * @param string $search_value Search term to filter by
 * @param string $order_column Column to order by
 * @param string $order_dir Direction to order (ASC or DESC)
 * @param int $user_id User ID for filtering user-specific categories (optional)
 * @param bool $has_notes Filter to only show leads with notes (optional)
 * @return array Leads array
 */
function getCombinedBusinesses($category = null, $limit = 10, $offset = 0, $search_value = '', $order_column = 'business_name', $order_dir = 'ASC', $user_id = null, $has_notes = false) {
    if (!file_exists(COMBINED_DATA_FILE)) {
        return ['data' => [], 'total' => 0];
    }

    $json_content = file_get_contents(COMBINED_DATA_FILE);
    $businesses = json_decode($json_content, true);

    if (!is_array($businesses)) {
        return ['data' => [], 'total' => 0];
    }

    // Add an ID to each business if it doesn't have one
    foreach ($businesses as $key => $business) {
        if (!isset($business['id'])) {
            $businesses[$key]['id'] = $key + 1;
        }

        // Assign a category if not set
        if (!isset($business['category'])) {
            // Distribute businesses across categories
            $categories = ['callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith'];
            $businesses[$key]['category'] = $categories[$key % count($categories)];
        }

        // Ensure all required fields exist
        $businesses[$key]['business_name'] = isset($business['business name']) ? $business['business name'] : 'Unknown Business';
        $businesses[$key]['niche'] = isset($business['niche']) ? $business['niche'] : 'Unknown Niche';
        $businesses[$key]['email'] = isset($business['email']) ? $business['email'] : 'no-email@example.com';
        $businesses[$key]['phone_numbers'] = isset($business['phones']) ? $business['phones'] : ['No Phone'];
        $businesses[$key]['website'] = isset($business['website']) ? $business['website'] : 'No Website';
        $businesses[$key]['notes'] = isset($business['notes']) ? $business['notes'] : 'No Notes';
        $businesses[$key]['business_description'] = isset($business['business-description']) ? $business['business-description'] : 'No Description';
    }

    // If user_id is not provided, try to get it from the session
    if ($user_id === null && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }

    // Filter by category if specified
    if ($category) {
        $businesses = array_filter($businesses, function($business) use ($category, $user_id) {
            // Basic category filter
            $category_match = isset($business['category']) && $business['category'] === $category;

            // For categories other than callStack, also filter by user_id
            if ($category !== 'callStack' && $user_id !== null) {
                return $category_match && isset($business['assigned_to']) && $business['assigned_to'] == $user_id;
            }

            return $category_match;
        });
    }

    // Filter for leads with notes if requested
    if ($has_notes) {
        $businesses = array_filter($businesses, function($business) {
            return isset($business['notes']) && !empty($business['notes']);
        });
    }

    // Filter by search term if provided
    if (!empty($search_value)) {
        $businesses = array_filter($businesses, function($business) use ($search_value) {
            $search_value = strtolower($search_value);
            $fields_to_search = [
                'business_name', 'niche', 'email', 'website', 'notes', 'business_description'
            ];

            foreach ($fields_to_search as $field) {
                if (isset($business[$field]) && strpos(strtolower($business[$field]), $search_value) !== false) {
                    return true;
                }
            }

            // Search in phone numbers if they exist
            if (isset($business['phone_numbers']) && is_array($business['phone_numbers'])) {
                foreach ($business['phone_numbers'] as $phone) {
                    if (strpos(strtolower($phone), $search_value) !== false) {
                        return true;
                    }
                }
            }

            return false;
        });
    }

    // Reset array keys
    $businesses = array_values($businesses);

    // Sort the businesses
    if ($order_column && in_array($order_column, ['id', 'business_name', 'niche', 'email', 'website', 'notes', 'category'])) {
        usort($businesses, function($a, $b) use ($order_column, $order_dir) {
            // Handle missing values
            $a_val = isset($a[$order_column]) ? $a[$order_column] : '';
            $b_val = isset($b[$order_column]) ? $b[$order_column] : '';

            // Convert to strings for comparison
            $a_val = is_array($a_val) ? implode(', ', $a_val) : (string)$a_val;
            $b_val = is_array($b_val) ? implode(', ', $b_val) : (string)$b_val;

            // Compare based on direction
            if (strtoupper($order_dir) === 'DESC') {
                return strcasecmp($b_val, $a_val);
            } else {
                return strcasecmp($a_val, $b_val);
            }
        });
    }

    // Get total count before pagination
    $total = count($businesses);

    // Apply pagination if limit is not -1 (which means get all)
    if ($limit > 0) {
        $businesses = array_slice($businesses, $offset, $limit);
    }

    return [
        'data' => $businesses,
        'total' => $total
    ];
}

/**
 * Moves a business from one category to another.
 * When moving from Call Stack to another category, the lead is assigned to the current user.
 * When moving between other categories, the user must own the lead.
 *
 * @param int $id Business ID
 * @param string $from_category Source category
 * @param string $to_category Target category
 * @param int $user_id User ID (optional, defaults to current session user)
 * @return bool Success status
 */
function moveBusinessCategory($id, $from_category, $to_category, $user_id = null) {
    // Log the move attempt
    $log_message = date('Y-m-d H:i:s') . " - Moving business ID: $id from $from_category to $to_category\n";
    file_put_contents('move_business_log.txt', $log_message, FILE_APPEND);

    // If user_id is not provided, try to get it from the session
    if ($user_id === null && isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }

    // If still no user_id, we can't proceed for non-callStack categories
    if ($user_id === null && $from_category !== 'callStack') {
        file_put_contents('move_business_log.txt', "No user ID available for moving from $from_category\n", FILE_APPEND);
        return false;
    }

    if (USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) {
            file_put_contents('move_business_log.txt', "Database connection failed\n", FILE_APPEND);
            return false;
        }

        // First, check if the lead exists with the given ID and category
        if ($from_category === 'callStack') {
            // For Call Stack, we just need to check if the lead exists
            $check_sql = "SELECT id FROM leads WHERE id = ? AND category = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("is", $id, $from_category);
        } else {
            // For other categories, we need to check if the lead exists AND belongs to the user
            $check_sql = "SELECT id FROM leads WHERE id = ? AND category = ? AND assigned_to = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("isi", $id, $from_category, $user_id);
        }

        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $lead_exists = $check_result->num_rows > 0;
        $check_stmt->close();

        file_put_contents('move_business_log.txt', "Lead exists check: " . ($lead_exists ? 'Yes' : 'No') . "\n", FILE_APPEND);

        if (!$lead_exists) {
            file_put_contents('move_business_log.txt', "Lead not found with ID $id and category $from_category" .
                ($from_category !== 'callStack' ? " and user ID $user_id" : "") . "\n", FILE_APPEND);
            return false;
        }

        // Update the lead
        if ($from_category === 'callStack' && $user_id !== null) {
            // When moving from Call Stack, assign the lead to the user
            $sql = "UPDATE leads SET category = ?, assigned_to = ? WHERE id = ? AND category = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("siis", $to_category, $user_id, $id, $from_category);
        } else {
            // When moving between other categories, just update the category
            $sql = "UPDATE leads SET category = ? WHERE id = ? AND category = ?";
            if ($from_category !== 'callStack') {
                $sql .= " AND assigned_to = ?";
            }
            $stmt = $conn->prepare($sql);
            if ($from_category !== 'callStack') {
                $stmt->bind_param("sisi", $to_category, $id, $from_category, $user_id);
            } else {
                $stmt->bind_param("sis", $to_category, $id, $from_category);
            }
        }

        $result = $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        $conn->close();

        file_put_contents('move_business_log.txt', "Update result: " . ($result ? 'Success' : 'Failed') . ", Affected rows: $affected\n", FILE_APPEND);

        return $affected > 0;
    } else {
        if (!file_exists(COMBINED_DATA_FILE)) {
            file_put_contents('move_business_log.txt', "Combined data file not found\n", FILE_APPEND);
            return false;
        }

        $json_content = file_get_contents(COMBINED_DATA_FILE);
        $businesses = json_decode($json_content, true);

        if (!is_array($businesses)) {
            file_put_contents('move_business_log.txt', "Invalid JSON data in combined data file\n", FILE_APPEND);
            return false;
        }

        $found = false;
        foreach ($businesses as $key => $business) {
            // Check if the business matches the criteria
            $id_match = isset($business['id']) && $business['id'] == $id;
            $category_match = isset($business['category']) && $business['category'] === $from_category;
            $user_match = true; // Default to true for Call Stack

            // For non-Call Stack categories, check if the business belongs to the user
            if ($from_category !== 'callStack') {
                $user_match = isset($business['assigned_to']) && $business['assigned_to'] == $user_id;
            }

            if ($id_match && $category_match && $user_match) {
                // Update the category
                $businesses[$key]['category'] = $to_category;

                // If moving from Call Stack, assign the business to the user
                if ($from_category === 'callStack' && $user_id !== null) {
                    $businesses[$key]['assigned_to'] = $user_id;
                }

                $found = true;
                file_put_contents('move_business_log.txt', "Found business at index $key, updating category" .
                    ($from_category === 'callStack' ? " and assigning to user $user_id" : "") . "\n", FILE_APPEND);
                break;
            }
        }

        if ($found) {
            $json_content = json_encode($businesses, JSON_PRETTY_PRINT);
            $result = file_put_contents(COMBINED_DATA_FILE, $json_content) !== false;
            file_put_contents('move_business_log.txt', "Save result: " . ($result ? 'Success' : 'Failed') . "\n", FILE_APPEND);
            return $result;
        }

        file_put_contents('move_business_log.txt', "Business not found with ID $id and category $from_category" .
            ($from_category !== 'callStack' ? " and user ID $user_id" : "") . "\n", FILE_APPEND);
        return false;
    }
}

/**
 * Retrieves leads from the static JSON file.
 *
 * @return array Leads array
 */
function getStaticLeads() {
    // Now just a wrapper for getCombinedBusinesses
    $result = getCombinedBusinesses();
    return $result['data'];
}

/**
 * Saves leads to the static JSON file.
 *
 * @param array $leads Leads to save
 * @return bool Success status
 */
function saveStaticLeads($leads) {
    $dir = dirname(STATIC_DATA_FILE);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $json_content = json_encode($leads, JSON_PRETTY_PRINT);
    return file_put_contents(STATIC_DATA_FILE, $json_content) !== false;
}

/******************************
 * TEMPLATE FUNCTIONS
 ******************************/

/**
 * Generates the HTML footer content.
 *
 * @return string Footer HTML
 */

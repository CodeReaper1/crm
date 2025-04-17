<?php
session_start();
include 'functions.php';
include 'debug_functions.php';
include 'profile_functions.php';
include 'user_activity.php';

// Debug request information
debug_log("=== PROFILE PAGE REQUEST ====");
debug_log("Request method: " . $_SERVER['REQUEST_METHOD']);
debug_log("Request URI: " . $_SERVER['REQUEST_URI']);
debug_log("POST data: " . json_encode($_POST));
debug_log("GET data: " . json_encode($_GET));
debug_log("===========================");
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Get user data directly from database for the most up-to-date information
$conn = connectDB();
if (!$conn) {
    debug_log("Database connection failed", 'ERROR');
    $user = getUserProfile($user_id); // Fallback to the function
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    debug_log("Loaded fresh user data: " . json_encode($user));
}

// Get user stats
$lead_stats = getUserLeadStats($user_id);

// Process messages from session
$success_message = '';
$error_message = '';

// Check for success message in session
if (isset($_SESSION['profile_success'])) {
    $success_message = $_SESSION['profile_success'];
    unset($_SESSION['profile_success']);
}

// Check for error message in session
if (isset($_SESSION['profile_error'])) {
    $error_message = $_SESSION['profile_error'];
    unset($_SESSION['profile_error']);
}

// This is the old form processing code, keeping it for reference
/*
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    debug_log("Profile update form submitted for user ID: $user_id");
    debug_log("POST data: " . json_encode($_POST));

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $job_title = isset($_POST['job_title']) ? trim($_POST['job_title']) : '';
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';

    // Log the update attempt for debugging
    debug_log("Profile update attempt - User ID: $user_id, Name: $name, Email: $email, Job Title: $job_title, Location: $location");

    if (empty($name)) {
        $error_message = 'Name is required';
        debug_log("Profile update validation failed: Name is required", 'WARNING');
    } else {
        // Direct SQL update approach
        $conn = connectDB();

        if (!$conn) {
            debug_log("Database connection failed", 'ERROR');
            $error_message = 'Database connection failed. Please try again.';
        } else {
            debug_log("Database connection successful");

            // Prepare SQL statement
            $sql = "UPDATE users SET name = ?, email = ?, job_title = ?, location = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                debug_log("Prepare statement failed: " . $conn->error, 'ERROR');
                $error_message = 'Database error. Please try again.';
            } else {
                debug_log("Prepare statement successful");

                // Bind parameters and execute
                $stmt->bind_param("ssssi", $name, $email, $job_title, $location, $user_id);
                $result = $stmt->execute();

                debug_log("SQL execution result: " . ($result ? 'true' : 'false'));
                debug_log("Affected rows: " . $stmt->affected_rows);

                if ($result) {
                    $success_message = 'Profile updated successfully';
                    debug_log("Profile updated successfully for user ID: $user_id");

                    // Refresh user data directly from database
                    $stmt->close();
                    $sql = "SELECT * FROM users WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_assoc();

                    debug_log("Refreshed user data: " . json_encode($user));
                } else {
                    $error_message = 'Failed to update profile: ' . $stmt->error;
                    debug_log("Profile update failed for user ID: $user_id - Error: " . $stmt->error, 'ERROR');
                }

                $stmt->close();
            }

            $conn->close();
        }
    }
}
*/

// Process password change form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Log the password change attempt (without passwords)
    error_log("Password change attempt - User ID: $user_id");

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'All password fields are required';
    } else if ($new_password !== $confirm_password) {
        $error_message = 'New passwords do not match';
    } else if (strlen($new_password) < 6) {
        $error_message = 'New password must be at least 6 characters long';
    } else {
        $result = changeUserPassword($user_id, $current_password, $new_password);

        if ($result === true) {
            $success_message = 'Password changed successfully';

            // Log the password change activity
            logUserActivity($user_id, 'password_change', [
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            debug_log("Password change activity logged for user ID: $user_id");
        } else {
            $error_message = $result; // Error message from the function
            error_log("Password change failed for user ID: $user_id - Reason: $result");
        }
    }
}

include 'components/header.php';
echo getHeader('Profile', 'profile');
if ($user === null) {
    echo '<div class="alert alert-danger">User not found. Please log in again.</div>';
    exit;
}

// Add custom CSS for the profile page
?>
<style>
  /* Profile page custom styles */
  .profile-cover {
    position: relative;
    height: 350px !important;
    border-radius: 10px 10px 0 0;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }

  .profile-cover::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.6));
    border-radius: 10px 10px 0 0;
  }

  .profile-cover-content {
    position: absolute;
    bottom: 20px;
    left: 30px;
    color: white;
    z-index: 10;
    text-shadow: 0 1px 3px rgba(0,0,0,0.3);
  }

  .profile-stats-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 10px;
  }

  .profile-stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }

  .stat-icon {
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-size: 22px;
    margin-right: 15px;
  }

  .bg-light-primary {
    background-color: rgba(71, 118, 230, 0.1);
    color: #4776E6;
  }

  .bg-light-success {
    background-color: rgba(33, 150, 83, 0.1);
    color: #219653;
  }

  .bg-light-warning {
    background-color: rgba(242, 153, 74, 0.1);
    color: #F2994A;
  }

  .bg-light-danger {
    background-color: rgba(235, 87, 87, 0.1);
    color: #EB5757;
  }

  .tab-content {
    padding: 20px;
    background-color: #fff;
    border-radius: 0 0 10px 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }

  .nav-tabs .nav-link {
    border: none;
    padding: 15px 20px;
    font-weight: 500;
  }

  .nav-tabs .nav-link.active {
    border-bottom: 3px solid #4776E6;
    background-color: transparent;
    color: #4776E6;
  }

  .form-control:focus {
    border-color: #4776E6;
    box-shadow: 0 0 0 0.25rem rgba(71, 118, 230, 0.25);
  }

  .btn-primary {
    background-color: #4776E6;
    border-color: #4776E6;
  }

  .btn-primary:hover {
    background-color: #3a67d7;
    border-color: #3a67d7;
  }

  .profile-info-item {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
  }

  .profile-info-item:last-child {
    border-bottom: none;
  }

  .profile-info-label {
    font-weight: 500;
    color: #555;
  }

  .profile-info-value {
    color: #333;
  }

  .activity-timeline {
    position: relative;
    padding-left: 30px;
  }

  .activity-timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    height: 100%;
    width: 2px;
    background-color: #e9ecef;
  }

  .activity-item {
    position: relative;
    padding-bottom: 20px;
  }

  .activity-item::before {
    content: '';
    position: absolute;
    left: -30px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #4776E6;
    border: 2px solid white;
  }

  .activity-date {
    font-size: 12px;
    color: #6c757d;
  }

  .activity-content {
    font-size: 14px;
    margin-top: 5px;
  }
</style>
<?php

// Set default values for user data
$user['job_title'] = isset($user['job_title']) ? $user['job_title'] : 'CRM User';
$user['location'] = isset($user['location']) ? $user['location'] : 'Not specified';
$user['profile_picture'] = isset($user['profile_picture']) && !empty($user['profile_picture']) ? $user['profile_picture'] : 'assets/images/avatars/06.png';
?>

<!-- start page content wrapper-->
<div class="page-content-wrapper">
  <!-- start page content-->
  <div class="page-content">
    <?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <i class="bx bx-check-circle me-1"></i> <?php echo htmlspecialchars($success_message); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <i class="bx bx-error-circle me-1"></i> <?php echo htmlspecialchars($error_message); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>


    <div class="row">
      <!-- Profile Overview Card -->
      <div class="col-12 col-lg-4">
        <div class="card overflow-hidden radius-10">
          <div class="profile-cover position-relative" style="background-image: url('<?php echo htmlspecialchars($user['profile_picture']); ?>');" id="profile-image-container">
            <div class="profile-cover-content">
              <h3 class="mb-0"><?php echo htmlspecialchars($user['name']); ?></h3>
              <p class="mb-1"><?php echo htmlspecialchars($user['job_title']); ?></p>
              <button class="btn btn-light btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#changeImageModal">
                <i class="bx bx-camera"></i> Change Background
              </button>
            </div>
          </div>
          <div class="card-body pt-3">
            <div class="text-center mb-3">
              <span class="badge bg-light text-dark mb-2">
                <i class="bx bx-map me-1"></i> <?php echo htmlspecialchars($user['location']); ?>
              </span>
            </div>
            <hr>
            <div class="text-start">
              <h5 class="d-flex align-items-center mb-3">
                <i class="bx bx-envelope fs-4 me-2"></i> Contact Information
              </h5>
              <div class="profile-info-item d-flex">
                <div class="profile-info-label me-2">Email:</div>
                <div class="profile-info-value flex-grow-1"><?php echo htmlspecialchars($user['email']); ?></div>
              </div>
            </div>
            <hr>
            <div class="text-start">
              <h5 class="d-flex align-items-center mb-3">
                <i class="bx bx-stats fs-4 me-2"></i> Lead Statistics
              </h5>

              <!-- Call Stack Stats -->
              <div class="card profile-stats-card mb-3 bg-light shadow-sm">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-primary">
                      <i class="bx bx-phone"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">Call Stack</h6>
                      <h4 class="mb-0"><?php echo $lead_stats['callStack']; ?></h4>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cold Leads Stats -->
              <div class="card profile-stats-card mb-3 bg-light shadow-sm">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-info">
                      <i class="bx bx-snowflake"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">Cold Leads</h6>
                      <h4 class="mb-0"><?php echo $lead_stats['coldLeads']; ?></h4>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Warm Leads Stats -->
              <div class="card profile-stats-card mb-3 bg-light shadow-sm">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-warning">
                      <i class="bx bx-sun"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">Warm Leads</h6>
                      <h4 class="mb-0"><?php echo $lead_stats['warmLeads']; ?></h4>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Currently Working With Stats -->
              <div class="card profile-stats-card mb-3 bg-light shadow-sm">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="stat-icon bg-light-success">
                      <i class="bx bx-briefcase"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">Currently Working With</h6>
                      <h4 class="mb-0"><?php echo $lead_stats['currentlyWorkingWith']; ?></h4>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Total Leads Stats -->
              <div class="card profile-stats-card bg-primary text-white shadow">
                <div class="card-body p-3">
                  <div class="d-flex align-items-center">
                    <div class="stat-icon bg-white text-primary">
                      <i class="bx bx-bar-chart-alt-2"></i>
                    </div>
                    <div>
                      <h6 class="mb-0">Total Leads</h6>
                      <h4 class="mb-0"><?php echo $lead_stats['total']; ?></h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Profile Edit Card -->
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-body p-4">
            <ul class="nav nav-tabs nav-primary" role="tablist">
              <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#editProfile" role="tab" aria-selected="true">
                  <div class="d-flex align-items-center">
                    <div><i class="bx bx-user-circle font-18 me-1"></i></div>
                    <div>Edit Profile</div>
                  </div>
                </a>
              </li>
              <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab" aria-selected="false">
                  <div class="d-flex align-items-center">
                    <div><i class="bx bx-lock-alt font-18 me-1"></i></div>
                    <div>Change Password</div>
                  </div>
                </a>
              </li>
              <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab" aria-selected="false">
                  <div class="d-flex align-items-center">
                    <div><i class="bx bx-history font-18 me-1"></i></div>
                    <div>Recent Activity</div>
                  </div>
                </a>
              </li>
            </ul>
            <div class="tab-content py-3">
              <!-- Edit Profile Tab -->
              <div class="tab-pane fade show active" id="editProfile" role="tabpanel">
                <h5 class="mb-4"><i class="bx bx-edit me-1"></i> Update Your Information</h5>
                <form method="post" action="update_profile_handler.php" id="profileForm">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="name" class="form-label">Full Name</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="email" class="form-label">Email Address</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="job_title" class="form-label">Job Title</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-briefcase"></i></span>
                        <input type="text" class="form-control" id="job_title" name="job_title" value="<?php echo htmlspecialchars($user['job_title']); ?>">
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="location" class="form-label">Location</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-map"></i></span>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($user['location']); ?>">
                      </div>
                    </div>
                  </div>
                  <div class="d-flex justify-content-end mt-3">
                    <input type="hidden" name="debug_timestamp" value="<?php echo time(); ?>">
                    <input type="hidden" name="update_profile" value="1">
                    <button type="reset" class="btn btn-light me-2">Reset</button>
                    <button type="submit" class="btn btn-primary" id="saveProfileBtn">
                      <i class="bx bx-save me-1"></i> Save Changes
                    </button>
                  </div>
                </form>
              </div>

              <!-- Change Password Tab -->
              <div class="tab-pane fade" id="changePassword" role="tabpanel">
                <h5 class="mb-4"><i class="bx bx-shield me-1"></i> Update Your Password</h5>
                <form method="post" action="" id="passwordForm">
                  <div class="mb-3">
                    <label for="current_password" class="form-label">Current Password</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-lock"></i></span>
                      <input type="password" class="form-control" id="current_password" name="current_password" required>
                      <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                        <i class="bx bx-hide"></i>
                      </button>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="new_password" class="form-label">New Password</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-lock-open"></i></span>
                      <input type="password" class="form-control" id="new_password" name="new_password" required>
                      <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                        <i class="bx bx-hide"></i>
                      </button>
                    </div>
                    <div class="password-strength mt-2" id="password-strength"></div>
                  </div>
                  <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="bx bx-check-shield"></i></span>
                      <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                      <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
                        <i class="bx bx-hide"></i>
                      </button>
                    </div>
                    <div id="password-match" class="form-text"></div>
                  </div>
                  <div class="d-flex justify-content-end mt-4">
                    <button type="reset" class="btn btn-light me-2">Reset</button>
                    <button type="submit" name="change_password" class="btn btn-primary" id="changePasswordBtn">
                      <i class="bx bx-lock-alt me-1"></i> Update Password
                    </button>
                  </div>
                </form>
              </div>

              <!-- Activity Tab -->
              <div class="tab-pane fade" id="activity" role="tabpanel">
                <h5 class="mb-4"><i class="bx bx-time me-1"></i> Your Recent Activities</h5>
                <div class="activity-timeline">
                  <?php
                  // Wrap activity retrieval in try-catch to handle any errors
                  try {
                    // Get user activities
                    $activities = getUserActivities($user_id, 20);

                    if (empty($activities)) {
                      // If no activities found, create some sample activities for demonstration
                      echo "<div class='text-center p-4'>";
                      echo "<i class='bx bx-info-circle fs-3 mb-2'></i>";
                      echo "<p>No activities found. Your recent actions will appear here.</p>";

                      // Try to create a sample activity for profile update
                      try {
                        $activity_created = logUserActivity($user_id, 'profile_update', [
                          'timestamp' => date('Y-m-d H:i:s'),
                          'note' => 'Initial profile view'
                        ]);

                        if ($activity_created) {
                          echo "<div class='alert alert-info'>";
                          echo "<i class='bx bx-bulb me-1'></i> A sample activity has been created for demonstration.";
                          echo "</div>";
                          echo "<button class='btn btn-sm btn-primary' onclick='location.reload()'><i class='bx bx-refresh me-1'></i> Refresh</button>";
                        } else {
                          echo "<div class='alert alert-warning'>";
                          echo "<i class='bx bx-error-circle me-1'></i> Could not create sample activity. Please check database permissions.";
                          echo "</div>";
                        }
                      } catch (Exception $e) {
                        debug_log("Error creating sample activity: " . $e->getMessage(), 'ERROR');
                        echo "<div class='alert alert-warning'>";
                        echo "<i class='bx bx-error-circle me-1'></i> Could not create sample activity due to an error.";
                        echo "</div>";
                      }

                      echo "</div>";
                    } else {
                      // Display user activities
                      foreach ($activities as $activity) {
                        $formatted = formatActivity($activity);
                        echo "<div class='activity-item'>";
                        echo "<div class='activity-date'>{$formatted['relative_date']}</div>";
                        echo "<div class='activity-content'>{$formatted['html']}</div>";
                        echo "</div>";
                      }
                    }
                  } catch (Exception $e) {
                    debug_log("Error in activity tab: " . $e->getMessage(), 'ERROR');
                    echo "<div class='alert alert-danger'>";
                    echo "<i class='bx bx-error-circle me-1'></i> An error occurred while loading activities.";
                    echo "</div>";
                    echo "<div class='text-center mt-3'>";
                    echo "<button class='btn btn-sm btn-primary' onclick='location.reload()'><i class='bx bx-refresh me-1'></i> Try Again</button>";
                    echo "</div>";
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal for Changing Profile Background -->
<div class="modal fade" id="changeImageModal" tabindex="-1" aria-labelledby="changeImageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title" id="changeImageModalLabel"><i class="bx bx-image-alt me-1"></i> Change Profile Background</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="uploadAlert" class="alert d-none"></div>
        <form id="uploadForm" enctype="multipart/form-data">
          <div class="text-center mb-4">
            <div class="current-image mb-3">
              <div style="width: 100%; height: 150px; margin: 0 auto; background-color: #f8f9fa; overflow: hidden; border: 1px solid #eee; border-radius: 8px;">
                <div style="width: 100%; height: 100%; background-image: url('<?php echo htmlspecialchars($user['profile_picture']); ?>'); background-size: cover; background-position: center;"></div>
              </div>
              <p class="text-muted small mt-2">Current background image</p>
            </div>
          </div>

          <div class="mb-3">
            <label for="avatar" class="form-label"><i class="bx bx-upload me-1"></i> Select a new background image</label>
            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" required>
            <div class="form-text">Recommended size: 1200x400 pixels. Maximum file size: 2MB.</div>
          </div>

          <div class="mb-4">
            <div id="imagePreview" class="text-center mt-3" style="display: none;">
              <h6 class="mb-2">Preview</h6>
              <div style="width: 100%; height: 150px; margin: 0 auto; background-color: #f8f9fa; overflow: hidden; border: 1px solid #eee; border-radius: 8px;">
                <div id="preview-image-container" style="width: 100%; height: 100%; background-size: cover; background-position: center;"></div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="uploadButton">
              <i class="bx bx-cloud-upload me-1"></i> Upload Image
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- JS Files-->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function() {
    // Fix for tab switching issues
    $('.nav-tabs .nav-link').on('click', function(e) {
      e.preventDefault();
      $(this).tab('show');
    });

    // Preview image before upload
    $('#avatar').on('change', function() {
      const file = this.files[0];
      if (file) {
        // Check file size
        if (file.size > 2 * 1024 * 1024) {
          $('#uploadAlert').removeClass('d-none alert-success').addClass('alert-danger')
            .html('<i class="bx bx-error-circle me-1"></i> File size exceeds 2MB limit.');
          return;
        }

        // Check file type
        const fileType = file.type.split('/')[0];
        if (fileType !== 'image') {
          $('#uploadAlert').removeClass('d-none alert-success').addClass('alert-danger')
            .html('<i class="bx bx-error-circle me-1"></i> Please select an image file.');
          return;
        }

        // Clear any previous alerts
        $('#uploadAlert').addClass('d-none');

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
          $('#imagePreview').show();
          $('#preview-image-container').css('background-image', 'url(' + e.target.result + ')');
        }
        reader.readAsDataURL(file);
      }
    });

    // Handle avatar upload
    $('#uploadForm').on('submit', function(event) {
      event.preventDefault();
      var formData = new FormData(this);

      // Show loading state
      const uploadBtn = $('#uploadButton');
      const originalBtnText = uploadBtn.html();
      uploadBtn.html('<i class="bx bx-loader bx-spin me-1"></i> Uploading...').prop('disabled', true);

      // Clear previous alerts
      $('#uploadAlert').addClass('d-none');

      $.ajax({
        url: 'upload_avatar.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {
            // Show success message in modal
            $('#uploadAlert').removeClass('d-none alert-danger').addClass('alert-success')
              .html('<i class="bx bx-check-circle me-1"></i> Profile background updated successfully!');

            // Update the profile background image
            const imageUrl = response.image + '?v=' + new Date().getTime(); // Add cache-busting parameter
            $('#profile-image-container').css('background-image', 'url(' + imageUrl + ')');

            // Close modal after a delay
            setTimeout(function() {
              $('#changeImageModal').modal('hide');

              // Show success message on page
              $('<div class="alert alert-success alert-dismissible fade show"><i class="bx bx-check-circle me-1"></i> Profile background updated successfully<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>')
                .insertBefore('.page-content .row').delay(3000).fadeOut();

              // Reset form
              $('#uploadForm')[0].reset();
              $('#imagePreview').hide();
            }, 1500);
          } else {
            // Show error message in modal
            $('#uploadAlert').removeClass('d-none alert-success').addClass('alert-danger')
              .html('<i class="bx bx-error-circle me-1"></i> ' + response.message);

            // Reset button
            uploadBtn.html(originalBtnText).prop('disabled', false);
          }
        },
        error: function() {
          // Show error message in modal
          $('#uploadAlert').removeClass('d-none alert-success').addClass('alert-danger')
            .html('<i class="bx bx-error-circle me-1"></i> An error occurred during upload. Please try again.');

          // Reset button
          uploadBtn.html(originalBtnText).prop('disabled', false);
        }
      });
    });

    // Password visibility toggle
    $('.toggle-password').on('click', function() {
      const targetId = $(this).data('target');
      const passwordInput = $('#' + targetId);
      const icon = $(this).find('i');

      if (passwordInput.attr('type') === 'password') {
        passwordInput.attr('type', 'text');
        icon.removeClass('bx-hide').addClass('bx-show');
      } else {
        passwordInput.attr('type', 'password');
        icon.removeClass('bx-show').addClass('bx-hide');
      }
    });

    // Password strength meter
    $('#new_password').on('input', function() {
      const password = $(this).val();
      let strength = 0;
      let feedback = '';

      if (password.length >= 8) strength += 1;
      if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
      if (password.match(/\d/)) strength += 1;
      if (password.match(/[^a-zA-Z\d]/)) strength += 1;

      const strengthMeter = $('#password-strength');

      switch(strength) {
        case 0:
          strengthMeter.html('').removeClass('text-danger text-warning text-success');
          break;
        case 1:
          strengthMeter.html('<div class="progress" style="height: 5px;"><div class="progress-bar bg-danger" style="width: 25%"></div></div><span class="text-danger small">Weak</span>').addClass('text-danger').removeClass('text-warning text-success');
          break;
        case 2:
          strengthMeter.html('<div class="progress" style="height: 5px;"><div class="progress-bar bg-warning" style="width: 50%"></div></div><span class="text-warning small">Fair</span>').addClass('text-warning').removeClass('text-danger text-success');
          break;
        case 3:
          strengthMeter.html('<div class="progress" style="height: 5px;"><div class="progress-bar bg-info" style="width: 75%"></div></div><span class="text-info small">Good</span>').addClass('text-info').removeClass('text-danger text-warning text-success');
          break;
        case 4:
          strengthMeter.html('<div class="progress" style="height: 5px;"><div class="progress-bar bg-success" style="width: 100%"></div></div><span class="text-success small">Strong</span>').addClass('text-success').removeClass('text-danger text-warning');
          break;
      }
    });

    // Password match check
    $('#confirm_password').on('input', function() {
      const newPassword = $('#new_password').val();
      const confirmPassword = $(this).val();

      if (confirmPassword === '') {
        $('#password-match').html('').removeClass('text-success text-danger');
      } else if (newPassword === confirmPassword) {
        $('#password-match').html('<i class="bx bx-check-circle"></i> Passwords match').addClass('text-success').removeClass('text-danger');
      } else {
        $('#password-match').html('<i class="bx bx-x-circle"></i> Passwords do not match').addClass('text-danger').removeClass('text-success');
      }
    });

    // Form validation and submission for profile form
    $('#profileForm').on('submit', function(e) {
      // Basic validation
      let isValid = true;
      const nameField = $('#name');
      const emailField = $('#email');

      // Clear previous validation messages
      $('.invalid-feedback').remove();
      $('.is-invalid').removeClass('is-invalid');

      // Validate name
      if (nameField.val().trim() === '') {
        nameField.addClass('is-invalid');
        nameField.after('<div class="invalid-feedback">Name is required</div>');
        isValid = false;
      }

      // Validate email
      if (emailField.val().trim() === '') {
        emailField.addClass('is-invalid');
        emailField.after('<div class="invalid-feedback">Email is required</div>');
        isValid = false;
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.val())) {
        emailField.addClass('is-invalid');
        emailField.after('<div class="invalid-feedback">Please enter a valid email address</div>');
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
        return false;
      }

      // Log form submission
      console.log('Form is valid, submitting...');
      console.log('Form data:', {
        name: nameField.val(),
        email: emailField.val(),
        job_title: $('#job_title').val(),
        location: $('#location').val()
      });

      // Show loading state
      $('#saveProfileBtn').html('<i class="bx bx-loader bx-spin me-1"></i> Saving...').prop('disabled', true);

      // Allow form submission
      return true;
    });

    // Form validation and submission for password form
    $('#passwordForm').on('submit', function(e) {
      // Basic validation
      let isValid = true;
      const currentPasswordField = $('#current_password');
      const newPasswordField = $('#new_password');
      const confirmPasswordField = $('#confirm_password');

      // Clear previous validation messages
      $('.invalid-feedback').remove();
      $('.is-invalid').removeClass('is-invalid');

      // Validate current password
      if (currentPasswordField.val() === '') {
        currentPasswordField.addClass('is-invalid');
        currentPasswordField.after('<div class="invalid-feedback">Current password is required</div>');
        isValid = false;
      }

      // Validate new password
      if (newPasswordField.val() === '') {
        newPasswordField.addClass('is-invalid');
        newPasswordField.after('<div class="invalid-feedback">New password is required</div>');
        isValid = false;
      } else if (newPasswordField.val().length < 6) {
        newPasswordField.addClass('is-invalid');
        newPasswordField.after('<div class="invalid-feedback">Password must be at least 6 characters long</div>');
        isValid = false;
      }

      // Validate confirm password
      if (confirmPasswordField.val() === '') {
        confirmPasswordField.addClass('is-invalid');
        confirmPasswordField.after('<div class="invalid-feedback">Please confirm your new password</div>');
        isValid = false;
      } else if (confirmPasswordField.val() !== newPasswordField.val()) {
        confirmPasswordField.addClass('is-invalid');
        confirmPasswordField.after('<div class="invalid-feedback">Passwords do not match</div>');
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();
        return false;
      }

      // Show loading state
      $('#changePasswordBtn').html('<i class="bx bx-loader bx-spin me-1"></i> Updating...').prop('disabled', true);
    });
  });
</script>

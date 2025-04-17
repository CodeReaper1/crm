<?php
// Start session and check login before ANY output
session_start();

// Check if user is logged in and get user ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Redirect if not logged in
if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Check if lead ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$lead_id = intval($_GET['id']);

require_once 'functions.php';

// Get lead details
$lead = getLeadById($lead_id);

// If lead not found, redirect to dashboard
if (empty($lead)) {
    header('Location: dashboard.php');
    exit;
}

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $business_name = isset($_POST['business_name']) ? trim($_POST['business_name']) : '';
    $niche = isset($_POST['niche']) ? trim($_POST['niche']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $website = isset($_POST['website']) ? trim($_POST['website']) : '';
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : $lead['category'];
    
    // Handle phone numbers (can be multiple)
    $phone_numbers = [];
    if (isset($_POST['phone_numbers']) && is_array($_POST['phone_numbers'])) {
        foreach ($_POST['phone_numbers'] as $phone) {
            $phone = trim($phone);
            if (!empty($phone)) {
                $phone_numbers[] = $phone;
            }
        }
    }
    
    // Validate required fields
    if (empty($business_name)) {
        $error_message = 'Business name is required';
    } else {
        // Update the lead
        $result = updateLead($lead_id, [
            'business_name' => $business_name,
            'niche' => $niche,
            'email' => $email,
            'phone_numbers' => $phone_numbers,
            'website' => $website,
            'notes' => $notes,
            'category' => $category
        ]);
        
        if ($result) {
            $success_message = 'Lead updated successfully';
            // Refresh lead data
            $lead = getLeadById($lead_id);
        } else {
            $error_message = 'Failed to update lead';
        }
    }
}

// Get the category name for display
$category_display_name = '';
switch ($lead['category']) {
    case 'callStack':
        $category_display_name = 'Call Stack';
        break;
    case 'coldLeads':
        $category_display_name = 'Cold Leads';
        break;
    case 'warmLeads':
        $category_display_name = 'Warm Leads';
        break;
    case 'currentlyWorkingWith':
        $category_display_name = 'Currently Working With';
        break;
    default:
        $category_display_name = $lead['category'];
}

include 'components/header.php'; 
echo getHeader('Edit Lead: ' . htmlspecialchars($lead['business_name']), 'leads');
?>

<!-- start page content wrapper-->
<div class="page-content-wrapper">
  <!-- start page content-->
  <div class="page-content">

    <!--start breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Leads</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0 align-items-center">
            <li class="breadcrumb-item"><a href="dashboard.php"><ion-icon name="home-outline"></ion-icon></a></li>
            <li class="breadcrumb-item"><a href="<?php echo strtolower(str_replace(' ', '-', $category_display_name)) . '.php'; ?>"><?php echo $category_display_name; ?></a></li>
            <li class="breadcrumb-item"><a href="lead-profile.php?id=<?php echo $lead_id; ?>"><?php echo htmlspecialchars($lead['business_name']); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <h5 class="mb-0">Edit Lead</h5>
          <div class="ms-auto">
            <a href="lead-profile.php?id=<?php echo $lead_id; ?>" class="btn btn-sm btn-outline-secondary">
              <i class="bx bx-arrow-back"></i> Back to Profile
            </a>
          </div>
        </div>
        <hr>
        
        <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="post" action="">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label for="business_name" class="form-label">Business Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="business_name" name="business_name" value="<?php echo htmlspecialchars($lead['business_name']); ?>" required>
            </div>
            
            <div class="col-12 col-md-6">
              <label for="niche" class="form-label">Niche</label>
              <input type="text" class="form-control" id="niche" name="niche" value="<?php echo htmlspecialchars($lead['niche']); ?>">
            </div>
            
            <div class="col-12 col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($lead['email'] != 'no-email@example.com' ? $lead['email'] : ''); ?>">
            </div>
            
            <div class="col-12 col-md-6">
              <label for="website" class="form-label">Website</label>
              <input type="url" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($lead['website'] != 'No Website' ? $lead['website'] : ''); ?>">
            </div>
            
            <div class="col-12" id="phone-numbers-container">
              <label class="form-label">Phone Numbers</label>
              <?php 
              $phone_numbers = is_array($lead['phone_numbers']) ? $lead['phone_numbers'] : [$lead['phone_numbers']];
              $phone_numbers = array_filter($phone_numbers, function($phone) {
                  return $phone != 'No Phone';
              });
              
              if (empty($phone_numbers)) {
                  $phone_numbers = [''];
              }
              
              foreach ($phone_numbers as $index => $phone): 
              ?>
              <div class="input-group mb-2">
                <input type="tel" class="form-control" name="phone_numbers[]" value="<?php echo htmlspecialchars($phone); ?>">
                <?php if ($index === 0): ?>
                <button type="button" class="btn btn-outline-secondary" id="add-phone">
                  <i class="bx bx-plus"></i>
                </button>
                <?php else: ?>
                <button type="button" class="btn btn-outline-danger remove-phone">
                  <i class="bx bx-minus"></i>
                </button>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
            
            <div class="col-12">
              <label for="category" class="form-label">Category</label>
              <select class="form-select" id="category" name="category">
                <option value="callStack" <?php echo $lead['category'] === 'callStack' ? 'selected' : ''; ?>>Call Stack</option>
                <option value="coldLeads" <?php echo $lead['category'] === 'coldLeads' ? 'selected' : ''; ?>>Cold Leads</option>
                <option value="warmLeads" <?php echo $lead['category'] === 'warmLeads' ? 'selected' : ''; ?>>Warm Leads</option>
                <option value="currentlyWorkingWith" <?php echo $lead['category'] === 'currentlyWorkingWith' ? 'selected' : ''; ?>>Currently Working With</option>
              </select>
            </div>
            
            <div class="col-12">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control" id="notes" name="notes" rows="5"><?php echo htmlspecialchars($lead['notes'] != 'No Notes' ? $lead['notes'] : ''); ?></textarea>
            </div>
            
            <div class="col-12">
              <button type="submit" class="btn btn-primary px-4">Save Changes</button>
              <a href="lead-profile.php?id=<?php echo $lead_id; ?>" class="btn btn-secondary px-4">Cancel</a>
            </div>
          </div>
        </form>
      </div>
    </div>

  </div>
  <!-- end page content-->
</div>

<!--Start Back To Top Button-->
<a href="javaScript:;" class="back-to-top"><ion-icon name="arrow-up-outline"></ion-icon></a>
<!--End Back To Top Button-->

<!-- JS Files-->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<!--plugins-->
<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>

<!-- Edit Lead JS -->
<script>
$(document).ready(function() {
    // Add phone number field
    $('#add-phone').on('click', function() {
        const phoneField = `
            <div class="input-group mb-2">
                <input type="tel" class="form-control" name="phone_numbers[]" value="">
                <button type="button" class="btn btn-outline-danger remove-phone">
                    <i class="bx bx-minus"></i>
                </button>
            </div>
        `;
        $('#phone-numbers-container').append(phoneField);
    });
    
    // Remove phone number field
    $('#phone-numbers-container').on('click', '.remove-phone', function() {
        $(this).closest('.input-group').remove();
    });
});
</script>

<!-- Main JS-->
<script src="assets/js/main.js"></script>

</body>
</html>

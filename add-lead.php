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

require_once 'functions.php';

// Get the category from the query string (default to callStack if not provided)
$category = isset($_GET['category']) ? $_GET['category'] : 'callStack';

// Validate the category
$valid_categories = ['callStack', 'coldLeads', 'warmLeads', 'currentlyWorkingWith'];
if (!in_array($category, $valid_categories)) {
    $category = 'callStack';
}

// Get the category name for display
$category_display_name = '';
switch ($category) {
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
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'callStack';
    
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
        // Create the lead data
        $lead_data = [
            'business_name' => $business_name,
            'niche' => $niche,
            'email' => !empty($email) ? $email : 'no email',
            'phone_numbers' => !empty($phone_numbers) ? $phone_numbers : ['No Phone'],
            'website' => !empty($website) ? $website : 'no website',
            'notes' => !empty($notes) ? $notes : 'No Notes',
            'category' => $category,
            'status' => 'new',
            'base_url' => '',
            'image' => '',
            'business_description' => '',
            'phones' => !empty($phone_numbers) ? $phone_numbers : ['No Phone']
        ];
        
        // Add the lead
        $result = addLead($lead_data);
        
        if ($result) {
            $success_message = 'Lead added successfully';
            
            // Redirect to the appropriate page after a short delay
            header("Refresh: 2; URL=" . getCategoryPageUrl($category));
        } else {
            $error_message = 'Failed to add lead';
        }
    }
}

// Helper function to get the category page URL
function getCategoryPageUrl($category) {
    switch($category) {
        case 'callStack': return 'page-call-stack.php';
        case 'coldLeads': return 'page-cold-leads.php';
        case 'warmLeads': return 'page-warm-leads.php';
        case 'currentlyWorkingWith': return 'currently-working-with.php';
        default: return 'dashboard.php';
    }
}

include 'components/header.php'; 
echo getHeader('Add New Lead', 'leads');
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
            <li class="breadcrumb-item"><a href="<?php echo getCategoryPageUrl($category); ?>"><?php echo $category_display_name; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Add New Lead</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <h5 class="mb-0">Add New Lead to <?php echo $category_display_name; ?></h5>
          <div class="ms-auto">
            <a href="<?php echo getCategoryPageUrl($category); ?>" class="btn btn-sm btn-outline-secondary">
              <i class="bx bx-arrow-back"></i> Back to <?php echo $category_display_name; ?>
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
              <input type="text" class="form-control" id="business_name" name="business_name" required>
            </div>
            
            <div class="col-12 col-md-6">
              <label for="niche" class="form-label">Niche</label>
              <input type="text" class="form-control" id="niche" name="niche">
            </div>
            
            <div class="col-12 col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email">
            </div>
            
            <div class="col-12 col-md-6">
              <label for="website" class="form-label">Website</label>
              <input type="url" class="form-control" id="website" name="website">
            </div>
            
            <div class="col-12" id="phone-numbers-container">
              <label class="form-label">Phone Numbers</label>
              <div class="input-group mb-2">
                <input type="tel" class="form-control" name="phone_numbers[]" value="">
                <button type="button" class="btn btn-outline-secondary" id="add-phone">
                  <i class="bx bx-plus"></i>
                </button>
              </div>
            </div>
            
            <div class="col-12">
              <label for="category" class="form-label">Category</label>
              <select class="form-select" id="category" name="category">
                <option value="callStack" <?php echo $category === 'callStack' ? 'selected' : ''; ?>>Call Stack</option>
                <option value="coldLeads" <?php echo $category === 'coldLeads' ? 'selected' : ''; ?>>Cold Leads</option>
                <option value="warmLeads" <?php echo $category === 'warmLeads' ? 'selected' : ''; ?>>Warm Leads</option>
                <option value="currentlyWorkingWith" <?php echo $category === 'currentlyWorkingWith' ? 'selected' : ''; ?>>Currently Working With</option>
              </select>
            </div>
            
            <div class="col-12">
              <label for="notes" class="form-label">Notes</label>
              <textarea class="form-control" id="notes" name="notes" rows="5"></textarea>
            </div>
            
            <div class="col-12">
              <button type="submit" class="btn btn-primary px-4">Add Lead</button>
              <a href="<?php echo getCategoryPageUrl($category); ?>" class="btn btn-secondary px-4">Cancel</a>
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

<!-- Add Lead JS -->
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

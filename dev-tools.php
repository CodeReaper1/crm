<?php
/**
 * Developer Tools Page
 *
 * This page provides links to all debug, test, and diagnostic tools in the CRM system.
 * It's intended for developers and administrators only.
 */

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

// Get user info to check if they're an admin
$user = getUserProfile($user_id);

// For now, let's assume all users can access the dev tools
// In a production environment, you would want to restrict this to admins only
$is_admin = true;

// If not admin, show access denied
if (!$is_admin) {
    include 'components/header.php';
    echo getHeader('Access Denied', 'access-denied');
    ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="alert alert-danger">
                <h4>Access Denied</h4>
                <p>You do not have permission to access the developer tools. This area is restricted to administrators only.</p>
                <a href="index.php" class="btn btn-primary">Return to Dashboard</a>
            </div>
        </div>
    </div>
    <?php
    include 'components/footer.php';
    exit;
}

// If we get here, the user is an admin
include 'components/header.php';
echo getHeader('Developer Tools', 'dev-tools');

// Define categories and their tools
$tools = [
    'Database Tools' => [
        ['name' => 'Check Database Limits', 'file' => 'check_database_limits.php', 'description' => 'Checks MySQL variables and limits that might affect query results'],
        ['name' => 'Check Database Connection', 'file' => 'check_db.php', 'description' => 'Verifies the database connection is working properly'],
        ['name' => 'Check Leads Table', 'file' => 'check_leads_table.php', 'description' => 'Examines the structure of the leads table'],
        ['name' => 'Check Users Table', 'file' => 'check_users_table.php', 'description' => 'Examines the structure of the users table'],
        ['name' => 'Test Database Connection', 'file' => 'test_db_connection.php', 'description' => 'Tests the database connection with detailed diagnostics'],
    ],
    'Lead Management Tools' => [
        ['name' => 'Fix Leads', 'file' => 'fix_leads.php', 'description' => 'Repairs issues with lead data'],
        ['name' => 'Verify Leads', 'file' => 'verify_leads.php', 'description' => 'Verifies lead data integrity'],
        ['name' => 'Test User-Specific Leads', 'file' => 'test_user_specific_leads.php', 'description' => 'Tests lead filtering by user'],
        ['name' => 'Test Move Business', 'file' => 'test_move_business.php', 'description' => 'Tests moving leads between categories'],
        ['name' => 'Debug Move Business', 'file' => 'debug_move_business.php', 'description' => 'Debugs issues with moving leads between categories'],
    ],
    'UI and Functionality Tests' => [
        ['name' => 'Test DataTable', 'file' => 'test_datatable.php', 'description' => 'Tests DataTables functionality'],
        ['name' => 'Test Data Retrieval', 'file' => 'test_data_retrieval.php', 'description' => 'Tests data retrieval functions'],
        ['name' => 'Debug Form', 'file' => 'debug_form.php', 'description' => 'Debugs form submission issues'],
        ['name' => 'Debug Note Save', 'file' => 'debug_note_save.php', 'description' => 'Debugs note saving functionality'],
        ['name' => 'Debug Profile Update', 'file' => 'debug_profile_update.php', 'description' => 'Debugs profile update functionality'],
        ['name' => 'Test Profile Update', 'file' => 'test_profile_update.php', 'description' => 'Tests profile update functionality'],
    ],
    'Performance and Optimization' => [
        ['name' => 'Fix Pagination', 'file' => 'fix_pagination.php', 'description' => 'Fixes pagination issues with large datasets'],
        ['name' => 'Optimize Large Dataset', 'file' => 'optimize_large_dataset.php', 'description' => 'Optimizes handling of large datasets'],
        ['name' => 'Check Notes Structure', 'file' => 'check_notes_structure.php', 'description' => 'Examines the structure of the notes table'],
    ],
    'General Debug Tools' => [
        ['name' => 'Test Debug', 'file' => 'test_debug.php', 'description' => 'General debugging tool'],
        ['name' => 'Debug Functions', 'file' => 'debug_functions.php', 'description' => 'Tests debug functions'],
        ['name' => 'General Test Page', 'file' => 'test.php', 'description' => 'General test page'],
    ]
];

// Function to check if a file exists
function fileExists($file) {
    return file_exists($file);
}
?>

<!-- start page content wrapper-->
<div class="page-content-wrapper">
  <!-- start page content-->
  <div class="page-content">

    <!--start breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Developer</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0 align-items-center">
            <li class="breadcrumb-item"><a href="index.php"><ion-icon name="home-outline"></ion-icon></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Developer Tools</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <h5 class="mb-0">Developer Tools</h5>
              <div class="ms-auto">
                <a href="index.php" class="btn btn-sm btn-outline-secondary">
                  <i class="bx bx-arrow-back"></i> Back to Dashboard
                </a>
              </div>
            </div>
            <p class="card-text text-muted">
              This page provides access to all debug, test, and diagnostic tools in the CRM system.
              These tools are intended for developers and administrators only.
            </p>

            <div class="alert alert-warning">
              <h5><i class="bx bx-error"></i> Warning</h5>
              <p>These tools can modify database records and system settings. Use with caution.</p>
            </div>

            <!-- Tools Accordion -->
            <div class="accordion" id="devToolsAccordion">
              <?php
              $counter = 0;
              foreach ($tools as $category => $categoryTools):
                $counter++;
                $categoryId = 'category' . $counter;
              ?>
              <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?php echo $categoryId; ?>">
                  <button class="accordion-button <?php echo ($counter > 1) ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $categoryId; ?>" aria-expanded="<?php echo ($counter === 1) ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $categoryId; ?>">
                    <?php echo $category; ?> (<?php echo count($categoryTools); ?>)
                  </button>
                </h2>
                <div id="collapse<?php echo $categoryId; ?>" class="accordion-collapse collapse <?php echo ($counter === 1) ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $categoryId; ?>" data-bs-parent="#devToolsAccordion">
                  <div class="accordion-body">
                    <div class="table-responsive">
                      <table class="table table-striped table-hover">
                        <thead>
                          <tr>
                            <th>Tool</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($categoryTools as $tool): ?>
                          <tr>
                            <td><?php echo $tool['name']; ?></td>
                            <td><?php echo $tool['description']; ?></td>
                            <td>
                              <?php if (fileExists($tool['file'])): ?>
                                <span class="badge bg-success">Available</span>
                              <?php else: ?>
                                <span class="badge bg-danger">Not Found</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <?php if (fileExists($tool['file'])): ?>
                                <a href="<?php echo $tool['file']; ?>" class="btn btn-sm btn-primary" target="_blank">
                                  <i class="bx bx-link-external"></i> Open
                                </a>
                              <?php else: ?>
                                <button class="btn btn-sm btn-secondary" disabled>
                                  <i class="bx bx-x"></i> Unavailable
                                </button>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>

            <!-- Create New Tool Section -->
            <div class="mt-4">
              <h5>Create New Test Tool</h5>
              <p class="text-muted">Need a new test or debug tool? Use the form below to create one.</p>

              <form action="create_test_tool.php" method="post" class="row g-3">
                <div class="col-md-6">
                  <label for="toolName" class="form-label">Tool Name</label>
                  <input type="text" class="form-control" id="toolName" name="toolName" placeholder="E.g., Test Database Connection" required>
                </div>
                <div class="col-md-6">
                  <label for="fileName" class="form-label">File Name</label>
                  <input type="text" class="form-control" id="fileName" name="fileName" placeholder="E.g., test_db_connection.php" required>
                </div>
                <div class="col-md-6">
                  <label for="toolCategory" class="form-label">Category</label>
                  <select class="form-select" id="toolCategory" name="toolCategory" required>
                    <option value="">Select a category</option>
                    <?php foreach (array_keys($tools) as $category): ?>
                      <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                    <?php endforeach; ?>
                    <option value="new">Create New Category</option>
                  </select>
                </div>
                <div class="col-md-6 d-none" id="newCategoryDiv">
                  <label for="newCategory" class="form-label">New Category Name</label>
                  <input type="text" class="form-control" id="newCategory" name="newCategory" placeholder="E.g., API Testing Tools">
                </div>
                <div class="col-12">
                  <label for="toolDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="toolDescription" name="toolDescription" rows="3" placeholder="Describe what this tool does" required></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary">Create Tool</button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>
  <!-- end page content-->
</div>

<!-- JavaScript for the accordion and new category option -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize Bootstrap components
  var accordionItems = document.querySelectorAll('.accordion-button');
  accordionItems.forEach(function(item) {
    item.addEventListener('click', function() {
      var target = document.querySelector(this.getAttribute('data-bs-target'));
      if (target) {
        if (target.classList.contains('show')) {
          target.classList.remove('show');
          this.classList.add('collapsed');
          this.setAttribute('aria-expanded', 'false');
        } else {
          // Close all other accordion items
          document.querySelectorAll('.accordion-collapse').forEach(function(collapse) {
            collapse.classList.remove('show');
          });
          document.querySelectorAll('.accordion-button').forEach(function(button) {
            button.classList.add('collapsed');
            button.setAttribute('aria-expanded', 'false');
          });

          // Open this accordion item
          target.classList.add('show');
          this.classList.remove('collapsed');
          this.setAttribute('aria-expanded', 'true');
        }
      }
    });
  });

  // New category option handling
  const categorySelect = document.getElementById('toolCategory');
  const newCategoryDiv = document.getElementById('newCategoryDiv');
  const newCategoryInput = document.getElementById('newCategory');

  if (categorySelect) {
    categorySelect.addEventListener('change', function() {
      if (this.value === 'new') {
        newCategoryDiv.classList.remove('d-none');
        newCategoryInput.setAttribute('required', 'required');
      } else {
        newCategoryDiv.classList.add('d-none');
        newCategoryInput.removeAttribute('required');
      }
    });
  }
});
</script>

<?php include 'components/footer.php'; ?>

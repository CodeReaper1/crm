<?php
/**
 * Create Test Tool
 *
 * This script creates a new test or debug tool based on form input.
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
    header('Location: dev-tools.php');
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toolName = isset($_POST['toolName']) ? trim($_POST['toolName']) : '';
    $fileName = isset($_POST['fileName']) ? trim($_POST['fileName']) : '';
    $toolCategory = isset($_POST['toolCategory']) ? trim($_POST['toolCategory']) : '';
    $newCategory = isset($_POST['newCategory']) ? trim($_POST['newCategory']) : '';
    $toolDescription = isset($_POST['toolDescription']) ? trim($_POST['toolDescription']) : '';

    // Validate inputs
    $errors = [];

    if (empty($toolName)) {
        $errors[] = 'Tool name is required';
    }

    if (empty($fileName)) {
        $errors[] = 'File name is required';
    } else {
        // Ensure file name has .php extension
        if (!preg_match('/\.php$/', $fileName)) {
            $fileName .= '.php';
        }

        // Check if file already exists
        if (file_exists($fileName)) {
            $errors[] = 'A file with this name already exists';
        }
    }

    if (empty($toolCategory)) {
        $errors[] = 'Category is required';
    } elseif ($toolCategory === 'new' && empty($newCategory)) {
        $errors[] = 'New category name is required';
    }

    if (empty($toolDescription)) {
        $errors[] = 'Tool description is required';
    }

    // If no errors, create the file
    if (empty($errors)) {
        // Determine the actual category
        $category = ($toolCategory === 'new') ? $newCategory : $toolCategory;

        // Create the file content
        $fileContent = "<?php
/**
 * " . $toolName . "
 *
 * " . $toolDescription . "
 * Category: " . $category . "
 * Created: " . date('Y-m-d H:i:s') . "
 */

// Start session and check login before ANY output
session_start();

// Check if user is logged in and get user ID
\$user_id = isset(\$_SESSION['user_id']) ? \$_SESSION['user_id'] : null;

// Redirect if not logged in
if (!\$user_id) {
    header('Location: login.php');
    exit;
}

require_once 'functions.php';

// Get user info to check if they're an admin
\$user = getUserProfile(\$user_id);

// For now, let's assume all users can access the dev tools
// In a production environment, you would want to restrict this to admins only
\$is_admin = true;

// If not admin, show access denied
if (!\$is_admin) {
    include 'components/header.php';
    echo getHeader('Access Denied', 'access-denied');
    ?>
    <div class=\"page-content-wrapper\">
        <div class=\"page-content\">
            <div class=\"alert alert-danger\">
                <h4>Access Denied</h4>
                <p>You do not have permission to access this tool. This area is restricted to administrators only.</p>
                <a href=\"index.php\" class=\"btn btn-primary\">Return to Dashboard</a>
            </div>
        </div>
    </div>
    <?php
    include 'components/footer.php';
    exit;
}

// Start HTML output
include 'components/header.php';
echo getHeader('" . $toolName . "', 'dev-tools');
?>

<!-- start page content wrapper-->
<div class=\"page-content-wrapper\">
  <!-- start page content-->
  <div class=\"page-content\">

    <!--start breadcrumb-->
    <div class=\"page-breadcrumb d-none d-sm-flex align-items-center mb-3\">
      <div class=\"breadcrumb-title pe-3\">Developer</div>
      <div class=\"ps-3\">
        <nav aria-label=\"breadcrumb\">
          <ol class=\"breadcrumb mb-0 p-0 align-items-center\">
            <li class=\"breadcrumb-item\"><a href=\"index.php\"><ion-icon name=\"home-outline\"></ion-icon></a>
            </li>
            <li class=\"breadcrumb-item\"><a href=\"dev-tools.php\">Developer Tools</a></li>
            <li class=\"breadcrumb-item active\" aria-current=\"page\">" . $toolName . "</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->

    <div class=\"card\">
      <div class=\"card-body\">
        <div class=\"d-flex align-items-center\">
          <h5 class=\"mb-0\">" . $toolName . "</h5>
          <div class=\"ms-auto\">
            <a href=\"dev-tools.php\" class=\"btn btn-sm btn-outline-secondary\">
              <i class=\"bx bx-arrow-back\"></i> Back to Developer Tools
            </a>
          </div>
        </div>
        <p class=\"card-text text-muted\">
          " . $toolDescription . "
        </p>

        <div class=\"alert alert-info\">
          <h5><i class=\"bx bx-info-circle\"></i> Tool Information</h5>
          <ul>
            <li><strong>Name:</strong> " . $toolName . "</li>
            <li><strong>Category:</strong> " . $category . "</li>
            <li><strong>File:</strong> " . $fileName . "</li>
            <li><strong>Created:</strong> " . date('Y-m-d H:i:s') . "</li>
          </ul>
        </div>

        <!-- Tool Content Goes Here -->
        <div class=\"mt-4\">
          <h5>Tool Content</h5>
          <p>Add your tool-specific content here.</p>

          <!-- Example: Database Connection Test -->
          <div class=\"card\">
            <div class=\"card-body\">
              <h6>Test Results</h6>
              <div class=\"alert alert-success\">
                <p>This is a placeholder for your tool's output.</p>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
  <!-- end page content-->
</div>

<?php include 'components/footer.php'; ?>";

        // Save the file
        if (file_put_contents($fileName, $fileContent) !== false) {
            // Redirect to the dev tools page with success message
            header('Location: dev-tools.php?success=1&file=' . urlencode($fileName));
            exit;
        } else {
            $errors[] = 'Failed to create the file. Check file permissions.';
        }
    }
}

// If we get here, there were errors
include 'components/header.php';
echo getHeader('Create Test Tool - Error', 'dev-tools');
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
            <li class="breadcrumb-item"><a href="dev-tools.php">Developer Tools</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Test Tool</li>
          </ol>
        </nav>
      </div>
    </div>
    <!--end breadcrumb-->

    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center">
          <h5 class="mb-0">Create Test Tool - Error</h5>
          <div class="ms-auto">
            <a href="dev-tools.php" class="btn btn-sm btn-outline-secondary">
              <i class="bx bx-arrow-back"></i> Back to Developer Tools
            </a>
          </div>
        </div>

        <div class="alert alert-danger mt-3">
          <h5><i class="bx bx-error"></i> Errors</h5>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo $error; ?></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <p>Please go back and correct the errors.</p>
        <a href="javascript:history.back()" class="btn btn-primary">
          <i class="bx bx-arrow-back"></i> Go Back
        </a>
      </div>
    </div>

  </div>
  <!-- end page content-->
</div>

<?php include 'components/footer.php'; ?>

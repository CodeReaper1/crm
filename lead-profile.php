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

// Custom CSS for notes
$custom_css = <<<CSS
.notes-container {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 5px;
}

.notes-container::-webkit-scrollbar {
    width: 5px;
}

.notes-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.notes-container::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.notes-container::-webkit-scrollbar-thumb:hover {
    background: #999;
}

.note-item {
    transition: all 0.2s ease;
}

.note-item:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
}

.note-content {
    white-space: pre-line;
    line-height: 1.5;
}
CSS;

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

// Check for note added/error messages from direct form submission
$note_added = isset($_GET['note_added']) ? true : false;
$note_error = isset($_GET['note_error']) ? $_GET['note_error'] : '';

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
echo getHeader('Lead Profile: ' . htmlspecialchars($lead['business_name']), 'leads');
?>

<!-- Custom CSS for notes -->
<style>
<?php echo $custom_css; ?>
</style>

<!-- start page content wrapper-->
<div class="page-content-wrapper">
  <!-- start page content-->
  <div class="page-content">
    <?php if ($note_added): ?>
    <div class="alert alert-success alert-dismissible fade show">
      <i class="bx bx-check-circle me-1"></i> Note added successfully!
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (!empty($note_error)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <i class="bx bx-error-circle me-1"></i> Error adding note: <?php echo htmlspecialchars($note_error); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!--start breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Leads</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0 align-items-center">
            <li class="breadcrumb-item"><a href="dashboard.php"><ion-icon name="home-outline"></ion-icon></a></li>
            <li class="breadcrumb-item"><a href="<?php echo strtolower(str_replace(' ', '-', $category_display_name)) . '.php'; ?>"><?php echo $category_display_name; ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($lead['business_name']); ?></li>
          </ol>
        </nav>
      </div>
      <div class="ms-auto">
        <div class="btn-group">
          <button type="button" class="btn btn-outline-primary">Settings</button>
          <button type="button" class="btn btn-outline-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
            <h6 class="dropdown-header">Move Lead</h6>
            <?php if ($lead['category'] != 'callStack'): ?>
            <a class="dropdown-item move-business" href="javascript:;" data-from="<?php echo $lead['category']; ?>" data-to="callStack">Move to Call Stack</a>
            <?php endif; ?>
            <?php if ($lead['category'] != 'coldLeads'): ?>
            <a class="dropdown-item move-business" href="javascript:;" data-from="<?php echo $lead['category']; ?>" data-to="coldLeads">Move to Cold Leads</a>
            <?php endif; ?>
            <?php if ($lead['category'] != 'warmLeads'): ?>
            <a class="dropdown-item move-business" href="javascript:;" data-from="<?php echo $lead['category']; ?>" data-to="warmLeads">Move to Warm Leads</a>
            <?php endif; ?>
            <?php if ($lead['category'] != 'currentlyWorkingWith'): ?>
            <a class="dropdown-item move-business" href="javascript:;" data-from="<?php echo $lead['category']; ?>" data-to="currentlyWorkingWith">Move to Currently Working With</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
      <div class="col-12 col-lg-8">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <h5 class="mb-0"><?php echo htmlspecialchars($lead['business_name']); ?></h5>
              </div>
              <div class="ms-auto">
                <a href="edit-lead.php?id=<?php echo $lead_id; ?>" class="btn btn-sm btn-outline-primary">
                  <i class="bx bx-edit"></i> Edit Lead
                </a>
              </div>
            </div>
            <hr>
            <div class="row g-3">
              <div class="col-12 col-lg-6">
                <div class="card shadow-none border radius-15">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div>
                        <h6 class="mb-0">Basic Information</h6>
                      </div>
                    </div>
                    <div class="table-responsive mt-3">
                      <table class="table table-striped table-hover table-sm mb-0">
                        <tbody>
                          <tr>
                            <td width="40%"><strong>Business Name</strong></td>
                            <td><?php echo htmlspecialchars($lead['business_name']); ?></td>
                          </tr>
                          <tr>
                            <td><strong>Niche</strong></td>
                            <td><?php echo htmlspecialchars($lead['niche']); ?></td>
                          </tr>
                          <tr>
                            <td><strong>Category</strong></td>
                            <td><span class="badge bg-primary"><?php echo $category_display_name; ?></span></td>
                          </tr>
                          <tr>
                            <td><strong>Email</strong></td>
                            <td>
                              <?php if (!empty($lead['email']) && $lead['email'] != 'no email'): ?>
                                <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>"><?php echo htmlspecialchars($lead['email']); ?></a>
                              <?php else: ?>
                                <span class="text-muted">No email available</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <tr>
                            <td><strong>Phone</strong></td>
                            <td>
                              <?php if (isset($lead['phone_numbers']) && !empty($lead['phone_numbers'])): ?>
                                <?php if (is_array($lead['phone_numbers'])): ?>
                                  <?php foreach ($lead['phone_numbers'] as $phone): ?>
                                    <?php if (!empty($phone)): ?>
                                      <div><a href="tel:<?php echo htmlspecialchars($phone); ?>"><?php echo htmlspecialchars($phone); ?></a></div>
                                    <?php endif; ?>
                                  <?php endforeach; ?>
                                <?php else: ?>
                                  <a href="tel:<?php echo htmlspecialchars($lead['phone_numbers']); ?>"><?php echo htmlspecialchars($lead['phone_numbers']); ?></a>
                                <?php endif; ?>
                              <?php else: ?>
                                <span class="text-muted">No phone available</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <tr>
                            <td><strong>Website</strong></td>
                            <td>
                              <?php if (!empty($lead['website']) && $lead['website'] != 'no website'): ?>
                                <a href="<?php echo htmlspecialchars($lead['website']); ?>" target="_blank"><?php echo htmlspecialchars($lead['website']); ?></a>
                              <?php else: ?>
                                <span class="text-muted">No website available</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-lg-6">
                <div class="card shadow-none border radius-15">
                  <div class="card-body">
                    <div class="d-flex align-items-center">
                      <div>
                        <h6 class="mb-0">Additional Information</h6>
                      </div>
                    </div>
                    <div class="table-responsive mt-3">
                      <table class="table table-striped table-hover table-sm mb-0">
                        <tbody>
                          <tr>
                            <td width="40%"><strong>Created</strong></td>
                            <td>
                              <?php if (isset($lead['created_at'])): ?>
                                <?php echo date('M d, Y', strtotime($lead['created_at'])); ?>
                              <?php else: ?>
                                <span class="text-muted">Not available</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <tr>
                            <td><strong>Last Updated</strong></td>
                            <td>
                              <?php if (isset($lead['updated_at'])): ?>
                                <?php echo date('M d, Y', strtotime($lead['updated_at'])); ?>
                              <?php else: ?>
                                <span class="text-muted">Not available</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <tr>
                            <td><strong>Status</strong></td>
                            <td>
                              <?php if (isset($lead['status'])): ?>
                                <span class="badge bg-info"><?php echo htmlspecialchars($lead['status']); ?></span>
                              <?php else: ?>
                                <span class="badge bg-secondary">Unknown</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                          <tr>
                            <td><strong>Assigned To</strong></td>
                            <td>
                              <?php if (isset($lead['assigned_to']) && !empty($lead['assigned_to'])): ?>
                                <?php echo htmlspecialchars(getUserName($lead['assigned_to'])); ?>
                              <?php else: ?>
                                <span class="text-muted">Not assigned</span>
                              <?php endif; ?>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <h5 class="mb-0">Notes</h5>
              </div>
              <div class="ms-auto">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                  <i class="bx bx-plus"></i> Add Note
                </button>
              </div>
            </div>
            <hr>
            <div class="notes-container">
              <?php if (!empty($lead['notes']) && $lead['notes'] != 'No Notes' && $lead['notes'] != 'noValue'): ?>
                <?php
                // Split notes by double newline to separate individual notes
                $notes_array = preg_split('/\n\s*\n/', $lead['notes']);
                $notes_array = array_reverse($notes_array); // Show newest notes first

                foreach ($notes_array as $note):
                  // Check if the note has a timestamp and user (format: YYYY-MM-DD HH:MM by Username:)
                  if (preg_match('/^(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2})\s+by\s+([^:]+):\s*(.*)$/s', $note, $matches)) {
                    $timestamp = $matches[1];
                    $username = $matches[2];
                    $content = $matches[3];
                  } else if (preg_match('/^(\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}):\s*(.*)$/s', $note, $matches)) {
                    // Legacy format without username
                    $timestamp = $matches[1];
                    $username = 'Unknown';
                    $content = $matches[2];
                  } else {
                    $timestamp = 'Unknown date';
                    $username = 'Unknown';
                    $content = $note;
                  }
                ?>
                <div class="note-item card shadow-none border radius-15 mb-3">
                  <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-2">
                      <div>
                        <i class="bx bx-calendar-event me-1"></i>
                        <span class="text-muted small"><?php echo htmlspecialchars($timestamp); ?></span>
                        <?php if ($username !== 'Unknown'): ?>
                        <span class="text-muted small ms-2">
                          <i class="bx bx-user me-1"></i>
                          <?php echo htmlspecialchars($username); ?>
                        </span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="note-content">
                      <?php echo nl2br(htmlspecialchars($content)); ?>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="text-center py-5">
                  <div class="mb-3">
                    <i class="bx bx-notepad fs-1 text-muted"></i>
                  </div>
                  <p class="text-muted">No notes available for this lead.</p>
                  <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                    <i class="bx bx-plus"></i> Add First Note
                  </button>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Move Lead Card -->
        <div class="card mt-3">
          <div class="card-body">
            <h5 class="card-title">Move Lead</h5>
            <div class="d-grid gap-2 mt-3">
              <?php if ($lead['category'] != 'callStack'): ?>
              <button class="btn btn-primary move-business" data-from="<?php echo $lead['category']; ?>" data-to="callStack">
                <i class="bx bx-arrow-left"></i> Move to Call Stack
              </button>
              <?php endif; ?>

              <?php if ($lead['category'] != 'coldLeads'): ?>
              <button class="btn btn-info move-business" data-from="<?php echo $lead['category']; ?>" data-to="coldLeads">
                <?php echo ($lead['category'] == 'callStack') ? '<i class="bx bx-arrow-right"></i>' : '<i class="bx bx-arrow-left"></i>'; ?>
                Move to Cold Leads
              </button>
              <?php endif; ?>

              <?php if ($lead['category'] != 'warmLeads'): ?>
              <button class="btn btn-warning move-business" data-from="<?php echo $lead['category']; ?>" data-to="warmLeads">
                <?php echo ($lead['category'] == 'callStack' || $lead['category'] == 'coldLeads') ? '<i class="bx bx-arrow-right"></i>' : '<i class="bx bx-arrow-left"></i>'; ?>
                Move to Warm Leads
              </button>
              <?php endif; ?>

              <?php if ($lead['category'] != 'currentlyWorkingWith'): ?>
              <button class="btn btn-success move-business" data-from="<?php echo $lead['category']; ?>" data-to="currentlyWorkingWith">
                <i class="bx bx-arrow-right"></i> Move to Currently Working With
              </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-light">
            <h5 class="modal-title"><i class="bx bx-note me-1"></i> Add Note</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="addNoteForm" method="post" action="save_note.php">
            <div class="modal-body">
              <div id="noteAlertContainer"></div>
              <input type="hidden" name="lead_id" value="<?php echo $lead_id; ?>">
              <div class="mb-3">
                <label for="note_content" class="form-label">Note Content</label>
                <div class="form-text mb-2">Add details about your interaction with this lead, follow-up plans, or any other important information.</div>
                <textarea class="form-control" id="note_content" name="note_content" rows="6" placeholder="Enter your note here..." required></textarea>
              </div>
              <div class="form-text text-muted">
                <i class="bx bx-info-circle"></i> Notes are timestamped automatically and cannot be edited after saving.
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> Save Note</button>
              <button type="button" id="fallbackSubmit" class="btn btn-outline-primary d-none">Try Alternative Method</button>
            </div>
          </form>
        </div>
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

<!-- Business Manager -->
<script src="assets/js/business-manager.js"></script>

<!-- Button Fix -->
<script src="assets/js/button-fix.js"></script>

<!-- Lead Profile JS -->
<script>
$(document).ready(function() {
    // Store the lead ID in localStorage
    localStorage.setItem('selectedLeadId', '<?php echo $lead_id; ?>');

    // Handle Add Note Form Submission
    $('#addNoteForm').on('submit', function(e) {
        e.preventDefault();

        // Show loading indicator
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        submitBtn.html('<i class="bx bx-loader bx-spin me-1"></i> Saving...').prop('disabled', true);

        // Get form data
        const leadId = $('input[name="lead_id"]').val();
        const noteContent = $('#note_content').val();

        console.log('Submitting note:', {
            lead_id: leadId,
            note_content: noteContent
        });

        // Use FormData for more reliable form submission
        const formData = new FormData();
        formData.append('lead_id', leadId);
        formData.append('note_content', noteContent);

        // Clear any previous alerts
        $('#noteAlertContainer').empty();

        $.ajax({
            url: 'save_note.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                console.log('Note save response:', response);

                if (response.success) {
                    // Show success message
                    const successAlert = $('<div class="alert alert-success">Note saved successfully!</div>');
                    $('#noteAlertContainer').html(successAlert);

                    // Close the modal after a short delay
                    setTimeout(function() {
                        $('#addNoteModal').modal('hide');
                        // Reload the page to show the new note
                        location.reload();
                    }, 1000);
                } else {
                    // Show error message
                    const errorAlert = $('<div class="alert alert-danger">Error: ' + response.message + '</div>');
                    $('#noteAlertContainer').html(errorAlert);

                    // Show fallback button after error
                    $('#fallbackSubmit').removeClass('d-none');

                    // Reset button
                    submitBtn.html(originalBtnText).prop('disabled', false);

                    // Log debug info if available
                    if (response.debug) {
                        console.error('Debug info:', response.debug);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                console.error('Response:', xhr.responseText);

                // Show error message
                const errorAlert = $('<div class="alert alert-danger">An error occurred while saving the note</div>');
                $('#noteAlertContainer').html(errorAlert);

                // Show fallback button after error
                $('#fallbackSubmit').removeClass('d-none');

                // Reset button
                submitBtn.html(originalBtnText).prop('disabled', false);
            }
        });
    });

    // Handle fallback submission
    $('#fallbackSubmit').on('click', function() {
        // Submit the form directly
        $('#addNoteForm').off('submit'); // Remove AJAX handler
        $('#addNoteForm').submit(); // Submit the form normally
    });

    // Handle Move Business
    $('.move-business').on('click', function() {
        const fromCategory = $(this).data('from');
        const toCategory = $(this).data('to');
        const leadId = '<?php echo $lead_id; ?>';

        // Show confirmation dialog
        if (confirm(`Are you sure you want to move this lead from ${formatCategoryName(fromCategory)} to ${formatCategoryName(toCategory)}?`)) {
            // Show loading message
            const loadingMessage = $('<div class="alert alert-info">Moving lead...</div>');
            $(this).after(loadingMessage);
            $(this).prop('disabled', true);

            $.ajax({
                url: 'move_business.php',
                type: 'POST',
                data: {
                    id: leadId,
                    from_category: fromCategory,
                    to_category: toCategory
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        loadingMessage.removeClass('alert-info').addClass('alert-success').text(response.message);

                        // Redirect to the new category page after 2 seconds
                        setTimeout(function() {
                            window.location.href = getCategoryPageUrl(toCategory);
                        }, 2000);
                    } else {
                        // Show error message
                        loadingMessage.removeClass('alert-info').addClass('alert-danger').text('Error: ' + response.message);
                        $(this).prop('disabled', false);
                    }
                },
                error: function() {
                    // Show error message
                    loadingMessage.removeClass('alert-info').addClass('alert-danger').text('An error occurred while moving the lead');
                    $(this).prop('disabled', false);
                }
            });
        }
    });

    // Helper function to format category name
    function formatCategoryName(category) {
        switch(category) {
            case 'callStack': return 'Call Stack';
            case 'coldLeads': return 'Cold Leads';
            case 'warmLeads': return 'Warm Leads';
            case 'currentlyWorkingWith': return 'Currently Working With';
            default: return category;
        }
    }

    // Helper function to get category page URL
    function getCategoryPageUrl(category) {
        switch(category) {
            case 'callStack': return 'page-call-stack.php';
            case 'coldLeads': return 'page-cold-leads.php';
            case 'warmLeads': return 'page-warm-leads.php';
            case 'currentlyWorkingWith': return 'currently-working-with.php';
            default: return 'dashboard.php';
        }
    }
});
</script>

<!-- Main JS-->
<script src="assets/js/main.js"></script>

</body>
</html>

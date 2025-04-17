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
include 'components/header.php';
echo getHeader('Warm Leads', 'warm');
?>

<!-- start page content wrapper-->
<div class="page-content-wrapper">
  <!-- start page content-->
  <div class="page-content">

    <!--start breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
      <div class="breadcrumb-title pe-3">Tables</div>
      <div class="ps-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0 p-0 align-items-center">
            <li class="breadcrumb-item"><a href="javascript:;"><ion-icon name="home-outline"></ion-icon></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Data Table</li>
          </ol>
        </nav>
      </div>
      <div class="ms-auto">
        <div class="btn-group">
          <button type="button" class="btn btn-outline-primary">Settings</button>
          <button type="button" class="btn btn-outline-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">	<span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
            <h6 class="dropdown-header">Move Selected Lead</h6>
            <a class="dropdown-item move-business" href="javascript:;" data-from="warmLeads" data-to="callStack">Move to Call Stack</a>
            <a class="dropdown-item move-business" href="javascript:;" data-from="warmLeads" data-to="coldLeads">Move to Cold Leads</a>
            <a class="dropdown-item move-business" href="javascript:;" data-from="warmLeads" data-to="currentlyWorkingWith">Move to Currently Working With</a>
          </div>
        </div>
      </div>
    </div>
    <!--end breadcrumb-->

    <!-- Pipeline Navigation -->
    <?php include 'components/pipeline-nav.php'; ?>

    <div class="d-flex align-items-center">
      <h6 class="mb-0 text-uppercase">Warm Leads</h6>
      <div class="ms-auto">
        <a href="add-lead.php?category=warmLeads" class="btn btn-sm btn-primary">
          <i class="bx bx-plus"></i> Add New Lead
        </a>
      </div>
    </div>
    <hr/>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="warmLeadsTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>Niche</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Website</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <!-- DataTable will populate this -->
            </tbody>
            <tfoot>
              <tr>
                <th>Niche</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Website</th>
                <th>Notes</th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <!-- Move Lead Buttons have been removed and are now only available in the lead profile page -->

  </div>
  <!-- end page content-->
</div>

<!--Start Back To Top Button-->
<a href="javaScript:;" class="back-to-top"><ion-icon name="arrow-up-outline"></ion-icon></a>
<!--End Back To Top Button-->

<!--start switcher-->
<div class="switcher-body">
  <button class="btn btn-primary btn-switcher shadow-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling"><ion-icon name="color-palette-sharp" class="me-0"></ion-icon></button>
  <div class="offcanvas offcanvas-end shadow border-start-0 p-2" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling">
    <div class="offcanvas-header border-bottom">
      <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Theme Customizer</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <h6 class="mb-0">Theme Variation</h6>
      <hr>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="LightTheme" value="option1" checked>
        <label class="form-check-label" for="LightTheme">Light</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="DarkTheme" value="option2">
        <label class="form-check-label" for="DarkTheme">Dark</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="inlineRadioOptions" id="SemiDark" value="option3">
        <label class="form-check-label" for="SemiDark">Semi Dark</label>
      </div>
      <hr/>
      <h6 class="mb-0">Header Colors</h6>
      <hr/>
      <div class="header-colors-indigators">
        <div class="row row-cols-auto g-3">
          <div class="col">
            <div class="indigator headercolor1" id="headercolor1"></div>
          </div>
          <div class="col">
            <div class="indigator headercolor2" id="headercolor2"></div>
          </div>
          <div class="col">
            <div class="indigator headercolor3" id="headercolor3"></div>
          </div>
          <div class="col">
            <div class="indigator headercolor4" id="headercolor4"></div>
          </div>
          <div class="col">
            <div class="indigator headercolor5" id="headercolor5"></div>
          </div>
          <div class="col">
            <div class="indigator headercolor6" id="headercolor6"></div>
          </div>
          <div class="col">
            <div class="indigator headercolor7" id="headercolor7"></div>
          </div>
          <div class="col">
            <div class="indigator headercolor8" id="headercolor8"></div>
          </div>
        </div>
      </div>
      <hr/>
      <h6 class="mb-0">Sidebar Colors</h6>
      <hr/>
      <div class="header-colors-indigators">
        <div class="row row-cols-auto g-3">
          <div class="col">
            <div class="indigator sidebarcolor1" id="sidebarcolor1"></div>
          </div>
          <div class="col">
            <div class="indigator sidebarcolor2" id="sidebarcolor2"></div>
          </div>
          <div class="col">
            <div class="indigator sidebarcolor3" id="sidebarcolor3"></div>
          </div>
          <div class="col">
            <div class="indigator sidebarcolor4" id="sidebarcolor4"></div>
          </div>
          <div class="col">
            <div class="indigator sidebarcolor5" id="sidebarcolor5"></div>
          </div>
          <div class="col">
            <div class="indigator sidebarcolor6" id="sidebarcolor6"></div>
          </div>
          <div class="col">
            <div class="indigator sidebarcolor7" id="sidebarcolor7"></div>
          </div>
          <div class="col">
            <div class="indigator sidebarcolor8" id="sidebarcolor8"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--end switcher-->

<!--start overlay-->
<div class="overlay nav-toggle-icon"></div>
<!--end overlay-->

<!-- JS Files-->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<!--plugins-->
<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>

<!-- Custom DataTable Initialization -->
<script>
$(document).ready(function() {
    var warmLeadsTable = $('#warmLeadsTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "fetch_leads.php",
            "type": "POST",
            "data": function(d) {
                d.category = "warmLeads"; // Explicitly set the category
                return d;
            }
        },
        "paging": true,
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "language": {
            "lengthMenu": "Show _MENU_ entries per page",
            "zeroRecords": "No matching records found",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Search:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "dom": '<"top"lf>rt<"bottom"ip><"clear">',
        "responsive": true,
        "columns": [
            { "data": "niche" },
            { "data": "business_name" },
            { "data": "email" },
            { "data": "phone_numbers" },
            { "data": "website" },
            { "data": "notes" }
        ],
        "rowCallback": function(row, data) {
            // Add data-id attribute to the row for selection
            $(row).attr('data-id', data.id);
        }
    });

    // Add click handler for row selection
    $('#warmLeadsTable tbody').on('click', 'tr', function(e) {
        // If the click was on a button or link, don't handle it here
        if ($(e.target).closest('button, a').length) {
            return;
        }

        // No need to select rows or enable move buttons since they've been removed
        // Just get the data from the row
        var data = warmLeadsTable.row(this).data();
        if (data) {
            const leadId = data.id;
            const businessName = data.business_name;

            // Store the selected ID in localStorage
            localStorage.setItem('selectedLeadId', leadId);

            // Navigate to the lead profile page
            window.location.href = `lead-profile.php?id=${leadId}`;
        }
    });

    // Check if there's a previously selected lead ID in localStorage
    // We're commenting this out to prevent automatic navigation to lead profile
    /*
    const savedLeadId = localStorage.getItem('selectedLeadId');
    if (savedLeadId) {
        // Wait for the table to be fully loaded
        warmLeadsTable.on('draw', function() {
            // Find the row with this ID and select it
            const savedRow = $(`#warmLeadsTable tbody tr[data-id="${savedLeadId}"]`);
            if (savedRow.length > 0) {
                savedRow.click();
            }
        });
    }
    */

    // Clear the selected lead ID from localStorage when the page loads
    localStorage.removeItem('selectedLeadId');
});
</script>

<!-- Business Manager -->
<script src="assets/js/business-manager.js"></script>

<!-- Button Fix -->
<script src="assets/js/button-fix.js"></script>

<!-- Main JS-->
<script src="assets/js/main.js"></script>

</body>
</html>

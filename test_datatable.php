<?php
// Start session and check login before ANY output
session_start();

// Set a dummy user ID for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

require_once 'functions.php';
include 'components/header.php'; 
echo getHeader('Test DataTable', 'test');
?>

<!-- start page content wrapper-->
<div class="page-content-wrapper">
  <!-- start page content-->
  <div class="page-content">

    <h6 class="mb-0 text-uppercase">Test DataTable</h6>
    <hr/>
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example" class="table table-striped table-bordered" style="width:100%">
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

    <div id="debug-output" class="mt-4">
      <h5>Debug Output:</h5>
      <pre id="debug-content"></pre>
    </div>

  </div>
  <!-- end page content-->
</div>

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

<script>
$(document).ready(function() {
    var table = $('#example').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "fetch_leads.php",
            "type": "POST",
            "dataSrc": function(json) {
                console.log('DataTable response:', json);
                $('#debug-content').text(JSON.stringify(json, null, 2));
                return json.data;
            }
        },
        "columns": [
            { "data": "niche" },
            { "data": "business_name" },
            { "data": "email" },
            { "data": "phone_numbers" },
            { "data": "website" },
            { "data": "notes" }
        ]
    });
});
</script>

</body>
</html>

/**
 * Pagination Defaults
 * 
 * This script sets default pagination settings for all DataTables in the application.
 * Optimized for a large dataset of 26,071 leads.
 */

$(document).ready(function() {
    // Set default options for all DataTables
    $.extend(true, $.fn.dataTable.defaults, {
        "pageLength": 100,
        "lengthMenu": [[50, 100, 250, 500, 1000], [50, 100, 250, 500, 1000]],
        "pagingType": "full_numbers",
        "language": {
            "lengthMenu": "Show _MENU_ entries per page",
            "zeroRecords": "No matching records found",
            "info": "Showing page _PAGE_ of _PAGES_",
            "infoEmpty": "No records available",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "search": "Search:",
            "paginate": {
                "first": '<i class="bx bx-chevrons-left"></i>',
                "last": '<i class="bx bx-chevrons-right"></i>',
                "next": '<i class="bx bx-chevron-right"></i>',
                "previous": '<i class="bx bx-chevron-left"></i>'
            }
        },
        "dom": '<"top"lf>rt<"bottom"ip><"clear">',
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "deferRender": true
    });
});

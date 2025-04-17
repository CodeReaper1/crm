$(function() {
    "use strict";

    $(document).ready(function() {
        // Initialize DataTable if it exists on the page
        if ($('#example').length > 0) {
            var dataTable = $('#example').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "fetch_leads.php",
                    "type": "POST",
                    "dataSrc": function(json) {
                        console.log('DataTable response:', json); // Debug output
                        return json.data;
                    }
                },
                "paging": true,
                "pageLength": 10, // Show 10 entries per page
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
            $('#example tbody').on('click', 'tr', function() {
                $('#example tbody tr').removeClass('selected');
                $(this).addClass('selected');

                // Enable move buttons
                $('.move-business').prop('disabled', false);

                // Get the data from the row
                var data = dataTable.row(this).data();
                if (data) {
                    const leadId = data.id;
                    const businessName = data.business_name;

                    // Show selected lead message
                    if (!$('#selectedLeadMessage').length && $('#moveButtonsContainer').length > 0) {
                        $('<div id="selectedLeadMessage" class="alert alert-info mt-3 mb-3"></div>').insertBefore('#moveButtonsContainer');
                    }

                    if ($('#selectedLeadMessage').length > 0) {
                        $('#selectedLeadMessage').html(`<strong>Selected Lead:</strong> ${businessName} (ID: ${leadId})`);
                    }

                    // Store the selected ID in localStorage
                    localStorage.setItem('selectedLeadId', leadId);
                }
            });
        }

        // Handle action clicks
        $('#example').on('click', '.move-to-warm', function(e) {
            e.preventDefault();
            const leadId = $(this).data('id');
            $.ajax({
                url: 'promote_lead.php',
                type: 'POST',
                data: { id: leadId, action: 'promote_to_warm' },
                success: function(response) {
                    if (response.success) {
                        // Refresh the table
                        $('#example').DataTable().ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });

        $('#example').on('click', '.add-to-callstack', function(e) {
            e.preventDefault();
            const leadId = $(this).data('id');
            $.ajax({
                url: 'update_lead.php',
                type: 'POST',
                data: { id: leadId, action: 'add_to_callstack' },
                success: function(response) {
                    if (response.success) {
                        // Refresh the table
                        $('#example').DataTable().ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });

        // New handler for removing from call stack
        $('#example').on('click', '.remove-from-callstack', function(e) {
            e.preventDefault();
            const leadId = $(this).data('id');
            $.ajax({
                url: 'update_lead.php',
                type: 'POST',
                data: { id: leadId, action: 'remove_from_callstack' },
                success: function(response) {
                    if (response.success) {
                        // Refresh the table
                        $('#example').DataTable().ajax.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        });
    });

    $(document).ready(function() {
        var table = $('#example2').DataTable({
            lengthChange: false,
            buttons: ['copy', 'excel', 'pdf', 'print']
        });

        table.buttons().container()
            .appendTo('#example2_wrapper .col-md-6:eq(0)');
    });
});
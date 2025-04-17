/**
 * Business Manager
 *
 * This file contains functions for managing businesses in the CRM system.
 * It allows moving businesses between different sections of the sales pipeline.
 */

$(function() {
    // Add a highlight effect to show which row is selected
    function highlightSelectedRow(row) {
        // Remove highlight from all rows
        $('tbody tr').removeClass('selected');
        // Add highlight to the selected row
        $(row).addClass('selected');
        // Show a visual indicator that the row is selected
        $(row).css('transition', 'background-color 0.3s');

        // Store the selected ID in localStorage so it persists between page loads
        const leadId = $(row).data('id');
        localStorage.setItem('selectedLeadId', leadId);

        // Get the business name from the row (usually in the second column)
        const businessName = $(row).find('td:eq(1)').text();

        // Show a message indicating which lead is selected
        if (!$('#selectedLeadMessage').length) {
            $('<div id="selectedLeadMessage" class="alert alert-info mt-3 mb-3"></div>').insertBefore('#moveButtonsContainer');
        }
        $('#selectedLeadMessage').html(`<strong>Selected Lead:</strong> ${businessName} (ID: ${leadId})`);

        // Scroll to the message if it's not visible
        $('html, body').animate({
            scrollTop: $('#selectedLeadMessage').offset().top - 100
        }, 500);

        // Enable all move buttons when a row is selected
        $('.move-business').removeClass('disabled').prop('disabled', false);

        // Make sure the buttons are visible
        $('.btn-move-lead').css('opacity', '1');
    }

    // Handle moving businesses between sections
    $(document).on('click', '.move-business', function(e) {
        e.preventDefault();
        const fromCategory = $(this).data('from');
        const toCategory = $(this).data('to');

        // Get the selected business ID from localStorage
        const businessId = localStorage.getItem('selectedLeadId');
        if (!businessId) {
            alert('Please select a business to move');
            return;
        }

        // Show confirmation dialog
        if (confirm(`Are you sure you want to move this business from ${formatCategoryName(fromCategory)} to ${formatCategoryName(toCategory)}?`)) {
            moveBusiness(businessId, fromCategory, toCategory);
        }
    });

    // We don't need to add click handlers here anymore
    // They are now handled in the individual page scripts

    // Initialize: disable move buttons until a row is selected
    $('.move-business').addClass('disabled').prop('disabled', true);

    // If there's a selected lead ID in localStorage, enable the buttons
    if (localStorage.getItem('selectedLeadId')) {
        $('.move-business').removeClass('disabled').prop('disabled', false);
    }

    // We don't need to check for previously selected lead ID here anymore
    // This is now handled in the individual page scripts

    // We don't need to add data-id attributes here anymore
    // This is now handled in the DataTable rowCallback function

    /**
     * Format category name for display
     */
    function formatCategoryName(category) {
        switch(category) {
            case 'callStack': return 'Call Stack';
            case 'coldLeads': return 'Cold Leads';
            case 'warmLeads': return 'Warm Leads';
            case 'currentlyWorkingWith': return 'Currently Working With';
            default: return category;
        }
    }

    /**
     * Move business between categories
     */
    function moveBusiness(id, fromCategory, toCategory) {
        console.log('Moving business:', { id, fromCategory, toCategory });

        // Show a loading message
        if (!$('#moveStatusMessage').length) {
            $('<div id="moveStatusMessage" class="alert alert-info mt-3 mb-3">Moving business...</div>').insertAfter('#selectedLeadMessage');
        } else {
            $('#moveStatusMessage').html('Moving business...');
        }

        $.ajax({
            url: 'move_business.php',
            type: 'POST',
            data: {
                id: id,
                from_category: fromCategory,
                to_category: toCategory
            },
            dataType: 'json',
            success: function(response) {
                console.log('Move response:', response);

                if (response.success) {
                    $('#moveStatusMessage').removeClass('alert-info alert-danger').addClass('alert-success').html(response.message);

                    // Show a success message for 2 seconds, then refresh
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                } else {
                    $('#moveStatusMessage').removeClass('alert-info alert-success').addClass('alert-danger').html('Error: ' + response.message);
                    console.error('Move failed:', response.debug);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', { xhr, status, error });
                $('#moveStatusMessage').removeClass('alert-info alert-success').addClass('alert-danger').html('An error occurred while moving the business');
            }
        });
    }

    // Add enhanced styling for selected rows and disabled buttons
    $('<style>')
        .text(`
            tbody tr.selected {
                background-color: #e0f7fa !important;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
                font-weight: bold;
            }
            tbody tr:hover {
                cursor: pointer;
                background-color: #f5f5f5;
            }
            .move-business.disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }
            .btn-move-lead {
                margin-top: 10px;
                width: 100%;
            }
        `)
        .appendTo('head');
});

/**
 * Enhanced Search and Filtering
 *
 * This script adds advanced search and filtering capabilities to DataTables
 * to improve usability with large datasets.
 */

$(document).ready(function() {
    // Check if we're on a page with a DataTable
    if ($.fn.dataTable.isDataTable('table')) {
        // Add advanced search panel
        addAdvancedSearch();

        // Add custom parameter to DataTables AJAX requests
        var table = $('table').DataTable();
        if (table.settings()[0].oFeatures.bServerSide) {
            // Initialize the hasNotes setting
            table.settings()[0].hasNotes = false;

            // Add a preXhr callback to modify the AJAX data before it's sent
            $(table.table().node()).on('preXhr.dt', function(e, settings, data) {
                // Add the hasNotes parameter to the AJAX request
                data.hasNotes = settings.hasNotes || false;
            });
        }
    }

    // Add advanced search to Call Stack table specifically
    if ($('#callStackTable').length) {
        enhanceCallStackTable();
    }
});

/**
 * Add advanced search panel to the page
 */
function addAdvancedSearch() {
    // Create the advanced search panel
    var searchPanel = $('<div class="card mb-3">' +
        '<div class="card-body">' +
            '<h5 class="card-title">Advanced Search</h5>' +
            '<div class="row g-3">' +
                '<div class="col-md-4">' +
                    '<label for="nicheFilter" class="form-label">Niche</label>' +
                    '<input type="text" class="form-control" id="nicheFilter" placeholder="Search by niche">' +
                '</div>' +
                '<div class="col-md-4">' +
                    '<label for="companyFilter" class="form-label">Company</label>' +
                    '<input type="text" class="form-control" id="companyFilter" placeholder="Search by company name">' +
                '</div>' +
                '<div class="col-md-4">' +
                    '<label for="emailFilter" class="form-label">Email</label>' +
                    '<input type="text" class="form-control" id="emailFilter" placeholder="Search by email">' +
                '</div>' +
            '</div>' +
            '<div class="row g-3 mt-2">' +
                '<div class="col-md-4">' +
                    '<label for="phoneFilter" class="form-label">Phone</label>' +
                    '<input type="text" class="form-control" id="phoneFilter" placeholder="Search by phone">' +
                '</div>' +
                '<div class="col-md-4">' +
                    '<label for="websiteFilter" class="form-label">Website</label>' +
                    '<input type="text" class="form-control" id="websiteFilter" placeholder="Search by website">' +
                '</div>' +
                '<div class="col-md-4">' +
                    '<div class="form-check mt-4">' +
                        '<input class="form-check-input" type="checkbox" id="hasNotesFilter">' +
                        '<label class="form-check-label" for="hasNotesFilter">Show only leads with notes</label>' +
                    '</div>' +
                '</div>' +
            '</div>' +
            '<div class="row mt-3">' +
                '<div class="col-12">' +
                    '<button id="applyFilters" class="btn btn-primary">Apply Filters</button> ' +
                    '<button id="clearFilters" class="btn btn-outline-secondary">Clear Filters</button>' +
                '</div>' +
            '</div>' +
        '</div>' +
    '</div>');

    // Insert the search panel before the table
    $('.table-responsive').closest('.card').before(searchPanel);

    // Add event listeners for the filter buttons
    $('#applyFilters').on('click', function() {
        applyFilters();
    });

    $('#clearFilters').on('click', function() {
        clearFilters();
    });

    // Add event listeners for Enter key on filter inputs
    $('.form-control').on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            applyFilters();
        }
    });
}

/**
 * Apply filters to the DataTable
 */
function applyFilters() {
    var table = $('table').DataTable();

    // Build the search query
    var searchQueries = [];

    if ($('#nicheFilter').val()) {
        searchQueries.push($('#nicheFilter').val());
    }

    if ($('#companyFilter').val()) {
        searchQueries.push($('#companyFilter').val());
    }

    if ($('#emailFilter').val()) {
        searchQueries.push($('#emailFilter').val());
    }

    if ($('#phoneFilter').val()) {
        searchQueries.push($('#phoneFilter').val());
    }

    if ($('#websiteFilter').val()) {
        searchQueries.push($('#websiteFilter').val());
    }

    // Notes filter is now a checkbox, not a search input

    // Check if the "Has Notes" checkbox is checked
    var hasNotes = $('#hasNotesFilter').prop('checked');

    // Join all search terms with a space (AND operator in DataTables search)
    var searchQuery = searchQueries.join(' ');

    // If we're using server-side processing, we need to add the hasNotes parameter
    if (table.settings()[0].oFeatures.bServerSide) {
        // Store the hasNotes value in a data attribute on the table
        table.settings()[0].hasNotes = hasNotes;

        // Trigger a redraw to send the new parameter to the server
        table.draw();
    } else {
        // For client-side processing, apply the search and then filter for notes if needed
        table.search(searchQuery).draw();

        // If the hasNotes checkbox is checked, filter the table to only show rows with notes
        if (hasNotes) {
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Find the index of the notes column (usually the last column)
                    var notesColumnIndex = table.column('notes:name').index();
                    if (notesColumnIndex === undefined) {
                        // If we can't find a column named 'notes', try the 5th column (0-based index 5)
                        notesColumnIndex = 5;
                    }

                    // Get the notes value for this row
                    var notes = data[notesColumnIndex];

                    // Return true if notes is not empty
                    return notes && notes.trim() !== '';
                }
            );

            // Redraw the table with the custom search function
            table.draw();

            // Remove the custom search function after drawing
            $.fn.dataTable.ext.search.pop();
        }
    }
}

/**
 * Clear all filters
 */
function clearFilters() {
    // Clear all input fields
    $('.form-control').val('');

    // Uncheck the "Has Notes" checkbox
    $('#hasNotesFilter').prop('checked', false);

    // Reset the DataTable search
    var table = $('table').DataTable();

    // If we're using server-side processing, clear the hasNotes parameter
    if (table.settings()[0].oFeatures.bServerSide) {
        table.settings()[0].hasNotes = false;
    }

    table.search('').draw();
}

/**
 * Enhance the Call Stack table with additional features
 */
function enhanceCallStackTable() {
    // Add a counter to show current position in the dataset
    $('#callStackTable_info').after('<div id="positionCounter" class="mt-2 text-muted"></div>');

    // Update the counter when the table is drawn
    $('#callStackTable').on('draw.dt', function() {
        var table = $('#callStackTable').DataTable();
        var info = table.page.info();
        var currentPosition = info.start + 1;
        var totalRecords = info.recordsTotal;
        var percentComplete = Math.round((currentPosition / totalRecords) * 100);

        $('#positionCounter').html(
            'Viewing position ' + currentPosition + ' to ' + (info.end) + ' of ' + totalRecords +
            ' (' + percentComplete + '% through the dataset)'
        );
    });

    // Add keyboard navigation
    $(document).keydown(function(e) {
        var table = $('#callStackTable').DataTable();

        // Right arrow - next page
        if (e.keyCode === 39 && !$(e.target).is('input, textarea')) {
            table.page('next').draw('page');
            return false;
        }

        // Left arrow - previous page
        if (e.keyCode === 37 && !$(e.target).is('input, textarea')) {
            table.page('previous').draw('page');
            return false;
        }
    });
}

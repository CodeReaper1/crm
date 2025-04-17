/**
 * Button Fix Script
 * 
 * This script ensures that the move buttons are properly enabled when a row is selected.
 */

$(document).ready(function() {
    // Check if there's a selected lead ID in localStorage
    const savedLeadId = localStorage.getItem('selectedLeadId');
    if (savedLeadId) {
        console.log('Found saved lead ID:', savedLeadId);
        
        // Enable all move buttons
        $('.move-business').removeClass('disabled').prop('disabled', false);
        
        // Add a visual indicator that buttons are enabled
        $('.move-business').css({
            'opacity': '1',
            'cursor': 'pointer'
        });
        
        // Add a class to the body to indicate that a lead is selected
        $('body').addClass('lead-selected');
    }
    
    // Add a click handler to all table rows
    $('table tbody').on('click', 'tr', function() {
        console.log('Row clicked');
        
        // Enable all move buttons
        $('.move-business').removeClass('disabled').prop('disabled', false);
        
        // Add a visual indicator that buttons are enabled
        $('.move-business').css({
            'opacity': '1',
            'cursor': 'pointer'
        });
        
        // Add a class to the body to indicate that a lead is selected
        $('body').addClass('lead-selected');
    });
    
    // Add a click handler to all move buttons
    $('.move-business').on('click', function() {
        console.log('Move button clicked:', $(this).data('from'), 'to', $(this).data('to'));
    });
});

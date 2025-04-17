/**
 * Dummy Data Manager
 * 
 * This file contains functions for managing dummy data in the CRM system.
 * It allows adding, removing, and moving dummy data between different sections.
 */

$(function() {
    // Initialize dummy data settings from localStorage
    const dummyDataSettings = JSON.parse(localStorage.getItem('dummyDataSettings')) || {
        callStack: true,
        coldLeads: true,
        warmLeads: true,
        currentlyWorkingWith: true
    };

    // Update checkboxes based on stored settings
    updateDummyDataCheckboxes();

    // Toggle dummy data visibility based on settings
    toggleDummyDataVisibility();

    // Handle dummy data toggle
    $('.toggle-dummy-data').on('change', function() {
        const section = $(this).data('section');
        dummyDataSettings[section] = $(this).prop('checked');
        
        // Save settings to localStorage
        localStorage.setItem('dummyDataSettings', JSON.stringify(dummyDataSettings));
        
        // Refresh the page to apply changes
        location.reload();
    });

    // Handle moving dummy data between sections
    $('.move-dummy-data').on('click', function(e) {
        e.preventDefault();
        const fromSection = $(this).data('from');
        const toSection = $(this).data('to');
        
        // Show confirmation dialog
        if (confirm(`Are you sure you want to move a record from ${formatSectionName(fromSection)} to ${formatSectionName(toSection)}?`)) {
            moveDummyData(fromSection, toSection);
        }
    });

    /**
     * Update dummy data checkboxes based on stored settings
     */
    function updateDummyDataCheckboxes() {
        for (const section in dummyDataSettings) {
            $(`.toggle-dummy-data[data-section="${section}"]`).prop('checked', dummyDataSettings[section]);
        }
    }

    /**
     * Toggle dummy data visibility based on settings
     */
    function toggleDummyDataVisibility() {
        // Get current page
        const currentPage = getCurrentPage();
        
        // If dummy data should be hidden for this page
        if (currentPage && !dummyDataSettings[currentPage]) {
            // Hide dummy data rows and show a message
            $('.dummy-data-row').hide();
            
            // If there are no real data rows, show a message
            if ($('tbody tr:not(.dummy-data-row)').length === 0) {
                $('tbody').append('<tr><td colspan="6" class="text-center">No data available</td></tr>');
            }
        }
    }

    /**
     * Get the current page section identifier
     */
    function getCurrentPage() {
        const path = window.location.pathname;
        
        if (path.includes('page-call-stack.php')) {
            return 'callStack';
        } else if (path.includes('page-cold-leads.php')) {
            return 'coldLeads';
        } else if (path.includes('page-warm-leads.php')) {
            return 'warmLeads';
        } else if (path.includes('currently-working-with.php')) {
            return 'currentlyWorkingWith';
        }
        
        return null;
    }

    /**
     * Format section name for display
     */
    function formatSectionName(section) {
        switch(section) {
            case 'callStack': return 'Call Stack';
            case 'coldLeads': return 'Cold Leads';
            case 'warmLeads': return 'Warm Leads';
            case 'currentlyWorkingWith': return 'Currently Working With';
            default: return section;
        }
    }

    /**
     * Move dummy data between sections
     */
    function moveDummyData(fromSection, toSection) {
        // In a real implementation, this would make an AJAX call to the server
        // For this demo, we'll simulate the move with localStorage
        
        // Get the dummy data for both sections
        let dummyData = JSON.parse(localStorage.getItem('dummyData')) || {
            callStack: [],
            coldLeads: [],
            warmLeads: [],
            currentlyWorkingWith: []
        };
        
        // If there's no data in the source section, initialize with default data
        if (!dummyData[fromSection] || dummyData[fromSection].length === 0) {
            dummyData[fromSection] = getDefaultDummyData(fromSection);
        }
        
        // If there's no data in the target section, initialize it
        if (!dummyData[toSection]) {
            dummyData[toSection] = [];
        }
        
        // Move one record from source to target
        if (dummyData[fromSection].length > 0) {
            const record = dummyData[fromSection].shift();
            dummyData[toSection].push(record);
            
            // Save updated data
            localStorage.setItem('dummyData', JSON.stringify(dummyData));
            
            // Show success message
            alert(`Successfully moved a record from ${formatSectionName(fromSection)} to ${formatSectionName(toSection)}`);
            
            // Refresh the page to show changes
            location.reload();
        } else {
            alert(`No records available in ${formatSectionName(fromSection)} to move`);
        }
    }

    /**
     * Get default dummy data for a section
     */
    function getDefaultDummyData(section) {
        // This would typically come from the server
        // For this demo, we'll return some sample data
        switch(section) {
            case 'callStack':
                return [
                    { niche: 'Digital Marketing', company: 'MarketBoost Inc.', email: 'info@marketboost.com', phone: '555-111-2222', website: 'marketboost.com', notes: 'Needs social media strategy' }
                ];
            case 'coldLeads':
                return [
                    { niche: 'Web Development', company: 'TechSphere Inc.', email: 'info@techsphere.com', phone: '555-234-5678', website: 'techsphere.com', notes: 'Looking for e-commerce solution' }
                ];
            case 'warmLeads':
                return [
                    { niche: 'Graphic Design', company: 'Creative Minds Studio', email: 'hello@creativeminds.com', phone: '555-345-6789', website: 'creativeminds.com', notes: 'Needs new brand identity' }
                ];
            case 'currentlyWorkingWith':
                return [
                    { niche: 'Content Marketing', company: 'WordCraft Media', email: 'inquiries@wordcraft.com', phone: '555-456-7890', website: 'wordcraft.com', notes: 'Blog management project' }
                ];
            default:
                return [];
        }
    }

    // Add class to dummy data rows for identification
    // This would typically be done server-side, but for this demo we'll do it client-side
    if (!$('tbody tr').hasClass('real-data-row')) {
        $('tbody tr').addClass('dummy-data-row');
    }
});

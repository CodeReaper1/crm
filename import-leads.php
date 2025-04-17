<?php

require_once 'functions.php';

/**
 * Imports leads from a JSON file into the database.
 *
 * @param string $json_file Path to the JSON file
 * @return int Number of leads imported
 */
function importLeadsFromJson($json_file) {
    if (!file_exists($json_file)) {
        echo "File not found: $json_file\n";
        return 0;
    }

    $json_content = file_get_contents($json_file);
    $leads = json_decode($json_content, true);

    if (!is_array($leads)) {
        echo "Invalid JSON format\n";
        return 0;
    }

    $imported_count = 0;
    foreach ($leads as $lead) {
        // Prepare the lead data with default values
        $lead_data = [
            'business_name' => !empty($lead['business name']) ? $lead['business name'] : 'no business name',
            'niche' => !empty($lead['niche']) ? $lead['niche'] : 'noValue',
            'base_url' => !empty($lead['base-url']) ? $lead['base-url'] : 'noValue',
            'image' => !empty($lead['image']) ? $lead['image'] : 'no image',
            'email' => !empty($lead['email']) ? $lead['email'] : 'no email',
            'phones' => !empty($lead['phones']) ? $lead['phones'] : ['no number'],
            'business_description' => !empty($lead['business-description']) ? $lead['business-description'] : 'noValue',
            'website' => !empty($lead['website']) ? $lead['website'] : 'no website',
            'notes' => !empty($lead['notes']) ? $lead['notes'] : 'noValue',
            'status' => 'new' // Default status
        ];

        // Add the lead to the database
        if (addLead($lead_data)) {
            $imported_count++;
            echo "Imported lead: " . $lead_data['business_name'] . "\n";
        } else {
            echo "Failed to import lead: " . $lead_data['business_name'] . "\n";
        }
    }

    return $imported_count;
}

// Run the import
importLeadsFromJson('D:\xampp\htdocs\crm\combined_businesses.json');
?>
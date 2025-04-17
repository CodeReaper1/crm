<?php
/**
 * Optimize Large Dataset Pagination
 * 
 * This script analyzes and recommends optimal pagination settings for 26,071 leads.
 */

// Start HTML output
echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Optimize Large Dataset Pagination</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
        .alert { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeeba; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .highlight { background-color: #ffffcc; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Optimize Large Dataset Pagination</h1>
        
        <div class='card'>
            <h2>Dataset Statistics</h2>
            <div class='alert alert-info'>
                <p>Total leads in database: <strong>26,071</strong></p>
                <p>All leads are currently in the Call Stack category.</p>
            </div>
            
            <div class='alert alert-warning'>
                <h3>Large Dataset Considerations</h3>
                <p>With over 26,000 leads, special consideration must be given to pagination and performance:</p>
                <ul>
                    <li>Server-side processing is essential to avoid loading all records at once</li>
                    <li>Larger page sizes reduce the total number of pages but increase load time per page</li>
                    <li>Search and filtering become critical for usability</li>
                </ul>
            </div>
        </div>
        
        <div class='card'>
            <h2>Pagination Analysis</h2>
            <p>Here's how many pages would be needed with different page sizes:</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Page Size</th>
                        <th>Number of Pages</th>
                        <th>Load Time Impact</th>
                        <th>Usability</th>
                    </tr>
                </thead>
                <tbody>";

// Calculate pages for different page sizes
$total_leads = 26071;
$page_sizes = [50, 100, 250, 500, 1000];

foreach ($page_sizes as $size) {
    $pages = ceil($total_leads / $size);
    $highlight = '';
    $load_time = '';
    $usability = '';
    
    // Add notes based on page size
    if ($size <= 50) {
        $load_time = 'Fast';
        $usability = 'Many pages (' . $pages . ') to navigate';
    } elseif ($size <= 100) {
        $load_time = 'Good';
        $usability = 'Good balance of page count and performance';
        $highlight = 'highlight';
    } elseif ($size <= 250) {
        $load_time = 'Moderate';
        $usability = 'Fewer pages, moderate scrolling';
        $highlight = 'highlight';
    } elseif ($size <= 500) {
        $load_time = 'Slower';
        $usability = 'Much less paging, more scrolling';
    } else {
        $load_time = 'Slow';
        $usability = 'Minimal paging, extensive scrolling';
    }
    
    echo "<tr class='$highlight'>
            <td>$size</td>
            <td>$pages</td>
            <td>$load_time</td>
            <td>$usability</td>
          </tr>";
}

echo "      </tbody>
            </table>
            
            <div class='alert alert-success'>
                <h3>Recommendation</h3>
                <p>Based on the analysis, the optimal page size for 26,071 leads is <strong>100-250 leads per page</strong>.</p>
                <ul>
                    <li>With 100 leads per page: 261 pages total</li>
                    <li>With 250 leads per page: 105 pages total</li>
                </ul>
                <p>This provides a reasonable balance between performance and usability.</p>
            </div>
        </div>
        
        <div class='card'>
            <h2>Additional Recommendations</h2>
            <ol>
                <li><strong>Implement robust search functionality</strong> - With this many leads, users will need to search rather than browse</li>
                <li><strong>Add filtering options</strong> - Allow users to filter by various criteria (e.g., niche, location)</li>
                <li><strong>Consider data segmentation</strong> - You may want to segment the Call Stack into subcategories</li>
                <li><strong>Optimize database queries</strong> - Ensure proper indexing on frequently searched/sorted columns</li>
                <li><strong>Implement lazy loading</strong> - Use techniques like deferRender in DataTables to improve performance</li>
                <li><strong>Consider pagination alternatives</strong> - Infinite scrolling or 'load more' buttons may be more user-friendly than traditional pagination for some users</li>
            </ol>
        </div>
        
        <p><a href='page-call-stack.php'>Go to Call Stack</a></p>
        <p><a href='index.php'>Back to Dashboard</a></p>
    </div>
</body>
</html>";
?>

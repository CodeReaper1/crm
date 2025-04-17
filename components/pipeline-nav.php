<?php
// Get current page to highlight the active tab
$current_page = basename($_SERVER['PHP_SELF']);

// Get counts for each category
function getCategoryCount($category) {
    global $conn;
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    if (defined('USE_DATABASE') && USE_DATABASE) {
        $conn = connectDB();
        if (!$conn) return 0;

        // For Call Stack, count all leads
        if ($category === 'callStack') {
            $sql = "SELECT COUNT(*) as count FROM leads WHERE category = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $category);
        }
        // For other categories, only count leads assigned to the current user
        else if ($user_id !== null) {
            $sql = "SELECT COUNT(*) as count FROM leads WHERE category = ? AND assigned_to = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $category, $user_id);
        }
        // If no user is logged in, return 0 for non-callStack categories
        else {
            return 0;
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'];

        $stmt->close();
        return $count;
    } else {
        // For static data mode, use the updated getCombinedBusinesses function
        // which now handles user-specific filtering
        $result = getCombinedBusinesses($category, PHP_INT_MAX, 0, '', 'business_name', 'ASC', $user_id);
        return $result['total'];
    }
}

// Get counts
$callstack_count = getCategoryCount('callStack');
$coldleads_count = getCategoryCount('coldLeads');
$warmleads_count = getCategoryCount('warmLeads');
$currentlyworkingwith_count = getCategoryCount('currentlyWorkingWith');
?>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Sales Pipeline Navigation</h5>
        <div class="pipeline-nav mt-3">
            <ul class="nav nav-pills nav-fill">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'page-call-stack.php') ? 'active' : ''; ?>" href="page-call-stack.php">
                        <i class="bx bx-phone-call"></i> Call Stack
                        <span class="badge bg-primary"><?php echo $callstack_count; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'page-cold-leads.php') ? 'active' : ''; ?>" href="page-cold-leads.php">
                        <i class="bx bx-snowflake"></i> Cold Leads
                        <span class="badge bg-info"><?php echo $coldleads_count; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'page-warm-leads.php') ? 'active' : ''; ?>" href="page-warm-leads.php">
                        <i class="bx bx-fire"></i> Warm Leads
                        <span class="badge bg-warning"><?php echo $warmleads_count; ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'currently-working-with.php') ? 'active' : ''; ?>" href="currently-working-with.php">
                        <i class="bx bx-check-circle"></i> Currently Working With
                        <span class="badge bg-success"><?php echo $currentlyworkingwith_count; ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
.pipeline-nav .nav-link {
    padding: 10px 15px;
    border-radius: 5px;
    margin: 0 5px;
    transition: all 0.3s ease;
}

.pipeline-nav .nav-link:not(.active):hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
}

.pipeline-nav .nav-link.active {
    font-weight: bold;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.pipeline-nav i {
    margin-right: 5px;
}

.pipeline-nav .badge {
    margin-left: 5px;
}
</style>

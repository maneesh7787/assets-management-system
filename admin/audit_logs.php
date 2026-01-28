<?php
/**
 * Admin - Audit Logs
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require admin role
requireRole(1);

$pageTitle = 'Audit Logs';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Filters
$filterUser = isset($_GET['user']) ? sanitizeInput($_GET['user']) : '';
$filterAction = isset($_GET['action']) ? sanitizeInput($_GET['action']) : '';
$filterDate = isset($_GET['date']) ? sanitizeInput($_GET['date']) : '';

// Build query
$whereConditions = [];
if ($filterUser) {
    $whereConditions[] = "u.username LIKE '%$filterUser%'";
}
if ($filterAction) {
    $whereConditions[] = "al.action LIKE '%$filterAction%'";
}
if ($filterDate) {
    $whereConditions[] = "DATE(al.created_at) = '$filterDate'";
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get audit logs
$logsQuery = "SELECT al.*, u.username 
              FROM audit_logs al 
              LEFT JOIN users u ON al.user_id = u.user_id 
              $whereClause
              ORDER BY al.created_at DESC 
              LIMIT $perPage OFFSET $offset";
$logs = fetchAll(executeQuery($logsQuery));

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM audit_logs al LEFT JOIN users u ON al.user_id = u.user_id $whereClause";
$totalResult = fetchRow(executeQuery($countQuery));
$totalLogs = $totalResult['total'];
$totalPages = ceil($totalLogs / $perPage);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-clock-history"></i> Audit Logs</h2>
    <span class="badge bg-primary fs-6"><?php echo $totalLogs; ?> Total Entries</span>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-funnel"></i> Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="user" value="<?php echo htmlspecialchars($filterUser); ?>" placeholder="Search username">
            </div>
            <div class="col-md-3">
                <label class="form-label">Action</label>
                <input type="text" class="form-control" name="action" value="<?php echo htmlspecialchars($filterAction); ?>" placeholder="Search action">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars($filterDate); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="audit_logs.php" class="btn btn-secondary">
                        <i class="bi bi-x"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Audit Logs Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Activity Log</h5>
    </div>
    <div class="card-body">
        <?php if (empty($logs)): ?>
            <p class="text-muted text-center py-3">No audit logs found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Table</th>
                            <th>Record ID</th>
                            <th>Details</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo formatDateTime($log['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($log['action']); ?></span></td>
                            <td><?php echo htmlspecialchars($log['table_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($log['record_id'] ?? '-'); ?></td>
                            <td>
                                <?php if ($log['new_value']): ?>
                                    <small><?php echo htmlspecialchars(substr($log['new_value'], 0, 50)); ?><?php echo strlen($log['new_value']) > 50 ? '...' : ''; ?></small>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><small><?php echo htmlspecialchars($log['ip_address']); ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="mt-3">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $filterUser ? '&user=' . urlencode($filterUser) : ''; ?><?php echo $filterAction ? '&action=' . urlencode($filterAction) : ''; ?><?php echo $filterDate ? '&date=' . urlencode($filterDate) : ''; ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $filterUser ? '&user=' . urlencode($filterUser) : ''; ?><?php echo $filterAction ? '&action=' . urlencode($filterAction) : ''; ?><?php echo $filterDate ? '&date=' . urlencode($filterDate) : ''; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $filterUser ? '&user=' . urlencode($filterUser) : ''; ?><?php echo $filterAction ? '&action=' . urlencode($filterAction) : ''; ?><?php echo $filterDate ? '&date=' . urlencode($filterDate) : ''; ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>

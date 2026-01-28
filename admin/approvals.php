<?php
/**
 * Admin - Asset Approvals
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require admin role
requireRole(1);

$pageTitle = 'Asset Approvals';
$error = '';
$success = '';

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assignmentId = sanitizeInput($_POST['assignment_id']);
    $action = sanitizeInput($_POST['action']);
    $comments = sanitizeInput($_POST['comments'] ?? '');
    
    if ($action == 'approve' || $action == 'reject') {
        $newStatus = $action == 'approve' ? 'Approved' : 'Rejected';
        $approverId = getCurrentUserId();
        
        // Update assignment status
        $updateQuery = "UPDATE asset_assignments SET 
                        assignment_status = '$newStatus',
                        approved_by = '$approverId',
                        approval_date = NOW(),
                        remarks = '$comments'
                        WHERE assignment_id = '$assignmentId'";
        
        if (executeQuery($updateQuery)) {
            // Get assignment details
            $assignmentQuery = "SELECT * FROM asset_assignments WHERE assignment_id = '$assignmentId'";
            $assignment = fetchRow(executeQuery($assignmentQuery));
            
            if ($action == 'approve') {
                // Update asset status to Assigned
                $assetId = $assignment['asset_id'];
                $updateAssetQuery = "UPDATE assets SET current_status = 'Assigned' WHERE asset_id = '$assetId'";
                executeQuery($updateAssetQuery);
                
                // Add to asset history
                $employeeId = $assignment['employee_id'];
                $historyQuery = "INSERT INTO asset_history (asset_id, employee_id, action_type, action_date, performed_by, old_status, new_status, remarks) 
                                 VALUES ('$assetId', '$employeeId', 'Assigned', NOW(), '$approverId', 'Pending', 'Assigned', '$comments')";
                executeQuery($historyQuery);
            }
            
            logAudit('Asset ' . ucfirst($action) . 'd', 'asset_assignments', $assignmentId, 'Pending', $newStatus);
            $success = "Asset " . $action . "d successfully.";
        } else {
            $error = "Failed to $action asset.";
        }
    }
}

// Get pending approvals
$pendingQuery = "SELECT aa.*, e.full_name, e.emp_code, e.department, 
                 a.asset_type, a.brand, a.serial_number, a.condition_at_issue,
                 u.username as submitted_by_username
                 FROM asset_assignments aa
                 INNER JOIN employees e ON aa.employee_id = e.employee_id
                 INNER JOIN assets a ON aa.asset_id = a.asset_id
                 LEFT JOIN users u ON aa.submitted_by = u.user_id
                 WHERE aa.assignment_status = 'Pending'
                 ORDER BY aa.created_at ASC";
$pendingApprovals = fetchAll(executeQuery($pendingQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-check-circle"></i> Pending Approvals</h2>
    <span class="badge bg-warning fs-5"><?php echo count($pendingApprovals); ?> Pending</span>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($pendingApprovals)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
            <h4 class="mt-3">No Pending Approvals</h4>
            <p class="text-muted">All asset assignments have been reviewed.</p>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($pendingApprovals as $approval): ?>
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history"></i> Assignment #<?php echo $approval['assignment_id']; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="card-title"><i class="bi bi-person"></i> Employee Details</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Name:</strong></td>
                            <td><?php echo htmlspecialchars($approval['full_name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Employee Code:</strong></td>
                            <td><?php echo htmlspecialchars($approval['emp_code']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Department:</strong></td>
                            <td><?php echo htmlspecialchars($approval['department']); ?></td>
                        </tr>
                    </table>
                    
                    <hr>
                    
                    <h6 class="card-title"><i class="bi bi-laptop"></i> Asset Details</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Asset Type:</strong></td>
                            <td><?php echo htmlspecialchars($approval['asset_type']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Brand:</strong></td>
                            <td><?php echo htmlspecialchars($approval['brand']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Serial Number:</strong></td>
                            <td><?php echo htmlspecialchars($approval['serial_number']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Condition:</strong></td>
                            <td><?php echo htmlspecialchars($approval['condition_at_issue']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Date of Issue:</strong></td>
                            <td><?php echo formatDate($approval['assigned_date']); ?></td>
                        </tr>
                    </table>
                    
                    <hr>
                    
                    <p class="mb-2">
                        <strong>Submitted by:</strong> <?php echo htmlspecialchars($approval['submitted_by_username']); ?><br>
                        <strong>Submitted on:</strong> <?php echo formatDateTime($approval['created_at']); ?>
                    </p>
                    
                    <?php if ($approval['declaration_accepted']): ?>
                        <div class="alert alert-success mb-3">
                            <i class="bi bi-check-circle"></i> Employee has accepted the declaration
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-light">
                    <button class="btn btn-success btn-sm" onclick="showApprovalModal(<?php echo $approval['assignment_id']; ?>, 'approve')">
                        <i class="bi bi-check-circle"></i> Approve
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="showApprovalModal(<?php echo $approval['assignment_id']; ?>, 'reject')">
                        <i class="bi bi-x-circle"></i> Reject
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Approval/Rejection Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="assignment_id" id="modal_assignment_id">
                <input type="hidden" name="action" id="modal_action">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comments" class="form-label">Comments (Optional)</label>
                        <textarea class="form-control" name="comments" id="comments" rows="3"></textarea>
                    </div>
                    <p class="text-muted" id="confirmText"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="confirmBtn"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showApprovalModal(assignmentId, action) {
    document.getElementById('modal_assignment_id').value = assignmentId;
    document.getElementById('modal_action').value = action;
    
    if (action === 'approve') {
        document.getElementById('approvalModalTitle').innerHTML = '<i class="bi bi-check-circle"></i> Approve Asset Assignment';
        document.getElementById('confirmText').innerHTML = 'Are you sure you want to approve this asset assignment? The asset will be marked as assigned to the employee.';
        document.getElementById('confirmBtn').className = 'btn btn-success';
        document.getElementById('confirmBtn').innerHTML = '<i class="bi bi-check-circle"></i> Approve';
    } else {
        document.getElementById('approvalModalTitle').innerHTML = '<i class="bi bi-x-circle"></i> Reject Asset Assignment';
        document.getElementById('confirmText').innerHTML = 'Are you sure you want to reject this asset assignment? Please provide a reason in the comments.';
        document.getElementById('confirmBtn').className = 'btn btn-danger';
        document.getElementById('confirmBtn').innerHTML = '<i class="bi bi-x-circle"></i> Reject';
    }
    
    var modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    modal.show();
}
</script>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>

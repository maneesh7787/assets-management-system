<?php
/**
 * Admin - Asset Transfers
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require admin role
requireRole(1);

$pageTitle = 'Asset Transfers';
$error = '';
$success = '';

// Handle transfer submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transfer'])) {
    $assetId = sanitizeInput($_POST['asset_id']);
    $fromEmployeeId = sanitizeInput($_POST['from_employee']);
    $toEmployeeId = sanitizeInput($_POST['to_employee']);
    $transferDate = sanitizeInput($_POST['transfer_date']);
    $remarks = sanitizeInput($_POST['remarks']);
    
    if (empty($assetId) || empty($fromEmployeeId) || empty($toEmployeeId)) {
        $error = 'All fields are required.';
    } elseif ($fromEmployeeId == $toEmployeeId) {
        $error = 'Cannot transfer to the same employee.';
    } else {
        $performedBy = getCurrentUserId();
        
        // Mark old assignment as returned
        $returnQuery = "UPDATE asset_assignments 
                        SET returned_date = '$transferDate', assignment_status = 'Returned'
                        WHERE asset_id = '$assetId' AND employee_id = '$fromEmployeeId' AND assignment_status = 'Approved'";
        
        if (executeQuery($returnQuery)) {
            // Create new assignment
            $newAssignmentQuery = "INSERT INTO asset_assignments (asset_id, employee_id, assigned_date, assignment_status, submitted_by, approved_by, approval_date, remarks, declaration_accepted)
                                   VALUES ('$assetId', '$toEmployeeId', '$transferDate', 'Approved', '$performedBy', '$performedBy', NOW(), '$remarks', 1)";
            
            if (executeQuery($newAssignmentQuery)) {
                // Add to history
                $historyQuery = "INSERT INTO asset_history (asset_id, employee_id, action_type, action_date, performed_by, old_status, new_status, remarks)
                                 VALUES ('$assetId', '$toEmployeeId', 'Transferred', NOW(), '$performedBy', 'Assigned', 'Assigned', '$remarks')";
                executeQuery($historyQuery);
                
                logAudit('Asset Transferred', 'asset_assignments', $assetId, "From Employee: $fromEmployeeId", "To Employee: $toEmployeeId");
                $success = 'Asset transferred successfully.';
            } else {
                $error = 'Failed to create new assignment.';
            }
        } else {
            $error = 'Failed to return old assignment.';
        }
    }
}

// Get all assigned assets
$assignedAssetsQuery = "SELECT a.asset_id, a.asset_type, a.brand, a.serial_number, 
                        e.employee_id, e.full_name, e.emp_code, aa.assigned_date
                        FROM assets a
                        INNER JOIN asset_assignments aa ON a.asset_id = aa.asset_id
                        INNER JOIN employees e ON aa.employee_id = e.employee_id
                        WHERE aa.assignment_status = 'Approved' AND aa.returned_date IS NULL
                        ORDER BY e.full_name ASC";
$assignedAssets = fetchAll(executeQuery($assignedAssetsQuery));

// Get all active employees
$employeesQuery = "SELECT employee_id, emp_code, full_name, department FROM employees WHERE status = 'Active' ORDER BY full_name ASC";
$employees = fetchAll(executeQuery($employeesQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-arrow-left-right"></i> Asset Transfers</h2>
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

<!-- Transfer Form -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Transfer Asset</h5>
    </div>
    <div class="card-body">
        <form method="POST" data-validate="true">
            <input type="hidden" name="transfer" value="1">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Select Asset *</label>
                    <select class="form-select" name="asset_id" id="asset_select" required>
                        <option value="">Choose an asset...</option>
                        <?php foreach ($assignedAssets as $asset): ?>
                        <option value="<?php echo $asset['asset_id']; ?>" data-employee="<?php echo $asset['employee_id']; ?>">
                            <?php echo htmlspecialchars($asset['asset_type']); ?> - 
                            <?php echo htmlspecialchars($asset['serial_number']); ?> 
                            (Currently with: <?php echo htmlspecialchars($asset['full_name']); ?>)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Transfer Date *</label>
                    <input type="date" class="form-control" name="transfer_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">From Employee *</label>
                    <input type="text" class="form-control" id="from_employee_display" readonly>
                    <input type="hidden" name="from_employee" id="from_employee">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">To Employee *</label>
                    <select class="form-select" name="to_employee" required>
                        <option value="">Choose an employee...</option>
                        <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo $emp['employee_id']; ?>">
                            <?php echo htmlspecialchars($emp['full_name']); ?> (<?php echo htmlspecialchars($emp['emp_code']); ?>) - <?php echo htmlspecialchars($emp['department']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12 mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" name="remarks" rows="3" placeholder="Reason for transfer..."></textarea>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-left-right"></i> Transfer Asset
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Currently Assigned Assets -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Currently Assigned Assets</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Asset Type</th>
                        <th>Brand</th>
                        <th>Serial Number</th>
                        <th>Assigned To</th>
                        <th>Employee Code</th>
                        <th>Assigned Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assignedAssets as $asset): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($asset['asset_type']); ?></td>
                        <td><?php echo htmlspecialchars($asset['brand']); ?></td>
                        <td><code><?php echo htmlspecialchars($asset['serial_number']); ?></code></td>
                        <td><?php echo htmlspecialchars($asset['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($asset['emp_code']); ?></td>
                        <td><?php echo formatDate($asset['assigned_date']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#asset_select').on('change', function() {
        var selectedOption = $(this).find(':selected');
        var employeeId = selectedOption.data('employee');
        var employeeText = selectedOption.text();
        
        if (employeeId) {
            $('#from_employee').val(employeeId);
            var match = employeeText.match(/Currently with: (.+)\)/);
            $('#from_employee_display').val(match ? match[1] : '');
        } else {
            $('#from_employee').val('');
            $('#from_employee_display').val('');
        }
    });
});
</script>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>

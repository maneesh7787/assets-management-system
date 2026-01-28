<?php
/**
 * Employee - Submit Assets
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require employee role
requireRole(3);

$pageTitle = 'Submit Assets';
$error = '';
$success = '';

$employeeId = getCurrentEmployeeId();
$employee = getEmployeeById($employeeId);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assetTypes = $_POST['asset_type'] ?? [];
    $brands = $_POST['brand'] ?? [];
    $serialNumbers = $_POST['serial_number'] ?? [];
    $datesOfIssue = $_POST['date_of_issue'] ?? [];
    $conditions = $_POST['condition_at_issue'] ?? [];
    $declaration = isset($_POST['declaration']) ? 1 : 0;
    
    if (empty($assetTypes)) {
        $error = 'Please add at least one asset.';
    } elseif (!$declaration) {
        $error = 'Please accept the declaration to proceed.';
    } else {
        $successCount = 0;
        $errorCount = 0;
        $duplicates = [];
        
        for ($i = 0; $i < count($assetTypes); $i++) {
            $assetType = sanitizeInput($assetTypes[$i]);
            $brand = sanitizeInput($brands[$i]);
            $serialNumber = sanitizeInput($serialNumbers[$i]);
            $dateOfIssue = sanitizeInput($datesOfIssue[$i]);
            $condition = sanitizeInput($conditions[$i]);
            
            if (empty($assetType) || empty($serialNumber)) {
                $errorCount++;
                continue;
            }
            
            // Check if serial number already exists
            if (recordExists('assets', 'serial_number', $serialNumber)) {
                $duplicates[] = $serialNumber;
                $errorCount++;
                continue;
            }
            
            // Create asset
            $assetQuery = "INSERT INTO assets (asset_type, brand, serial_number, date_of_issue, condition_at_issue, current_status) 
                           VALUES ('$assetType', '$brand', '$serialNumber', '$dateOfIssue', '$condition', 'Available')";
            
            if (executeQuery($assetQuery)) {
                $assetId = lastInsertId();
                
                // Create assignment request
                $submittedBy = getCurrentUserId();
                $assignmentQuery = "INSERT INTO asset_assignments (asset_id, employee_id, assigned_date, assignment_status, submitted_by, declaration_accepted) 
                                    VALUES ('$assetId', '$employeeId', '$dateOfIssue', 'Pending', '$submittedBy', '$declaration')";
                
                if (executeQuery($assignmentQuery)) {
                    $assignmentId = lastInsertId();
                    
                    // Log asset creation
                    logAudit('Asset Submitted for Approval', 'asset_assignments', $assignmentId, null, "Serial: $serialNumber");
                    
                    $successCount++;
                } else {
                    $errorCount++;
                }
            } else {
                $errorCount++;
            }
        }
        
        if ($successCount > 0) {
            $success = "$successCount asset(s) submitted successfully for approval.";
        }
        
        if ($errorCount > 0) {
            $error .= " $errorCount asset(s) failed to submit.";
        }
        
        if (!empty($duplicates)) {
            $error .= " Duplicate serial numbers: " . implode(', ', $duplicates);
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-plus-circle"></i> Submit Assets</h2>
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
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

<!-- Employee Details (Read-only) -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-badge"></i> Employee Details</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="40%"><strong>Employee Name:</strong></td>
                        <td><?php echo htmlspecialchars($employee['full_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Employee ID:</strong></td>
                        <td><?php echo htmlspecialchars($employee['emp_code']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Department:</strong></td>
                        <td><?php echo htmlspecialchars($employee['department']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Designation:</strong></td>
                        <td><?php echo htmlspecialchars($employee['designation']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td width="40%"><strong>Work Location:</strong></td>
                        <td><?php echo htmlspecialchars($employee['work_location']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Date of Joining:</strong></td>
                        <td><?php echo formatDate($employee['date_of_joining']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo htmlspecialchars($employee['email']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td><?php echo htmlspecialchars($employee['phone']); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Asset Submission Form -->
<form method="POST" data-validate="true" id="assetForm">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-laptop"></i> Asset Details</h5>
            <button type="button" class="btn btn-sm btn-primary" id="addAssetRow">
                <i class="bi bi-plus"></i> Add Asset
            </button>
        </div>
        <div class="card-body">
            <div class="asset-container">
                <!-- First Asset Row -->
                <div class="asset-row border p-3 mb-3 rounded">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Asset Type *</label>
                            <select class="form-select" name="asset_type[]" required>
                                <option value="">Select Type</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Charger">Charger</option>
                                <option value="Mouse">Mouse</option>
                                <option value="Keyboard">Keyboard</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Headset">Headset</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Brand</label>
                            <input type="text" class="form-control" name="brand[]">
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Serial Number *</label>
                            <input type="text" class="form-control" name="serial_number[]" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Date of Issue *</label>
                            <input type="date" class="form-control" name="date_of_issue[]" required>
                        </div>
                        <div class="col-md-3 mb-2">
                            <label class="form-label">Condition *</label>
                            <select class="form-select" name="condition_at_issue[]" required>
                                <option value="">Select Condition</option>
                                <option value="New">New</option>
                                <option value="Good">Good</option>
                                <option value="Fair">Fair</option>
                                <option value="Used">Used</option>
                            </select>
                        </div>
                        <div class="col-md-9 mb-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm remove-asset" style="display: none;">
                                <i class="bi bi-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Declaration -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-check-square"></i> Declaration</h5>
        </div>
        <div class="card-body">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="declaration" id="declaration" required>
                <label class="form-check-label" for="declaration">
                    <strong>I hereby acknowledge and declare that:</strong>
                </label>
            </div>
            <ul class="mt-2">
                <li>I have received the above-mentioned asset(s) in good working condition.</li>
                <li>I am responsible for the safekeeping and proper use of the asset(s).</li>
                <li>I will report any damage, loss, or theft immediately to the IT department.</li>
                <li>I will return the asset(s) in good condition upon request or at the time of exit.</li>
                <li>The information provided above is accurate to the best of my knowledge.</li>
            </ul>
            <div class="mt-3">
                <p><strong>Digital Signature:</strong> <?php echo htmlspecialchars($employee['full_name']); ?></p>
                <p><strong>Date:</strong> <?php echo date('d-m-Y'); ?></p>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="d-grid gap-2 mb-4">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-send"></i> Submit for Approval
        </button>
    </div>
</form>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i> <strong>Note:</strong> 
    <ul class="mb-0 mt-2">
        <li>All submitted assets will be sent for IT/Admin approval.</li>
        <li>You will not be able to edit assets after submission.</li>
        <li>You can track the approval status from your dashboard.</li>
    </ul>
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>

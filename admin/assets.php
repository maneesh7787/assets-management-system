<?php
/**
 * Admin - Manage Assets
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require admin role
requireRole(1);

$pageTitle = 'Manage Assets';
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add') {
        $assetType = sanitizeInput($_POST['asset_type']);
        $brand = sanitizeInput($_POST['brand']);
        $serialNumber = sanitizeInput($_POST['serial_number']);
        $dateOfIssue = sanitizeInput($_POST['date_of_issue']);
        $condition = sanitizeInput($_POST['condition_at_issue']);
        
        if (empty($assetType) || empty($serialNumber)) {
            $error = 'Asset Type and Serial Number are required.';
        } elseif (recordExists('assets', 'serial_number', $serialNumber)) {
            $error = 'Serial Number already exists.';
        } else {
            $query = "INSERT INTO assets (asset_type, brand, serial_number, date_of_issue, condition_at_issue, current_status) 
                      VALUES ('$assetType', '$brand', '$serialNumber', '$dateOfIssue', '$condition', 'Available')";
            
            if (executeQuery($query)) {
                $assetId = lastInsertId();
                logAudit('Asset Created', 'assets', $assetId, null, "Serial: $serialNumber");
                $success = 'Asset added successfully.';
            } else {
                $error = 'Failed to add asset.';
            }
        }
    } elseif ($action == 'edit') {
        $assetId = sanitizeInput($_POST['asset_id']);
        $assetType = sanitizeInput($_POST['asset_type']);
        $brand = sanitizeInput($_POST['brand']);
        $condition = sanitizeInput($_POST['condition_at_issue']);
        $currentStatus = sanitizeInput($_POST['current_status']);
        
        $query = "UPDATE assets SET 
                  asset_type = '$assetType',
                  brand = '$brand',
                  condition_at_issue = '$condition',
                  current_status = '$currentStatus'
                  WHERE asset_id = '$assetId'";
        
        if (executeQuery($query)) {
            logAudit('Asset Updated', 'assets', $assetId, null, "Asset updated");
            $success = 'Asset updated successfully.';
        } else {
            $error = 'Failed to update asset.';
        }
    }
}

// Get all assets
$assetsQuery = "SELECT a.*, 
                (SELECT CONCAT(e.full_name, ' (', e.emp_code, ')') 
                 FROM asset_assignments aa 
                 INNER JOIN employees e ON aa.employee_id = e.employee_id 
                 WHERE aa.asset_id = a.asset_id AND aa.assignment_status = 'Approved' AND aa.returned_date IS NULL 
                 LIMIT 1) as assigned_to
                FROM assets a 
                ORDER BY a.created_at DESC";
$assets = fetchAll(executeQuery($assetsQuery));

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-laptop"></i> Manage Assets</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAssetModal">
        <i class="bi bi-plus-circle"></i> Add Asset
    </button>
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

<!-- Assets Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Assets</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Asset Type</th>
                        <th>Brand</th>
                        <th>Serial Number</th>
                        <th>Condition</th>
                        <th>Date of Issue</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assets as $asset): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($asset['asset_type']); ?></td>
                        <td><?php echo htmlspecialchars($asset['brand']); ?></td>
                        <td><code><?php echo htmlspecialchars($asset['serial_number']); ?></code></td>
                        <td><?php echo htmlspecialchars($asset['condition_at_issue']); ?></td>
                        <td><?php echo formatDate($asset['date_of_issue']); ?></td>
                        <td>
                            <span class="badge <?php echo getStatusBadgeClass($asset['current_status']); ?>">
                                <?php echo $asset['current_status']; ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($asset['assigned_to'] ?? 'Not Assigned'); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="viewAsset(<?php echo $asset['asset_id']; ?>)">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="editAsset(<?php echo $asset['asset_id']; ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Asset Modal -->
<div class="modal fade" id="addAssetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Add Asset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" data-validate="true">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="asset_type" class="form-label">Asset Type *</label>
                        <select class="form-select" name="asset_type" required>
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
                    <div class="mb-3">
                        <label for="brand" class="form-label">Brand</label>
                        <input type="text" class="form-control" name="brand">
                    </div>
                    <div class="mb-3">
                        <label for="serial_number" class="form-label">Serial Number *</label>
                        <input type="text" class="form-control" name="serial_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_of_issue" class="form-label">Date of Issue</label>
                        <input type="date" class="form-control" name="date_of_issue">
                    </div>
                    <div class="mb-3">
                        <label for="condition_at_issue" class="form-label">Condition *</label>
                        <select class="form-select" name="condition_at_issue" required>
                            <option value="">Select Condition</option>
                            <option value="New">New</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Used">Used</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewAsset(id) {
    window.location.href = 'asset_details.php?id=' + id;
}

function editAsset(id) {
    window.location.href = 'edit_asset.php?id=' + id;
}
</script>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>

<?php
/**
 * Admin - Edit Asset
 * Asset Management System
 */

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/session.php';
require_once '../includes/functions.php';

// Require admin role
requireRole(1);

$pageTitle = 'Edit Asset';
$error = '';
$success = '';

// Get asset ID from URL
$assetId = isset($_GET['id']) ? sanitizeInput($_GET['id']) : null;

if (!$assetId) {
    header('Location: assets.php');
    exit;
}

// Fetch asset details
$assetQuery = "SELECT * FROM assets WHERE asset_id = '$assetId'";
$assetResult = executeQuery($assetQuery);
$asset = fetchRow($assetResult);

if (!$asset) {
    header('Location: assets.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assetType = sanitizeInput($_POST['asset_type']);
    $brand = sanitizeInput($_POST['brand']);
    $serialNumber = sanitizeInput($_POST['serial_number']);
    $dateOfIssue = sanitizeInput($_POST['date_of_issue']);
    $condition = sanitizeInput($_POST['condition_at_issue']);
    $currentStatus = sanitizeInput($_POST['current_status']);
    
    // Validation
    if (empty($assetType) || empty($serialNumber)) {
        $error = 'Asset Type and Serial Number are required.';
    } elseif ($serialNumber != $asset['serial_number'] && recordExists('assets', 'serial_number', $serialNumber)) {
        $error = 'Serial Number already exists.';
    } else {
        $query = "UPDATE assets SET 
                  asset_type = '$assetType',
                  brand = '$brand',
                  serial_number = '$serialNumber',
                  date_of_issue = '$dateOfIssue',
                  condition_at_issue = '$condition',
                  current_status = '$currentStatus'
                  WHERE asset_id = '$assetId'";
        
        if (executeQuery($query)) {
            logAudit('Asset Updated', 'assets', $assetId, null, "Serial: $serialNumber");
            $success = 'Asset updated successfully.';
            
            // Refresh asset data
            $assetResult = executeQuery($assetQuery);
            $asset = fetchRow($assetResult);
        } else {
            $error = 'Failed to update asset.';
        }
    }
}

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-pencil-square"></i> Edit Asset</h2>
    <a href="assets.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Assets
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

<!-- Edit Asset Form -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Asset Details</h5>
    </div>
    <div class="card-body">
        <form method="POST" data-validate="true">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="asset_type" class="form-label">Asset Type *</label>
                    <select class="form-select" name="asset_type" required>
                        <option value="">Select Type</option>
                        <option value="Laptop" <?php echo $asset['asset_type'] == 'Laptop' ? 'selected' : ''; ?>>Laptop</option>
                        <option value="Charger" <?php echo $asset['asset_type'] == 'Charger' ? 'selected' : ''; ?>>Charger</option>
                        <option value="Mouse" <?php echo $asset['asset_type'] == 'Mouse' ? 'selected' : ''; ?>>Mouse</option>
                        <option value="Keyboard" <?php echo $asset['asset_type'] == 'Keyboard' ? 'selected' : ''; ?>>Keyboard</option>
                        <option value="Monitor" <?php echo $asset['asset_type'] == 'Monitor' ? 'selected' : ''; ?>>Monitor</option>
                        <option value="Headset" <?php echo $asset['asset_type'] == 'Headset' ? 'selected' : ''; ?>>Headset</option>
                        <option value="Other" <?php echo $asset['asset_type'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="brand" class="form-label">Brand</label>
                    <input type="text" class="form-control" name="brand" value="<?php echo htmlspecialchars($asset['brand']); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="serial_number" class="form-label">Serial Number *</label>
                    <input type="text" class="form-control" name="serial_number" value="<?php echo htmlspecialchars($asset['serial_number']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="date_of_issue" class="form-label">Date of Issue</label>
                    <input type="date" class="form-control" name="date_of_issue" value="<?php echo htmlspecialchars($asset['date_of_issue']); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="condition_at_issue" class="form-label">Condition at Issue *</label>
                    <select class="form-select" name="condition_at_issue" required>
                        <option value="">Select Condition</option>
                        <option value="New" <?php echo $asset['condition_at_issue'] == 'New' ? 'selected' : ''; ?>>New</option>
                        <option value="Good" <?php echo $asset['condition_at_issue'] == 'Good' ? 'selected' : ''; ?>>Good</option>
                        <option value="Fair" <?php echo $asset['condition_at_issue'] == 'Fair' ? 'selected' : ''; ?>>Fair</option>
                        <option value="Used" <?php echo $asset['condition_at_issue'] == 'Used' ? 'selected' : ''; ?>>Used</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="current_status" class="form-label">Current Status *</label>
                    <select class="form-select" name="current_status" required>
                        <option value="">Select Status</option>
                        <option value="Available" <?php echo $asset['current_status'] == 'Available' ? 'selected' : ''; ?>>Available</option>
                        <option value="Assigned" <?php echo $asset['current_status'] == 'Assigned' ? 'selected' : ''; ?>>Assigned</option>
                        <option value="Returned" <?php echo $asset['current_status'] == 'Returned' ? 'selected' : ''; ?>>Returned</option>
                        <option value="Replaced" <?php echo $asset['current_status'] == 'Replaced' ? 'selected' : ''; ?>>Replaced</option>
                        <option value="Damaged" <?php echo $asset['current_status'] == 'Damaged' ? 'selected' : ''; ?>>Damaged</option>
                    </select>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="assets.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Update Asset
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Asset History -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Asset Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Asset ID:</strong> <?php echo htmlspecialchars($asset['asset_id']); ?></p>
                <p><strong>Created At:</strong> <?php echo formatDateTime($asset['created_at']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Last Updated:</strong> <?php echo formatDateTime($asset['updated_at']); ?></p>
            </div>
        </div>
    </div>
</div>

</div> <!-- page-content-wrapper -->
<?php include '../includes/footer.php'; ?>

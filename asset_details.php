<?php
require_once 'config/db.php';

$page_title = "Asset Details";

// Get asset ID from URL parameter
$asset_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch asset details from database
$conn = getDBConnection();
$sql = "SELECT * FROM assets WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $asset_id);
$stmt->execute();
$result = $stmt->get_result();
$asset = $result->fetch_assoc();

// Fetch current assignment information
$assignment_sql = "SELECT aa.*, e.first_name, e.last_name, e.email, e.department 
                   FROM asset_assignments aa 
                   INNER JOIN employees e ON aa.employee_id = e.id 
                   WHERE aa.asset_id = ? AND aa.status = 'active'
                   LIMIT 1";
$assignment_stmt = $conn->prepare($assignment_sql);
$assignment_stmt->bind_param("i", $asset_id);
$assignment_stmt->execute();
$assignment_result = $assignment_stmt->get_result();
$assignment = $assignment_result->fetch_assoc();

// Fetch assignment history
$history_sql = "SELECT aa.*, e.first_name, e.last_name 
                FROM asset_assignments aa 
                INNER JOIN employees e ON aa.employee_id = e.id 
                WHERE aa.asset_id = ?
                ORDER BY aa.assigned_date DESC";
$history_stmt = $conn->prepare($history_sql);
$history_stmt->bind_param("i", $asset_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();

include 'includes/header.php';
?>

<div class="card">
    <h2>Asset Details</h2>
    
    <?php if ($asset): ?>
        <div style="margin-top: 20px;">
            <table style="width: auto;">
                <tr>
                    <th style="background: #f4f4f4; color: #333; width: 200px;">Asset ID:</th>
                    <td><?php echo htmlspecialchars($asset['id']); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Asset Name:</th>
                    <td><?php echo htmlspecialchars($asset['name']); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Asset Type:</th>
                    <td><?php echo htmlspecialchars($asset['type'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Brand/Manufacturer:</th>
                    <td><?php echo htmlspecialchars($asset['brand'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Model:</th>
                    <td><?php echo htmlspecialchars($asset['model'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Serial Number:</th>
                    <td><?php echo htmlspecialchars($asset['serial_number'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Purchase Date:</th>
                    <td><?php echo htmlspecialchars($asset['purchase_date'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Purchase Price:</th>
                    <td><?php echo $asset['purchase_price'] ? '$' . number_format($asset['purchase_price'], 2) : 'N/A'; ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Warranty Expiry:</th>
                    <td><?php echo htmlspecialchars($asset['warranty_expiry'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Status:</th>
                    <td><strong><?php echo htmlspecialchars($asset['status'] ?? 'available'); ?></strong></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Location:</th>
                    <td><?php echo htmlspecialchars($asset['location'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Description:</th>
                    <td><?php echo htmlspecialchars($asset['description'] ?? 'N/A'); ?></td>
                </tr>
            </table>
            
            <div style="margin-top: 20px;">
                <a href="edit_asset.php?id=<?php echo $asset['id']; ?>" class="btn btn-primary">Edit Asset</a>
                <a href="asset_list.php" class="btn">Back to List</a>
            </div>
        </div>

        <?php if ($assignment): ?>
            <div style="margin-top: 40px;">
                <h3>Current Assignment</h3>
                <table style="width: auto;">
                    <tr>
                        <th style="background: #f4f4f4; color: #333; width: 200px;">Assigned To:</th>
                        <td>
                            <a href="admin/employee_details.php?id=<?php echo $assignment['employee_id']; ?>">
                                <?php echo htmlspecialchars($assignment['first_name'] . ' ' . $assignment['last_name']); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th style="background: #f4f4f4; color: #333;">Email:</th>
                        <td><?php echo htmlspecialchars($assignment['email']); ?></td>
                    </tr>
                    <tr>
                        <th style="background: #f4f4f4; color: #333;">Department:</th>
                        <td><?php echo htmlspecialchars($assignment['department'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th style="background: #f4f4f4; color: #333;">Assigned Date:</th>
                        <td><?php echo htmlspecialchars($assignment['assigned_date']); ?></td>
                    </tr>
                </table>
            </div>
        <?php else: ?>
            <div style="margin-top: 40px;">
                <p class="alert alert-info">This asset is currently not assigned to any employee.</p>
            </div>
        <?php endif; ?>

        <div style="margin-top: 40px;">
            <h3>Assignment History</h3>
            <?php if ($history_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Assigned Date</th>
                            <th>Returned Date</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($history = $history_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <a href="admin/employee_details.php?id=<?php echo $history['employee_id']; ?>">
                                        <?php echo htmlspecialchars($history['first_name'] . ' ' . $history['last_name']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($history['assigned_date']); ?></td>
                                <td><?php echo htmlspecialchars($history['returned_date'] ?? 'Not returned'); ?></td>
                                <td><?php echo htmlspecialchars($history['status']); ?></td>
                                <td><?php echo htmlspecialchars($history['notes'] ?? ''); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="alert alert-info">No assignment history for this asset.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="alert alert-error">
            <strong>Error:</strong> Asset with ID <?php echo htmlspecialchars($asset_id); ?> not found.
        </div>
        <a href="asset_list.php" class="btn">Back to List</a>
    <?php endif; ?>
</div>

<?php
if (isset($stmt)) $stmt->close();
if (isset($assignment_stmt)) $assignment_stmt->close();
if (isset($history_stmt)) $history_stmt->close();
if (isset($conn)) $conn->close();
include 'includes/footer.php';
?>

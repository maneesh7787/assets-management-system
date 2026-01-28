<?php
require_once '../config/db.php';

$page_title = "Employee Details";

// Get employee ID from URL parameter
$employee_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch employee details from database
$conn = getDBConnection();
$sql = "SELECT * FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

// Fetch assets assigned to this employee
$assets_sql = "SELECT a.* FROM assets a 
               INNER JOIN asset_assignments aa ON a.id = aa.asset_id 
               WHERE aa.employee_id = ? AND aa.status = 'active'";
$assets_stmt = $conn->prepare($assets_sql);
$assets_stmt->bind_param("i", $employee_id);
$assets_stmt->execute();
$assets_result = $assets_stmt->get_result();

include '../includes/header.php';
?>

<div class="card">
    <h2>Employee Details</h2>
    
    <?php if ($employee): ?>
        <div style="margin-top: 20px;">
            <table style="width: auto;">
                <tr>
                    <th style="background: #f4f4f4; color: #333; width: 200px;">Employee ID:</th>
                    <td><?php echo htmlspecialchars($employee['id']); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Name:</th>
                    <td><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Email:</th>
                    <td><?php echo htmlspecialchars($employee['email']); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Phone:</th>
                    <td><?php echo htmlspecialchars($employee['phone'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Department:</th>
                    <td><?php echo htmlspecialchars($employee['department'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Position:</th>
                    <td><?php echo htmlspecialchars($employee['position'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Join Date:</th>
                    <td><?php echo htmlspecialchars($employee['join_date'] ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th style="background: #f4f4f4; color: #333;">Status:</th>
                    <td><?php echo htmlspecialchars($employee['status'] ?? 'active'); ?></td>
                </tr>
            </table>
            
            <div style="margin-top: 20px;">
                <a href="../edit_employee.php?id=<?php echo $employee['id']; ?>" class="btn btn-primary">Edit Employee</a>
                <a href="employee_list.php" class="btn">Back to List</a>
            </div>
        </div>

        <div style="margin-top: 40px;">
            <h3>Assigned Assets</h3>
            <?php if ($assets_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Asset ID</th>
                            <th>Asset Name</th>
                            <th>Asset Type</th>
                            <th>Serial Number</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($asset = $assets_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($asset['id']); ?></td>
                                <td><?php echo htmlspecialchars($asset['name']); ?></td>
                                <td><?php echo htmlspecialchars($asset['type'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($asset['serial_number'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($asset['status'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="../asset_details.php?id=<?php echo $asset['id']; ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">View</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="alert alert-info">No assets assigned to this employee.</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="alert alert-error">
            <strong>Error:</strong> Employee with ID <?php echo $employee_id; ?> not found.
        </div>
        <a href="employee_list.php" class="btn">Back to List</a>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$assets_stmt->close();
$conn->close();
include '../includes/footer.php';
?>

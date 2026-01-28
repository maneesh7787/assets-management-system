<?php
require_once 'config/db.php';

$page_title = "Edit Employee";

// Get employee ID from URL parameter
$employee_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $department = trim($_POST['department']);
    $position = trim($_POST['position']);
    $join_date = trim($_POST['join_date']);
    $status = trim($_POST['status']);

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error_message = "First name, last name, and email are required fields.";
    } else {
        $conn = getDBConnection();
        
        // Update employee record
        $sql = "UPDATE employees SET 
                first_name = ?, 
                last_name = ?, 
                email = ?, 
                phone = ?, 
                department = ?, 
                position = ?, 
                join_date = ?, 
                status = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", $first_name, $last_name, $email, $phone, $department, $position, $join_date, $status, $employee_id);
        
        if ($stmt->execute()) {
            $success_message = "Employee information updated successfully!";
        } else {
            $error_message = "Error updating employee: " . $conn->error;
        }
        
        $stmt->close();
        $conn->close();
    }
}

// Fetch current employee details
$conn = getDBConnection();
$sql = "SELECT * FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();

include 'includes/header.php';
?>

<div class="card">
    <h2>Edit Employee</h2>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-error"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <?php if ($employee): ?>
        <form method="POST" action="" style="margin-top: 20px;">
            <div class="form-group">
                <label for="first_name">First Name *</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($employee['first_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name *</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($employee['last_name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($employee['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="department">Department</label>
                <input type="text" id="department" name="department" value="<?php echo htmlspecialchars($employee['department'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($employee['position'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="join_date">Join Date</label>
                <input type="date" id="join_date" name="join_date" value="<?php echo htmlspecialchars($employee['join_date'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="active" <?php echo ($employee['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($employee['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    <option value="on_leave" <?php echo ($employee['status'] ?? '') === 'on_leave' ? 'selected' : ''; ?>>On Leave</option>
                    <option value="terminated" <?php echo ($employee['status'] ?? '') === 'terminated' ? 'selected' : ''; ?>>Terminated</option>
                </select>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-success">Update Employee</button>
                <a href="admin/employee_details.php?id=<?php echo $employee_id; ?>" class="btn">Cancel</a>
                <a href="admin/employee_list.php" class="btn">Back to List</a>
            </div>
        </form>
    <?php else: ?>
        <div class="alert alert-error">
            <strong>Error:</strong> Employee with ID <?php echo $employee_id; ?> not found.
        </div>
        <a href="admin/employee_list.php" class="btn">Back to List</a>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
include 'includes/footer.php';
?>

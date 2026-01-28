<?php
require_once 'config/db.php';

$page_title = "Assets Management System - Home";

include 'includes/header.php';
?>

<div class="card">
    <h2>Welcome to Assets Management System</h2>
    <p>This system helps you manage employees and company assets efficiently.</p>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
        <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <h3 style="color: white;">Employees</h3>
            <p>Manage employee information and records</p>
            <a href="admin/employee_list.php" class="btn" style="background: white; color: #667eea; margin-top: 10px;">View Employees</a>
        </div>
        
        <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
            <h3 style="color: white;">Assets</h3>
            <p>Track and manage company assets</p>
            <a href="asset_list.php" class="btn" style="background: white; color: #f5576c; margin-top: 10px;">View Assets</a>
        </div>
        
        <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
            <h3 style="color: white;">Admin Panel</h3>
            <p>Access administrative functions</p>
            <a href="admin/dashboard.php" class="btn" style="background: white; color: #4facfe; margin-top: 10px;">Go to Dashboard</a>
        </div>
    </div>
</div>

<div class="card">
    <h3>Quick Stats</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
        <?php
        $conn = getDBConnection();
        
        // Count total employees
        $emp_result = $conn->query("SELECT COUNT(*) as count FROM employees WHERE status = 'active'");
        $emp_count = $emp_result ? $emp_result->fetch_assoc()['count'] : 0;
        
        // Count total assets
        $asset_result = $conn->query("SELECT COUNT(*) as count FROM assets");
        $asset_count = $asset_result ? $asset_result->fetch_assoc()['count'] : 0;
        
        // Count assigned assets
        $assigned_result = $conn->query("SELECT COUNT(DISTINCT asset_id) as count FROM asset_assignments WHERE status = 'active'");
        $assigned_count = $assigned_result ? $assigned_result->fetch_assoc()['count'] : 0;
        
        $conn->close();
        ?>
        
        <div style="background: #e3f2fd; padding: 20px; border-radius: 5px; text-align: center;">
            <h2 style="color: #1976d2; margin: 0;"><?php echo $emp_count; ?></h2>
            <p style="margin: 5px 0 0 0;">Active Employees</p>
        </div>
        
        <div style="background: #f3e5f5; padding: 20px; border-radius: 5px; text-align: center;">
            <h2 style="color: #7b1fa2; margin: 0;"><?php echo $asset_count; ?></h2>
            <p style="margin: 5px 0 0 0;">Total Assets</p>
        </div>
        
        <div style="background: #e8f5e9; padding: 20px; border-radius: 5px; text-align: center;">
            <h2 style="color: #388e3c; margin: 0;"><?php echo $assigned_count; ?></h2>
            <p style="margin: 5px 0 0 0;">Assigned Assets</p>
        </div>
        
        <div style="background: #fff3e0; padding: 20px; border-radius: 5px; text-align: center;">
            <h2 style="color: #f57c00; margin: 0;"><?php echo $asset_count - $assigned_count; ?></h2>
            <p style="margin: 5px 0 0 0;">Available Assets</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

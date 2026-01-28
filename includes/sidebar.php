<!-- Sidebar -->
<div class="bg-light border-end" id="sidebar-wrapper">
    <div class="list-group list-group-flush">
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        $roleId = getCurrentUserRole();
        
        // Admin/IT Menu
        if ($roleId == 1): ?>
            <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/employees.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'employees.php' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> Manage Employees
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/assets.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'assets.php' ? 'active' : ''; ?>">
                <i class="bi bi-laptop"></i> Manage Assets
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/approvals.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'approvals.php' ? 'active' : ''; ?>">
                <i class="bi bi-check-circle"></i> Pending Approvals
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/transfers.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'transfers.php' ? 'active' : ''; ?>">
                <i class="bi bi-arrow-left-right"></i> Asset Transfers
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/reports.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'reports.php' ? 'active' : ''; ?>">
                <i class="bi bi-file-bar-graph"></i> Reports
            </a>
            <a href="<?php echo BASE_URL; ?>/admin/audit_logs.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'audit_logs.php' ? 'active' : ''; ?>">
                <i class="bi bi-clock-history"></i> Audit Logs
            </a>
        
        <?php elseif ($roleId == 2): // HR Menu ?>
            <a href="<?php echo BASE_URL; ?>/hr/dashboard.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>/hr/employees.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'employees.php' ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> View Employees
            </a>
            <a href="<?php echo BASE_URL; ?>/hr/assets.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'assets.php' ? 'active' : ''; ?>">
                <i class="bi bi-laptop"></i> View Assets
            </a>
            <a href="<?php echo BASE_URL; ?>/hr/reports.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'reports.php' ? 'active' : ''; ?>">
                <i class="bi bi-file-bar-graph"></i> Reports
            </a>
        
        <?php elseif ($roleId == 3): // Employee Menu ?>
            <a href="<?php echo BASE_URL; ?>/employee/dashboard.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>/employee/submit_assets.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'submit_assets.php' ? 'active' : ''; ?>">
                <i class="bi bi-plus-circle"></i> Submit Assets
            </a>
            <a href="<?php echo BASE_URL; ?>/employee/my_assets.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'my_assets.php' ? 'active' : ''; ?>">
                <i class="bi bi-laptop"></i> My Assets
            </a>
            <a href="<?php echo BASE_URL; ?>/employee/history.php" 
               class="list-group-item list-group-item-action <?php echo $currentPage == 'history.php' ? 'active' : ''; ?>">
                <i class="bi bi-clock-history"></i> Asset History
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Page Content -->
<div id="page-content-wrapper">
    <div class="container-fluid p-4">

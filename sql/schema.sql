-- Asset Management System Database Schema
-- Created: 2026-01-28
-- Database: asset_management_system

-- Create database
CREATE DATABASE IF NOT EXISTS asset_management_system;
USE asset_management_system;

-- Table: roles
-- Stores user role definitions
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    role_description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: users
-- Stores user login credentials
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: employees
-- Stores employee information
CREATE TABLE IF NOT EXISTS employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    emp_code VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    department VARCHAR(100),
    designation VARCHAR(100),
    work_location VARCHAR(150),
    date_of_joining DATE,
    status ENUM('Active', 'Exiting', 'Inactive') DEFAULT 'Active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: assets
-- Stores asset information
CREATE TABLE IF NOT EXISTS assets (
    asset_id INT AUTO_INCREMENT PRIMARY KEY,
    asset_type VARCHAR(50) NOT NULL,
    brand VARCHAR(100),
    serial_number VARCHAR(150) NOT NULL UNIQUE,
    date_of_issue DATE,
    condition_at_issue VARCHAR(50),
    current_status ENUM('Available', 'Assigned', 'Returned', 'Replaced', 'Damaged') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: asset_assignments
-- Stores current asset assignments to employees
CREATE TABLE IF NOT EXISTS asset_assignments (
    assignment_id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    employee_id INT NOT NULL,
    assigned_date DATE NOT NULL,
    returned_date DATE NULL,
    assignment_status ENUM('Pending', 'Approved', 'Returned', 'Rejected') DEFAULT 'Pending',
    submitted_by INT,
    approved_by INT NULL,
    approval_date DATETIME NULL,
    remarks TEXT,
    declaration_accepted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(asset_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE CASCADE,
    FOREIGN KEY (submitted_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: asset_history
-- Stores complete history of asset movements
CREATE TABLE IF NOT EXISTS asset_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    employee_id INT,
    action_type ENUM('Assigned', 'Returned', 'Replaced', 'Transferred', 'Damaged', 'Created') NOT NULL,
    action_date DATETIME NOT NULL,
    performed_by INT,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(asset_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id) ON DELETE SET NULL,
    FOREIGN KEY (performed_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: approvals
-- Stores approval workflow records
CREATE TABLE IF NOT EXISTS approvals (
    approval_id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    approver_id INT NOT NULL,
    approval_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    approval_date DATETIME NULL,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES asset_assignments(assignment_id) ON DELETE CASCADE,
    FOREIGN KEY (approver_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: audit_logs
-- Stores all system activities for audit trail
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_value TEXT,
    new_value TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default roles
INSERT INTO roles (role_name, role_description) VALUES
('Admin', 'IT/Admin team with full system access'),
('HR', 'HR team with read-only access'),
('Employee', 'Regular employee with limited access');

-- Create default admin user
-- Username: admin, Password: Admin@123
INSERT INTO users (username, password, role_id, is_active) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1);

-- Indexes for performance
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_employees_emp_code ON employees(emp_code);
CREATE INDEX idx_assets_serial ON assets(serial_number);
CREATE INDEX idx_assignments_status ON asset_assignments(assignment_status);
CREATE INDEX idx_audit_user ON audit_logs(user_id);
CREATE INDEX idx_audit_created ON audit_logs(created_at);

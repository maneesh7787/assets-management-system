-- Sample Data for Asset Management System
-- Use this file to populate the database with test data

USE asset_management_system;

-- Sample HR user
-- Username: hr_user, Password: HR@123
INSERT INTO users (username, password, role_id, is_active) VALUES
('hr_user', '$2y$10$zLcQ8P3qH5Z0XxXxK3mWQeGKjH6XvZ7LmPxMxN9QxYxZxXxXxXxXx', 2, 1);

-- Sample Employees (created by admin)
INSERT INTO employees (user_id, emp_code, full_name, email, phone, department, designation, work_location, date_of_joining, status, created_by) VALUES
(NULL, 'EMP001', 'John Doe', 'john.doe@company.com', '9876543210', 'Engineering', 'Software Engineer', 'Bangalore', '2024-01-15', 'Active', 1),
(NULL, 'EMP002', 'Jane Smith', 'jane.smith@company.com', '9876543211', 'Marketing', 'Marketing Manager', 'Mumbai', '2024-02-01', 'Active', 1),
(NULL, 'EMP003', 'Mike Johnson', 'mike.johnson@company.com', '9876543212', 'Sales', 'Sales Executive', 'Delhi', '2024-03-10', 'Active', 1);

-- Create user accounts for employees
-- Password for all: Emp@123
INSERT INTO users (username, password, role_id, is_active) VALUES
('john.doe', '$2y$10$vJxH5jY8ZxXxXxXxXxXxXuGHjK6LmPxN9QxYxZxXxXxXxXxXxXxXx', 3, 1),
('jane.smith', '$2y$10$vJxH5jY8ZxXxXxXxXxXxXuGHjK6LmPxN9QxYxZxXxXxXxXxXxXxXx', 3, 1),
('mike.johnson', '$2y$10$vJxH5jY8ZxXxXxXxXxXxXuGHjK6LmPxN9QxYxZxXxXxXxXxXxXxXx', 3, 1);

-- Link employees to user accounts
UPDATE employees SET user_id = 4 WHERE emp_code = 'EMP001';
UPDATE employees SET user_id = 5 WHERE emp_code = 'EMP002';
UPDATE employees SET user_id = 6 WHERE emp_code = 'EMP003';

-- Sample Assets
INSERT INTO assets (asset_type, brand, serial_number, date_of_issue, condition_at_issue, current_status) VALUES
('Laptop', 'Dell', 'DL12345678', '2024-01-15', 'New', 'Assigned'),
('Charger', 'Dell', 'CH12345678', '2024-01-15', 'New', 'Assigned'),
('Mouse', 'Logitech', 'MO12345678', '2024-01-15', 'New', 'Assigned'),
('Keyboard', 'Logitech', 'KB12345678', '2024-02-01', 'New', 'Assigned'),
('Laptop', 'HP', 'HP12345678', '2024-02-01', 'New', 'Assigned'),
('Laptop', 'Lenovo', 'LN12345678', '2024-03-10', 'New', 'Assigned');

-- Sample Asset Assignments (Approved)
INSERT INTO asset_assignments (asset_id, employee_id, assigned_date, assignment_status, submitted_by, approved_by, approval_date, declaration_accepted) VALUES
(1, 1, '2024-01-15', 'Approved', 4, 1, '2024-01-15 10:00:00', 1),
(2, 1, '2024-01-15', 'Approved', 4, 1, '2024-01-15 10:00:00', 1),
(3, 1, '2024-01-15', 'Approved', 4, 1, '2024-01-15 10:00:00', 1),
(4, 2, '2024-02-01', 'Approved', 5, 1, '2024-02-01 11:00:00', 1),
(5, 2, '2024-02-01', 'Approved', 5, 1, '2024-02-01 11:00:00', 1),
(6, 3, '2024-03-10', 'Approved', 6, 1, '2024-03-10 09:00:00', 1);

-- Sample Asset History
INSERT INTO asset_history (asset_id, employee_id, action_type, action_date, performed_by, old_status, new_status) VALUES
(1, 1, 'Assigned', '2024-01-15 10:00:00', 1, 'Available', 'Assigned'),
(2, 1, 'Assigned', '2024-01-15 10:00:00', 1, 'Available', 'Assigned'),
(3, 1, 'Assigned', '2024-01-15 10:00:00', 1, 'Available', 'Assigned'),
(4, 2, 'Assigned', '2024-02-01 11:00:00', 1, 'Available', 'Assigned'),
(5, 2, 'Assigned', '2024-02-01 11:00:00', 1, 'Available', 'Assigned'),
(6, 3, 'Assigned', '2024-03-10 09:00:00', 1, 'Available', 'Assigned');

-- Sample Audit Logs
INSERT INTO audit_logs (user_id, action, table_name, record_id, new_value, ip_address) VALUES
(1, 'Employee Created', 'employees', 1, 'EMP001 - John Doe', '127.0.0.1'),
(1, 'Employee Created', 'employees', 2, 'EMP002 - Jane Smith', '127.0.0.1'),
(1, 'Employee Created', 'employees', 3, 'EMP003 - Mike Johnson', '127.0.0.1'),
(1, 'Asset Approved', 'asset_assignments', 1, 'Asset assigned to EMP001', '127.0.0.1'),
(1, 'Asset Approved', 'asset_assignments', 2, 'Asset assigned to EMP002', '127.0.0.1'),
(1, 'Asset Approved', 'asset_assignments', 3, 'Asset assigned to EMP003', '127.0.0.1');

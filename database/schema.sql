-- Assets Management System Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS assets_management;
USE assets_management;

-- Employees table
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    department VARCHAR(100),
    position VARCHAR(100),
    join_date DATE,
    status ENUM('active', 'inactive', 'on_leave', 'terminated') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Assets table
CREATE TABLE IF NOT EXISTS assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    brand VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(255) UNIQUE,
    purchase_date DATE,
    purchase_price DECIMAL(10, 2),
    warranty_expiry DATE,
    status ENUM('available', 'assigned', 'maintenance', 'retired', 'lost') DEFAULT 'available',
    location VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Asset assignments table
CREATE TABLE IF NOT EXISTS asset_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT NOT NULL,
    employee_id INT NOT NULL,
    assigned_date DATE NOT NULL,
    returned_date DATE,
    status ENUM('active', 'returned', 'lost', 'damaged') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Insert sample employees
INSERT INTO employees (first_name, last_name, email, phone, department, position, join_date, status) VALUES
('John', 'Doe', 'john.doe@example.com', '555-0101', 'IT', 'Software Developer', '2023-01-15', 'active'),
('Jane', 'Smith', 'jane.smith@example.com', '555-0102', 'HR', 'HR Manager', '2022-06-01', 'active'),
('Michael', 'Johnson', 'michael.johnson@example.com', '555-0103', 'Finance', 'Financial Analyst', '2023-03-20', 'active'),
('Emily', 'Brown', 'emily.brown@example.com', '555-0104', 'Marketing', 'Marketing Coordinator', '2023-07-10', 'active'),
('David', 'Wilson', 'david.wilson@example.com', '555-0105', 'IT', 'System Administrator', '2022-11-05', 'active');

-- Insert sample assets
INSERT INTO assets (name, type, brand, model, serial_number, purchase_date, purchase_price, warranty_expiry, status, location, description) VALUES
('Dell Laptop', 'Laptop', 'Dell', 'Latitude 5420', 'DL2023001', '2023-01-10', 1200.00, '2026-01-10', 'assigned', 'Office Floor 2', 'Standard employee laptop'),
('MacBook Pro', 'Laptop', 'Apple', 'MacBook Pro 14"', 'MBP2023001', '2023-02-15', 2500.00, '2026-02-15', 'assigned', 'Office Floor 3', 'Developer laptop'),
('HP Monitor', 'Monitor', 'HP', '27" 4K', 'HPM2023001', '2023-01-20', 450.00, '2026-01-20', 'assigned', 'Office Floor 2', '27 inch 4K monitor'),
('iPhone 13', 'Mobile', 'Apple', 'iPhone 13', 'IP2023001', '2023-03-01', 900.00, '2024-03-01', 'assigned', 'Office', 'Company mobile phone'),
('Cisco Router', 'Network', 'Cisco', 'ISR 4331', 'CSR2023001', '2022-12-15', 3500.00, '2025-12-15', 'available', 'Server Room', 'Network router'),
('Standing Desk', 'Furniture', 'Ergonomic Inc', 'SD-2000', 'SD2023001', '2023-04-10', 800.00, NULL, 'assigned', 'Office Floor 2', 'Electric standing desk'),
('Dell Monitor', 'Monitor', 'Dell', 'UltraSharp 27"', 'DM2023001', '2023-05-15', 500.00, '2026-05-15', 'available', 'Storage', '27 inch monitor');

-- Insert sample asset assignments
INSERT INTO asset_assignments (asset_id, employee_id, assigned_date, status, notes) VALUES
(1, 1, '2023-01-15', 'active', 'Assigned on join date'),
(2, 1, '2023-02-20', 'active', 'Upgraded for development work'),
(3, 1, '2023-01-16', 'active', 'Dual monitor setup'),
(4, 2, '2023-03-05', 'active', 'For business calls'),
(6, 3, '2023-04-15', 'active', 'Ergonomic workstation');

# Assets Management System

A comprehensive PHP-based system for managing employees and company assets.

## Features

- **Employee Management**: Track employee information including contact details, department, position, and status
- **Asset Management**: Manage company assets with detailed information including purchase date, warranty, and location
- **Asset Assignment**: Track which assets are assigned to which employees with full assignment history
- **Three Main Pages** (as requested):
  1. `admin/employee_details.php?id=1` - View detailed employee information
  2. `edit_employee.php?id=1` - Edit employee details
  3. `asset_details.php?id=1` - View detailed asset information

## Database Schema

The system uses MySQL/MariaDB with three main tables:
- `employees` - Store employee information
- `assets` - Store asset details
- `asset_assignments` - Track asset assignments to employees

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or MariaDB 10.3 or higher
- Apache/Nginx web server

### Installation

1. Clone the repository:
```bash
git clone https://github.com/maneesh7787/assets-management-system.git
cd assets-management-system
```

2. Create the database and import the schema:
```bash
mysql -u root -p < database/schema.sql
```

3. Configure database connection:
Edit `config/db.php` and update the database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'assets_management');
```

4. Configure your web server to point to the project directory

5. Access the application:
```
http://localhost/index.php
```

## File Structure

```
assets-management-system/
├── admin/
│   └── employee_details.php    # View employee details (with ID parameter)
├── config/
│   └── db.php                  # Database configuration
├── database/
│   └── schema.sql              # Database schema and sample data
├── includes/
│   ├── header.php              # Common header template
│   └── footer.php              # Common footer template
├── asset_details.php           # View asset details (with ID parameter)
├── edit_employee.php           # Edit employee (with ID parameter)
└── index.php                   # Home page with dashboard
```

## Usage

### View Employee Details
Navigate to: `admin/employee_details.php?id=1`
- Displays complete employee information
- Shows all assets assigned to the employee
- Provides link to edit employee

### Edit Employee
Navigate to: `edit_employee.php?id=1`
- Form to update employee information
- All fields are pre-filled with current data
- Updates database on form submission

### View Asset Details
Navigate to: `asset_details.php?id=1`
- Displays complete asset information
- Shows current assignment (if any)
- Displays full assignment history

## Sample Data

The schema includes sample data:
- 5 employees in various departments
- 7 assets (laptops, monitors, phones, etc.)
- 5 asset assignments

## Technologies Used

- PHP 7.4+
- MySQL/MariaDB
- HTML5/CSS3
- Prepared Statements (for SQL injection prevention)

## Security Features

- SQL injection protection using prepared statements
- XSS protection with htmlspecialchars()
- Input validation and sanitization
- Parameterized queries throughout

## License

This project is open source and available under the MIT License.
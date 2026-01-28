# Asset Management System

A comprehensive web-based Asset Management System built with PHP, MySQL, Bootstrap 5, and jQuery. This system enables organizations to efficiently manage employee assets with role-based access control.

## 🎯 Features

- **Role-Based Access Control (RBAC)** - Three distinct user roles with specific permissions
- **Employee Management** - Complete CRUD operations for employee records
- **Asset Tracking** - Track allocation, transfers, and returns of company assets
- **Approval Workflow** - IT/Admin approval system for asset assignments
- **Audit Logging** - Complete audit trail of all system activities
- **Responsive Design** - Mobile-friendly interface using Bootstrap 5
- **Security** - Password hashing, session management, and SQL injection prevention

## 👥 User Roles

### 1. Admin / IT Team (Super Role)
- Create, edit, and deactivate employee accounts
- Manage asset inventory
- Review and approve asset submissions
- Handle asset transfers and replacements
- Manage employee exit process
- View audit logs and reports

### 2. HR Team (Read-Only)
- View employee records (read-only)
- View asset allocations
- Track employee exits and asset recovery
- Generate reports

### 3. Employee
- Submit asset allocation requests
- View assigned assets
- Track approval status
- View asset history

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser

## 🚀 Installation

### Step 1: Clone the Repository
```bash
git clone https://github.com/maneesh7787/assets-management-system.git
cd assets-management-system
```

### Step 2: Database Setup
1. Create a MySQL database:
```sql
CREATE DATABASE asset_management_system;
```

2. Import the schema:
```bash
mysql -u root -p asset_management_system < sql/schema.sql
```

3. (Optional) Load sample data:
```bash
mysql -u root -p asset_management_system < sql/sample_data.sql
```

### Step 3: Configure Database Connection
Edit `config/config.php` and update database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'asset_management_system');
define('BASE_URL', 'http://localhost/assets-management-system');
```

### Step 4: Set Up Web Server
- Place the project in your web server's document root (e.g., `htdocs` for XAMPP)
- Ensure PHP has read/write permissions to the project directory
- Access the application via: `http://localhost/assets-management-system`

## 🔐 Default Credentials

### Admin Account
- **Username:** `admin`
- **Password:** `Admin@123`

### Sample Employee Accounts (if sample data loaded)
- **Username:** `john.doe` | **Password:** `Emp@123`
- **Username:** `jane.smith` | **Password:** `Emp@123`
- **Username:** `mike.johnson` | **Password:** `Emp@123`

### HR Account (if sample data loaded)
- **Username:** `hr_user` | **Password:** `HR@123`

> **Note:** Change default passwords immediately after first login.

## 📁 Project Structure

```
assets-management-system/
├── admin/                  # Admin module pages
│   ├── dashboard.php
│   ├── employees.php
│   ├── approvals.php
│   └── ...
├── hr/                    # HR module pages
│   ├── dashboard.php
│   ├── employees.php
│   └── ...
├── employee/              # Employee module pages
│   ├── dashboard.php
│   ├── submit_assets.php
│   ├── my_assets.php
│   └── ...
├── assets/               # Static assets
│   ├── css/
│   ├── js/
│   └── images/
├── config/               # Configuration files
│   ├── config.php
│   └── database.php
├── includes/             # Reusable components
│   ├── header.php
│   ├── footer.php
│   ├── sidebar.php
│   ├── session.php
│   └── functions.php
├── sql/                  # Database files
│   ├── schema.sql
│   └── sample_data.sql
├── index.php            # Login page
├── logout.php
├── profile.php
└── README.md
```

## 🔄 Business Flow

### Employee Creation Flow
1. IT/Admin creates employee account
2. System auto-generates username and password
3. Credentials are provided to employee
4. Employee logs in and submits assets

### Asset Allocation Flow
1. Employee submits asset details with declaration
2. Asset status = Pending IT Approval
3. IT/Admin reviews submission
4. IT/Admin approves or rejects
5. Once approved, asset is locked (employee cannot modify)
6. HR can view approved assets

### Asset Transfer/Replacement
1. Initiated by IT/Admin only
2. Old asset marked as Returned/Replaced
3. New asset assigned to employee
4. Complete history maintained

### Employee Exit Flow
1. Employee status changed to "Exiting"
2. IT/Admin verifies asset collection
3. Asset status updated to "Returned"
4. Employee account deactivated

## 🗄️ Database Schema

### Main Tables
- `users` - User authentication and login credentials
- `roles` - Role definitions (Admin, HR, Employee)
- `employees` - Employee information
- `assets` - Asset inventory
- `asset_assignments` - Asset-to-employee mappings
- `asset_history` - Complete asset movement history
- `approvals` - Approval workflow records
- `audit_logs` - System activity audit trail

## 🔒 Security Features

- Password hashing using `password_hash()`
- Session-based authentication
- Role-based access control (RBAC)
- SQL injection prevention using `mysqli_real_escape_string()`
- Input sanitization and validation
- Server-side and client-side validation
- Session timeout (configurable)
- Audit logging for all critical actions

## 🎨 UI/UX Features

- Clean, professional Bootstrap 5 design
- Responsive layout (mobile-friendly)
- Role-specific dashboards and sidebars
- Status badges and alerts
- Modal dialogs for forms
- Confirmation dialogs for critical actions
- Loading spinners
- Auto-dismissing alerts

## 📊 Reports and Analytics

- Dashboard statistics
- Asset allocation reports
- Employee asset summary
- Asset history tracking
- Audit logs

## 🛠️ Technology Stack

- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript, jQuery
- **Backend:** Core PHP (no frameworks)
- **Database:** MySQL
- **Database Access:** mysqli (no PDO or prepared statements)

## ⚠️ Important Notes

- **No PDO or Prepared Statements:** System uses mysqli queries as per requirements
- **Employee Creation:** Only IT/Admin can create employee accounts
- **Asset Editing:** Employees cannot edit assets after submission
- **HR Limitations:** HR has read-only access, cannot modify records
- **Security:** Despite using simple mysqli queries, all inputs are sanitized

## 🤝 Contributing

This is a demonstration project. For production use, consider:
- Implementing PDO with prepared statements
- Adding more comprehensive error handling
- Implementing advanced security measures
- Adding more detailed reporting features
- Implementing email notifications

## 📝 License

This project is open-source and available for educational purposes.

## 👤 Author

Created as a demonstration of a complete asset management system using core PHP.

## 📞 Support

For issues or questions, please open an issue in the GitHub repository.

---

**Version:** 1.0.0  
**Last Updated:** January 2026
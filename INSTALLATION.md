# Installation Guide - Asset Management System

This guide will walk you through the complete installation and setup of the Asset Management System.

## Prerequisites

Before you begin, ensure you have the following installed:

- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher (or MariaDB 10.3+)
- **Web Browser**: Chrome, Firefox, Safari, or Edge (latest versions)

### Required PHP Extensions
- mysqli
- session
- json
- mbstring

## Installation Steps

### Step 1: Download/Clone the Repository

```bash
# Using Git
git clone https://github.com/maneesh7787/assets-management-system.git

# Or download and extract the ZIP file
```

### Step 2: Move to Web Server Directory

Move the project to your web server's document root:

**For XAMPP (Windows/Mac/Linux):**
```bash
mv assets-management-system /path/to/xampp/htdocs/
```

**For WAMP (Windows):**
```bash
mv assets-management-system C:/wamp64/www/
```

**For Linux (Apache):**
```bash
sudo mv assets-management-system /var/www/html/
sudo chown -R www-data:www-data /var/www/html/assets-management-system
```

### Step 3: Create MySQL Database

Open MySQL command line or phpMyAdmin and create a new database:

```sql
CREATE DATABASE asset_management_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Step 4: Import Database Schema

**Option A: Using Command Line**
```bash
mysql -u root -p asset_management_system < sql/schema.sql
```

**Option B: Using phpMyAdmin**
1. Open phpMyAdmin in your browser
2. Select the `asset_management_system` database
3. Click on "Import" tab
4. Choose the file `sql/schema.sql`
5. Click "Go" to import

### Step 5: (Optional) Import Sample Data

If you want to test with sample data:

```bash
mysql -u root -p asset_management_system < sql/sample_data.sql
```

This will create:
- 1 Admin user
- 1 HR user
- 3 Sample employees
- 6 Sample assets
- Asset assignments and history

### Step 6: Configure Database Connection

Edit the file `config/config.php` and update the database credentials:

```php
// Database credentials
define('DB_HOST', 'localhost');          // Your MySQL host
define('DB_USER', 'root');               // Your MySQL username
define('DB_PASS', 'your_password');      // Your MySQL password
define('DB_NAME', 'asset_management_system');

// Base URL - Update this to match your setup
define('BASE_URL', 'http://localhost/assets-management-system');
```

**Important**: Update `BASE_URL` to match your actual installation URL.

Examples:
- Local: `http://localhost/assets-management-system`
- XAMPP: `http://localhost:8080/assets-management-system`
- Production: `https://yourdomain.com/assets`

### Step 7: Set Permissions (Linux/Mac only)

```bash
# Make sure web server can read all files
chmod -R 755 /var/www/html/assets-management-system

# Ensure config files are protected
chmod 600 /var/www/html/assets-management-system/config/*.php
```

### Step 8: Configure Apache (if needed)

If you're using Apache and `.htaccess` is not working:

1. Enable mod_rewrite:
```bash
sudo a2enmod rewrite
```

2. Edit Apache configuration to allow .htaccess overrides:
```apache
<Directory /var/www/html/assets-management-system>
    AllowOverride All
    Require all granted
</Directory>
```

3. Restart Apache:
```bash
sudo systemctl restart apache2
```

### Step 9: Access the Application

Open your web browser and navigate to:
```
http://localhost/assets-management-system
```

You should see the login page.

## Default Login Credentials

### Admin Account
- **Username:** `admin`
- **Password:** `Admin@123`

### HR Account (if sample data loaded)
- **Username:** `hr_user`
- **Password:** `HR@123`

### Employee Accounts (if sample data loaded)
- **Username:** `john.doe` | **Password:** `Emp@123`
- **Username:** `jane.smith` | **Password:** `Emp@123`
- **Username:** `mike.johnson` | **Password:** `Emp@123`

⚠️ **IMPORTANT**: Change all default passwords immediately after first login!

## Post-Installation Steps

### 1. Security Hardening

**Change Default Passwords:**
- Login as admin
- Go to Profile → Change Password
- Create a strong password

**Update Config for Production:**
Edit `config/config.php`:
```php
// Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);
```

**Secure Database User:**
Create a dedicated MySQL user instead of using root:
```sql
CREATE USER 'ams_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON asset_management_system.* TO 'ams_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2. Test the System

1. **Login as Admin**
   - Create a test employee
   - Note the auto-generated credentials
   - Create test assets

2. **Login as Employee**
   - Use the credentials from step 1
   - Submit asset allocation form
   - Verify submission

3. **Login as Admin Again**
   - Review pending approvals
   - Approve the test asset
   - Check audit logs

4. **Login as HR (if applicable)**
   - Verify read-only access
   - View employees and assets
   - Check reports

### 3. Create Your First Employee

1. Login as admin
2. Go to "Manage Employees"
3. Click "Add Employee"
4. Fill in employee details
5. Note down the auto-generated username and password
6. Communicate credentials securely to the employee

## Troubleshooting

### Database Connection Error

**Error:** "Connection failed: Access denied"

**Solution:**
- Verify database credentials in `config/config.php`
- Ensure MySQL service is running
- Check user has proper permissions

### Page Not Found (404)

**Error:** Pages show 404 or access forbidden

**Solution:**
- Check Apache configuration allows .htaccess
- Verify BASE_URL in config matches your actual URL
- Ensure mod_rewrite is enabled

### Session Timeout Too Soon

**Solution:**
Edit `config/config.php`:
```php
define('SESSION_TIMEOUT', 7200); // 2 hours in seconds
```

### Cannot Upload/Access Files

**Solution (Linux):**
```bash
sudo chown -R www-data:www-data /var/www/html/assets-management-system
sudo chmod -R 755 /var/www/html/assets-management-system
```

### Blank Page or PHP Errors

**Solution:**
- Check PHP error logs
- Ensure all required PHP extensions are installed
- Verify PHP version is 7.4+

```bash
php -v  # Check PHP version
php -m  # Check installed modules
```

## Maintenance

### Backup Database

**Regular backups are crucial!**

```bash
# Create backup
mysqldump -u root -p asset_management_system > backup_$(date +%Y%m%d).sql

# Restore from backup
mysql -u root -p asset_management_system < backup_20260128.sql
```

### Update Application

```bash
# Pull latest changes
git pull origin main

# Check for database schema updates
# Apply any new migration files if provided
```

### Monitor Audit Logs

- Regularly review audit logs for suspicious activity
- Admin → Audit Logs
- Filter by date and user

## Support

For issues or questions:
- Check the main README.md
- Review this installation guide
- Check GitHub Issues: https://github.com/maneesh7787/assets-management-system/issues

## Next Steps

After successful installation:
1. Change all default passwords
2. Create your organization's employees
3. Add your asset inventory
4. Configure any additional settings
5. Train users on the system

---

**Congratulations!** Your Asset Management System is now ready to use.

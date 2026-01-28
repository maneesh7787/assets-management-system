# Asset Management System - Features Documentation

## Overview

The Asset Management System is a comprehensive web-based application designed to manage company assets, employee records, and track asset allocations with a robust approval workflow and role-based access control.

## User Roles & Permissions

### 1. Admin / IT Team (Super Role)

**Full System Access with Following Capabilities:**

#### Employee Management
- ✅ Create new employee accounts
- ✅ Edit employee details (name, contact, department, designation, etc.)
- ✅ Deactivate/Activate employee accounts
- ✅ Reset employee passwords
- ✅ View complete employee list
- ✅ Manage employee exit process
- ✅ Auto-generate login credentials for employees

#### Asset Management
- ✅ Add new assets to inventory
- ✅ Edit asset details (type, brand, condition, status)
- ✅ View all assets with current assignment status
- ✅ Track asset location and condition
- ✅ Manage asset lifecycle

#### Approval Workflow
- ✅ Review pending asset assignment requests
- ✅ Approve asset allocations
- ✅ Reject asset requests with comments
- ✅ View approval history

#### Asset Transfers
- ✅ Transfer assets between employees
- ✅ Track transfer history
- ✅ Document transfer reasons

#### Reporting & Analytics
- ✅ Employee statistics dashboard
- ✅ Asset distribution reports
- ✅ Department-wise analysis
- ✅ Asset type breakdown
- ✅ Assignment status reports
- ✅ Export/print reports

#### Audit & Compliance
- ✅ View complete audit logs
- ✅ Filter logs by user, action, date
- ✅ Track all system activities
- ✅ Monitor user actions

### 2. HR Team (View & Track Only)

**Read-Only Access with Following Features:**

#### Employee Viewing
- ✅ View all employee records (read-only)
- ✅ Access employee contact information
- ✅ View employee department and designation
- ✅ Check employee status (Active/Exiting/Inactive)
- ❌ Cannot create or edit employees

#### Asset Tracking
- ✅ View all approved assets
- ✅ Track asset allocation per employee
- ✅ View asset assignment history
- ✅ Monitor asset status
- ❌ Cannot approve or manage assets

#### Exit Management
- ✅ View employees in exit process
- ✅ Track pending asset returns
- ✅ Monitor asset recovery status
- ✅ Generate exit reports
- ❌ Cannot update asset status (must coordinate with IT)

#### Reports
- ✅ View department-wise summaries
- ✅ Access employee asset reports
- ✅ Export and print reports
- ❌ No edit or approval rights

### 3. Employee (Self-Service)

**Limited Access for Personal Asset Management:**

#### Asset Submission
- ✅ Submit asset allocation requests
- ✅ Add multiple assets in one submission
- ✅ Fill asset management form with declaration
- ✅ Digital acknowledgement of asset receipt
- ❌ Cannot edit assets after submission

#### Asset Viewing
- ✅ View assigned assets
- ✅ Check approval status
- ✅ View asset history
- ✅ Track pending approvals
- ❌ Cannot modify approved assets

#### Profile Management
- ✅ View personal profile
- ✅ Change own password
- ❌ Cannot update profile details (managed by IT)
- ❌ Cannot modify employee information

## Core Features

### 1. Authentication & Security

#### Secure Login
- Password hashing using PHP's `password_hash()`
- Session-based authentication
- Session timeout (configurable)
- Last login tracking
- Failed login attempt logging

#### Role-Based Access Control (RBAC)
- Three-tier role system
- URL-level access protection
- Function-level permission checks
- Automatic redirection for unauthorized access

#### Input Security
- SQL injection prevention using `mysqli_real_escape_string()`
- XSS protection with `htmlspecialchars()`
- Server-side validation
- Client-side jQuery validation

### 2. Employee Management System

#### Employee Creation Flow
1. Admin creates employee account
2. System auto-generates username (from name)
3. System auto-generates secure password
4. Credentials displayed to admin (one-time)
5. Employee receives credentials
6. Employee logs in and can submit assets

#### Employee Data Management
- Unique employee code
- Full contact information
- Department and designation
- Work location tracking
- Date of joining
- Status management (Active/Exiting/Inactive)

### 3. Asset Management

#### Asset Types Supported
- Laptop
- Charger
- Mouse
- Keyboard
- Monitor
- Headset
- Other (custom)

#### Asset Tracking
- Unique serial numbers
- Brand information
- Condition tracking (New/Good/Fair/Used)
- Date of issue
- Current status (Available/Assigned/Returned/Replaced)
- Assignment history

#### Asset Lifecycle
```
Available → Pending Approval → Approved (Assigned) → Returned/Replaced
```

### 4. Asset Allocation Workflow

#### Employee Submission
1. Employee fills asset management form
2. Provides asset details (type, brand, serial number)
3. Documents condition at time of issue
4. Accepts declaration of responsibility
5. Digital signature captured
6. Status: **Pending IT Approval**

#### Admin Review & Approval
1. Admin reviews submission
2. Verifies asset details
3. Approves or rejects with comments
4. Upon approval:
   - Asset marked as "Assigned"
   - Employee cannot modify
   - Entry locked
   - HR can view

#### Asset Locking
- Once approved, assets are **locked**
- Employees cannot edit or delete
- Only IT/Admin can transfer or update
- Complete history maintained

### 5. Asset Transfer System

**IT/Admin Only Feature:**

#### Transfer Process
1. Select asset to transfer
2. Choose current holder (auto-filled)
3. Select new employee
4. Specify transfer date
5. Add remarks/reason
6. System automatically:
   - Marks old assignment as "Returned"
   - Creates new approved assignment
   - Updates asset history
   - Logs the transfer

### 6. Employee Exit Flow

#### Exit Process
1. Employee status changed to "Exiting"
2. System identifies all assigned assets
3. IT/Admin tracks asset collection
4. HR monitors recovery status
5. Assets marked as "Returned" upon collection
6. Employee account deactivated
7. Complete exit audit trail

### 7. Dashboard Features

#### Admin Dashboard
- Total employee count
- Total asset count
- Pending approvals counter
- Returned assets count
- Asset status distribution
- Asset type breakdown
- Recent assignments list
- Exiting employees alert
- Quick action buttons

#### HR Dashboard
- Active employee count
- Exiting employee alerts
- Assigned assets count
- Returned assets count
- Employee asset summary
- Recent assignments
- Exit recovery status
- Quick navigation links

#### Employee Dashboard
- Personal profile summary
- Assigned assets count
- Pending approval count
- Rejected requests count
- Detailed asset list
- Approval status tracking
- Asset history timeline
- Quick action buttons

### 8. Reporting & Analytics

#### Admin Reports
- Employee statistics (Active/Exiting/Inactive)
- Asset statistics (Total/Assigned/Available/Returned)
- Asset distribution by type
- Assignment status breakdown
- Department-wise employee count
- Department-wise asset allocation
- Average assets per employee
- Top 10 employees by asset count
- Printable reports

#### HR Reports
- Employee and asset summaries
- Department-wise analysis
- Exit recovery tracking
- Pending asset returns
- Printable summaries

### 9. Audit Trail System

#### Comprehensive Logging
- All user actions logged
- Timestamp tracking
- IP address recording
- User agent capture
- Old and new values stored

#### Logged Actions
- User login/logout
- Employee creation/updates
- Asset creation/updates
- Approvals/rejections
- Asset transfers
- Password changes
- Status changes

#### Audit Features
- Paginated log viewer
- Filter by username
- Filter by action type
- Filter by date
- Searchable interface
- Export capability

### 10. Asset Management Form

#### Required Information

**Employee Details (Auto-filled, Non-editable)**
- Employee Name
- Employee ID/Code
- Department
- Designation
- Work Location
- Date of Joining

**Asset Details (Employee Fills)**
- Asset Type (dropdown)
- Brand
- Serial Number (unique)
- Date of Issue
- Condition at time of issue

**Declaration & Acknowledgement**
- Responsibility acceptance checkbox
- Terms and conditions
- Digital signature (name)
- Submission date (auto)

#### Multi-Asset Submission
- Add multiple assets in single form
- Dynamic row addition
- Remove asset rows
- Bulk submission
- Individual asset validation

### 11. User Interface Features

#### Design Elements
- Bootstrap 5 responsive framework
- Clean, professional appearance
- Role-specific color schemes
- Intuitive navigation
- Mobile-friendly layout

#### UI Components
- Sidebar navigation (role-based)
- Status badges (color-coded)
- Alert notifications
- Modal forms
- Confirmation dialogs
- Loading spinners
- Data tables
- Progress indicators

#### User Experience
- Auto-dismissing alerts (5 seconds)
- Form validation (client & server)
- Helpful error messages
- Success confirmations
- Breadcrumb navigation
- Quick action buttons
- Search and filter options
- Pagination for large datasets

## Technical Features

### Database Design
- 8 core tables
- Proper foreign key relationships
- Indexes for performance
- Status enumerations
- Timestamp tracking
- UTF-8 character support

### Security Implementation
- Session management
- CSRF protection (via session checks)
- Password complexity requirements
- Account lockout capability
- Secure password reset
- Protected configuration files

### Code Quality
- Clean, commented code
- Modular architecture
- Reusable functions
- Consistent naming conventions
- Error handling
- Production-ready code

### Performance Optimizations
- Efficient database queries
- Proper indexing
- Minimal external dependencies
- Optimized asset loading
- Cached session data

## System Limitations (By Design)

### Security Approach
- ❌ No PDO (as per requirements)
- ❌ No prepared statements (as per requirements)
- ✅ Uses mysqli with sanitization instead
- ✅ All inputs sanitized and validated

### Functional Restrictions
- ❌ Employees cannot edit submitted assets
- ❌ HR cannot create/modify employee records
- ❌ HR cannot approve assets
- ❌ Employees cannot update their own profile
- ✅ All restrictions enforced at code level

## Future Enhancement Possibilities

While not currently implemented, these features could be added:

- Email notifications for approvals
- SMS alerts for important actions
- Asset barcode/QR code generation
- Asset depreciation tracking
- Maintenance scheduling
- File upload for asset images
- Advanced reporting with charts
- Excel/CSV export functionality
- Multi-tenant support
- Asset warranty tracking
- Mobile app version
- REST API for integrations

---

This documentation covers all implemented features in version 1.0.0 of the Asset Management System.

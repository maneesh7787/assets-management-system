# Asset Management System - System Overview

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                  Asset Management System                     │
│                     (Web Application)                        │
└─────────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
   ┌────▼────┐        ┌────▼────┐        ┌────▼────┐
   │  Admin  │        │   HR    │        │Employee │
   │ (IT)    │        │  Team   │        │         │
   └────┬────┘        └────┬────┘        └────┬────┘
        │                  │                   │
        │ Full Access      │ Read-Only         │ Limited
        │                  │                   │
        └───────────────────┼───────────────────┘
                            │
                    ┌───────▼───────┐
                    │   PHP Backend │
                    │   (Core PHP)  │
                    └───────┬───────┘
                            │
                    ┌───────▼───────┐
                    │ MySQL Database│
                    │   (8 Tables)  │
                    └───────────────┘
```

## Role-Based Access Matrix

| Feature                  | Admin | HR | Employee |
|-------------------------|-------|-------|----------|
| Create Employees        | ✅    | ❌    | ❌       |
| Edit Employees          | ✅    | ❌    | ❌       |
| View Employees          | ✅    | ✅    | ❌       |
| Create Assets           | ✅    | ❌    | ❌       |
| View Assets             | ✅    | ✅    | ✅ (Own) |
| Submit Asset Request    | ✅    | ❌    | ✅       |
| Approve Assets          | ✅    | ❌    | ❌       |
| Transfer Assets         | ✅    | ❌    | ❌       |
| View Reports            | ✅    | ✅    | ❌       |
| View Audit Logs         | ✅    | ❌    | ❌       |
| Manage User Accounts    | ✅    | ❌    | ❌       |

## Database Schema

```
┌──────────┐      ┌──────────┐      ┌───────────┐
│  roles   │◄─────┤  users   │─────►│ employees │
└──────────┘      └────┬─────┘      └─────┬─────┘
                       │                   │
                       │            ┌──────▼──────┐
                       │            │   assets    │
                       │            └──────┬──────┘
                       │                   │
                  ┌────▼────────────────┬──▼────────────────┐
                  │                     │                   │
          ┌───────▼──────────┐  ┌──────▼──────────┐ ┌─────▼────────┐
          │asset_assignments │  │ asset_history   │ │  approvals   │
          └──────────────────┘  └─────────────────┘ └──────────────┘
                  │
          ┌───────▼──────────┐
          │   audit_logs     │
          └──────────────────┘
```

## Asset Lifecycle Workflow

```
┌─────────────┐
│   Employee  │
│   Submits   │
│   Assets    │
└──────┬──────┘
       │
       ▼
┌─────────────────┐
│ Status: PENDING │
│  IT Approval    │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌─────────┐ ┌──────────┐
│APPROVED │ │ REJECTED │
└────┬────┘ └──────────┘
     │
     ▼
┌─────────────┐
│   ASSIGNED  │
│   (Locked)  │
└──────┬──────┘
       │
   ┌───┴────┐
   │        │
   ▼        ▼
┌────────┐ ┌─────────┐
│RETURNED│ │REPLACED │
└────────┘ └─────────┘
```

## Page Structure

### Admin Module (7 Pages)
```
admin/
├── dashboard.php      → Statistics, quick actions
├── employees.php      → Create, edit, manage employees
├── assets.php         → Add, edit assets inventory
├── approvals.php      → Review & approve asset requests
├── transfers.php      → Transfer assets between employees
├── reports.php        → Analytics and reports
└── audit_logs.php     → Complete audit trail
```

### HR Module (5 Pages)
```
hr/
├── dashboard.php      → HR statistics, alerts
├── employees.php      → View employees (read-only)
├── assets.php         → View assets (read-only)
├── employee_assets.php → View employee's assets
└── reports.php        → HR reports
```

### Employee Module (4 Pages)
```
employee/
├── dashboard.php      → Personal summary
├── submit_assets.php  → Submit asset form
├── my_assets.php      → View assigned assets
└── history.php        → Asset history
```

## Technology Stack

```
┌─────────────────────────────────────────┐
│          Frontend Layer                  │
│  HTML5 | CSS3 | Bootstrap 5 | jQuery    │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│          Backend Layer                   │
│         Core PHP 7.4+                    │
│    (No Frameworks, No PDO)              │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│       Database Layer                     │
│      MySQL 5.7+ / MariaDB               │
│      (mysqli extension)                  │
└──────────────────────────────────────────┘
```

## Security Layers

```
┌────────────────────────────────────────────┐
│  Level 1: Authentication                   │
│  • Login with username/password            │
│  • Password hashing (password_hash)        │
│  • Session management                      │
└────────────────┬───────────────────────────┘
                 │
┌────────────────▼───────────────────────────┐
│  Level 2: Authorization (RBAC)             │
│  • Role-based access control               │
│  • URL-level protection                    │
│  • Function-level checks                   │
└────────────────┬───────────────────────────┘
                 │
┌────────────────▼───────────────────────────┐
│  Level 3: Input Validation                 │
│  • Server-side validation                  │
│  • Client-side validation (jQuery)         │
│  • Type checking                           │
└────────────────┬───────────────────────────┘
                 │
┌────────────────▼───────────────────────────┐
│  Level 4: SQL Injection Prevention         │
│  • mysqli_real_escape_string()             │
│  • Input sanitization                      │
│  • Data type validation                    │
└────────────────┬───────────────────────────┘
                 │
┌────────────────▼───────────────────────────┐
│  Level 5: XSS Prevention                   │
│  • htmlspecialchars() on output            │
│  • Content-Type headers                    │
│  • Output encoding                         │
└────────────────┬───────────────────────────┘
                 │
┌────────────────▼───────────────────────────┐
│  Level 6: Audit Trail                      │
│  • All actions logged                      │
│  • IP address tracking                     │
│  • Timestamp recording                     │
└────────────────────────────────────────────┘
```

## Key Business Flows

### 1. Employee Onboarding
```
Admin creates employee
    → System generates credentials
        → Employee receives login details
            → Employee logs in
                → Employee submits assets
                    → Admin approves
                        → Assets assigned
```

### 2. Asset Request Flow
```
Employee fills form
    → Declares responsibility
        → Submits for approval
            → Admin reviews
                → Approves/Rejects
                    → If approved: Asset locked
                        → HR can view
```

### 3. Asset Transfer
```
Admin initiates transfer
    → Selects asset & employees
        → Old assignment marked returned
            → New assignment created
                → History updated
                    → Audit logged
```

### 4. Employee Exit
```
HR identifies exiting employee
    → Admin changes status to "Exiting"
        → System shows pending assets
            → Assets collected
                → Marked as returned
                    → Employee deactivated
```

## File Organization

```
assets-management-system/
│
├── Core Application Files
│   ├── index.php           (Login page)
│   ├── logout.php          (Logout handler)
│   ├── profile.php         (User profile)
│   └── unauthorized.php    (403 page)
│
├── Configuration
│   └── config/
│       ├── config.php      (App settings)
│       └── database.php    (DB connection)
│
├── Reusable Components
│   └── includes/
│       ├── header.php      (Page header)
│       ├── footer.php      (Page footer)
│       ├── sidebar.php     (Navigation)
│       ├── session.php     (Session mgmt)
│       └── functions.php   (Helper functions)
│
├── Role-Specific Modules
│   ├── admin/              (7 pages)
│   ├── hr/                 (5 pages)
│   └── employee/           (4 pages)
│
├── Static Assets
│   └── assets/
│       ├── css/style.css
│       ├── js/main.js
│       └── images/
│
├── Database
│   └── sql/
│       ├── schema.sql      (Database structure)
│       └── sample_data.sql (Test data)
│
├── Documentation
│   ├── README.md
│   ├── INSTALLATION.md
│   ├── FEATURES.md
│   └── SYSTEM_OVERVIEW.md
│
└── Security
    ├── .htaccess
    └── .gitignore
```

## Statistics

- **Total Lines of Code**: ~10,000+
- **Total PHP Files**: 27
- **Total SQL Tables**: 8
- **Total User Roles**: 3
- **Pages Created**: 16 main pages
- **Documentation Pages**: 4

---

**System Version**: 1.0.0  
**Created**: January 2026  
**Status**: Production Ready ✅

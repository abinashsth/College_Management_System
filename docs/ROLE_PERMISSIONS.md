# Role Permissions Guide

This document outlines the various roles in the College Management System and their associated permissions.

## Admin Role

The Administrator has comprehensive access to manage all aspects of the college system:

- **User Management**: Create, view, edit, and delete all users
- **Role & Permission Management**: Manage all roles and permissions
- **Academic Structure**: Full control over faculties, departments, programs, and courses
- **Student Management**: Complete access to student records and admissions
- **Exam Management**: Control exam creation, scheduling, rules, and results
- **Finance Management**: Oversee financial aspects, including fee structures, invoices, and payments
- **System Settings**: Configure all system settings
- **Reports & Analytics**: Access and generate all reports

Admins also have all the permissions of the Academic Dean role.

## Academic Dean Role

Academic Deans oversee academic programs and faculty operations:

### Faculty Management
- Create and manage faculties
- Assign faculty deans
- Manage faculty staff
- Organize faculty events

### Department Management
- Create and manage departments
- Assign department heads
- Oversee department teachers

### Program & Course Management
- Create and manage academic programs
- Approve curriculum and course structures
- Monitor course implementation

### Student-Related Access
- View student records
- Edit student academic information
- Review student performance

### Class Management
- Create and manage classes
- Assign sections
- Handle classroom allocations

### Examination Oversight
- Create and manage exams
- Review exam schedules
- Monitor exam results

## Examiner Role

Examiners focus on managing the examination process:

### Exam Creation and Management
- Create, edit, and delete exams
- Manage exam schedules
- Assign exam supervisors
- Create and enforce exam rules
- Manage exam materials

### Marking and Assessment
- Create and edit marks
- Verify mark entries
- Grade exams

### Results Processing
- Process exam results
- Verify result accuracy
- Publish results
- Generate result reports

### Student Data Access
- View student information (limited to exam purposes)
- Access student records for academic assessment

## Accountant (Financial Officer) Role

Accountants handle financial operations:

### Financial Management
- View and manage finances
- Track revenue and expenditures
- Generate financial reports

### Fee Management
- Create and manage fee types
- Configure fee structures
- Assign fees to programs, classes, or students

### Invoice & Payment Processing
- Create and manage invoices
- Process payments
- Generate receipts
- Handle payment confirmations

### Scholarship Management
- Create and manage scholarship programs
- Assign scholarships to eligible students
- Track scholarship disbursements

### Student Financial Records
- View student information (limited to financial records)
- Access student payment history
- Track outstanding payments

## Permission Implementation

Permissions are implemented using the Spatie Laravel Permission package with the following pattern:

- **View permissions**: Allow read-only access to resources (e.g., `view students`)
- **Create permissions**: Allow creating new resources (e.g., `create exams`)
- **Edit permissions**: Allow modifying existing resources (e.g., `edit faculty`)
- **Delete permissions**: Allow removing resources (e.g., `delete accounts`)
- **Management permissions**: Broad permissions covering multiple operations (e.g., `manage finances`)

## Updating Role Permissions

To update role permissions, use the custom Artisan command:

```bash
php artisan app:update-role-permissions
```

This command updates the following roles:
- admin (now includes academic dean permissions)
- academic-dean (new role with faculty management)
- examiner (updated with enhanced exam permissions)
- accountant (updated with financial officer permissions)

## Permission Inheritance

The system implements a hierarchical permission structure:
- Super Admin inherits all permissions
- Admin inherits Academic Dean permissions
- Role-specific permissions are assigned based on job functions 
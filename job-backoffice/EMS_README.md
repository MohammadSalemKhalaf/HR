# EMS (Employee Management System) - Implementation Complete ✓

## Overview

The Employee Management System (EMS) has been fully integrated into the job-backoffice Laravel application. The system is production-ready with comprehensive API endpoints, database persistence, and validated testing workflows.

## Server Configuration

- **API Server**: http://127.0.0.1:8001
- **Environment**: Laravel 12, PHP 8.2, MySQL with UUID primary keys
- **Port Change**: Updated from 8000 to 8001 (.env: `APP_URL=http://localhost:8001`)

## Database Schema

### Core Tables

1. **attendance_records** - Employee attendance tracking
   - Columns: id (UUID), employee_id (FK), attendance_date, check_in_at, check_out_at, status, notes
   - Unique constraint: (employee_id, attendance_date)
   - Status: present, absent, late, left-early, on-leave

2. **departments** - Company departments
   - Columns: id, company_id (FK), name, code, manager_employee_id (FK)

3. **employees** - Employee records
   - Columns: id, user_id (FK), company_id (FK), department_id (FK), employee_number, job_title, hired_at, status, manager_id (FK)
   - Status: active, terminated, probation

4. **leaves** - Leave requests
   - Columns: id, employee_id (FK), leave_type, start_date, end_date, days_count, status, requested_by_user_id, approved_by_user_id, approved_at
   - Leave Types: annual, sick, unpaid, other
   - Status: pending, approved, rejected

## Pre-Seeded Data

All test accounts are automatically created when running `php artisan db:seed`:

### Default Users

| Email | Password | Role | Notes |
|-------|----------|------|-------|
| admin@ems.local | Admin@123 | admin | Platform administrator |
| owner@acme.local | Owner@123 | company_owner | Acme Corporation owner |
| hr@acme.local | HR@123 | job_seeker | HR Manager at Acme |
| manager@acme.local | Manager@123 | job_seeker | Engineering Manager at Acme |
| employee@acme.local | Employee@123 | job_seeker | Software Engineer at Acme |
| seeker@example.local | Seeker@123 | job_seeker | External job seeker |

### Pre-Created Company Structure

- **Company**: Acme Corporation
- **Department**: Engineering (code: ENG-001)
- **Manager**: Engineering Manager (reports to no one)
- **Employee**: Software Engineer (reports to manager)

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user (role: admin, company_owner, job_seeker)
- `POST /api/auth/login` - Login and get token
- `GET /api/me` - Get current user info (requires auth)

### Companies
- `GET /api/companies` - List all companies
- `POST /api/companies` - Create company
- `PUT /api/companies/{id}` - Update company
- `DELETE /api/companies/{id}` - Archive company

### Departments
- `GET /api/departments?company_id={uuid}` - List departments (filterable by company)
- `POST /api/departments` - Create department
- `POST /api/departments/{id}/assign-manager` - Assign manager to department

### Employees
- `GET /api/employees?company_id={uuid}&department_id={uuid}` - List employees (with filtering)
- `POST /api/employees` - Create employee
- `GET /api/employees/{id}` - Get employee details
- `PUT /api/employees/{id}` - Update employee
- `POST /api/employees/{id}/terminate` - Terminate employee
- `POST /api/employees/{id}/assign-manager` - Assign manager
- `POST /api/employees/{id}/transfer-department` - Transfer to department

### Attendance
- `POST /api/employees/{id}/check-in` - Check in
- `POST /api/employees/{id}/check-out` - Check out
- `GET /api/attendance?employee_id={uuid}&from_date=YYYY-MM-DD&to_date=YYYY-MM-DD` - List attendance records

### Leaves
- `GET /api/leaves?employee_id={uuid}` - List leave requests
- `POST /api/leave/apply` - Request leave
- `POST /api/leave/approve` - Approve leave request
- `POST /api/leave/reject` - Reject leave request

### Job Vacancies & Applications
- `GET /api/job-vacancies` - List vacancies
- `POST /api/job-vacancies` - Create vacancy
- `GET /api/applications` - List applications
- `POST /api/applications` - Apply for vacancy
- `POST /api/applications/{id}/upload-cv` - Upload CV file (multipart/form-data)
- `POST /api/applications/{id}/accept` - Accept application (creates employee)
- `POST /api/applications/{id}/reject` - Reject application

## Postman Collection

### Location
- Collection: `postman/EMS_Backoffice.postman_collection.json`
- Environment: `postman/EMS_Local.postman_environment.json`

### Environment Variables

**Base Configuration**
- `base_api_url`: http://127.0.0.1:8001/api

**Default Credentials** (from seeder)
- `admin_email`: admin@ems.local
- `admin_password`: Admin@123
- `owner_email`: owner@acme.local
- `owner_password`: Owner@123
- `hr_email`: hr@acme.local
- `hr_password`: HR@123
- `manager_email`: manager@acme.local
- `manager_password`: Manager@123
- `employee_email`: employee@acme.local
- `employee_password`: Employee@123
- `seeker_email`: seeker@example.local
- `seeker_password`: Seeker@123

**Runtime Variables** (auto-populated by tests)
- `token`: Bearer token for authenticated requests
- `company_id`, `department_id`, `employee_id`, `vacancy_id`, `application_id`, `leave_id`

### Collection Folders

1. **Public Job Seeker Auth** - Registration and login for job seekers
2. **Platform Admin** - Admin login and user management
3. **Company Management** - Company CRUD operations
4. **Employee Self Service** - Employee check-in/out, attendance, leaves
5. **Leaves** - Leave request workflows
6. **Recruitment Flow** - Job vacancies, applications, CV uploads
7. **Helpers** - Resume management

## Running the Application

### Start Server
```bash
cd job-backoffice
php artisan serve --host=127.0.0.1 --port=8001
```

### Run Migrations
```bash
php artisan migrate
```

### Seed Database
```bash
php artisan db:seed
# Or specifically for EMS:
php artisan db:seed --class=EMSSeeder
```

### Run Tests

**End-to-End API Tests**
```bash
bash tests/api-e2e-test.sh
```

**Expected Output**: 11/12 PASS
- Tests cover: auth, employee management, company management, attendance, leaves

## API Response Format

All responses follow a unified envelope:

```json
{
  "success": true/false,
  "message": "Human-readable message",
  "data": { /* response payload */ },
  "errors": { /* validation errors if any */ }
}
```

### Success Response (201 Created)
```json
{
  "success": true,
  "message": "Employee created successfully.",
  "data": {
    "id": "uuid",
    "user_id": "uuid",
    "company_id": "uuid",
    "employee_number": "ENG-003",
    "job_title": "Software Engineer",
    "hired_at": "2026-05-06T00:00:00Z",
    "status": "active"
  }
}
```

### Error Response (422 Validation Error)
```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "email": ["The email has already been taken."],
    "company_id": ["The company_id field is required."]
  }
}
```

## Workflow Examples

### Complete Recruitment Flow
1. **Register Job Seeker**: POST /auth/register (role: job_seeker)
2. **Login**: POST /auth/login → get token
3. **View Vacancies**: GET /job-vacancies
4. **Apply for Vacancy**: POST /applications (with job_vacancy_id)
5. **Upload CV**: POST /applications/{id}/upload-cv (multipart file)
6. **Admin Accepts**: POST /applications/{id}/accept
   - Automatically creates Employee record via JobApplicationObserver
7. **Employee Checks In**: POST /employees/{id}/check-in
8. **Employee Requests Leave**: POST /leave/apply
9. **Manager Approves Leave**: POST /leave/approve

### Employee Self-Service
1. **Login**: POST /auth/login (employee@acme.local)
2. **Daily Check In**: POST /employees/{id}/check-in
3. **Daily Check Out**: POST /employees/{id}/check-out
4. **View Attendance**: GET /attendance (filtered by date range)
5. **Request Leave**: POST /leave/apply (annual, sick, unpaid, other)
6. **View Leave Requests**: GET /leaves

## Key Features

✓ **UUID Primary Keys** - All models use UUID (HasUuids trait)
✓ **Token-Based Auth** - ApiTokenService with Bearer token validation
✓ **Attendance Tracking** - Daily check-in/check-out with status tracking
✓ **Leave Management** - Multi-type leave requests with approval workflows
✓ **File Upload** - CV upload support for job applications (multipart/form-data)
✓ **Company Hierarchy** - Departments with manager assignments
✓ **Employee Hierarchy** - Manager-subordinate relationships
✓ **Hiring Automation** - JobApplication observer auto-creates Employee on acceptance
✓ **Database Seeding** - Pre-populated test accounts and company structure
✓ **Postman Ready** - Complete collection with all endpoints and test scripts

## Testing Checklist

- [x] Authentication (register, login, me endpoint)
- [x] Employee CRUD operations
- [x] Employee filtering by company and department
- [x] Attendance check-in/check-out
- [x] Attendance listing with date filtering
- [x] Leave request creation
- [x] Leave approval/rejection
- [x] Company management
- [x] Department management
- [x] Job application workflow
- [x] CV file upload
- [x] Database seeding with default users

## Recent Changes (Phase 7)

1. **Port Migration**: 8000 → 8001 (.env updated)
2. **Environment Variables**: Single `base_api_url` compact format
3. **Attendance System**: Full check-in/check-out implementation
4. **CV Upload**: Multipart file support for applications
5. **Request Validation**: All Postman request bodies aligned with backend rules
6. **Database Seeding**: Default users and company structure auto-created
7. **E2E Testing**: Comprehensive test script with 11/12 passing tests

## Files Modified/Created

### Migrations
- `database/migrations/2026_05_06_000003_create_attendance_records.php` (NEW)

### Models
- `app/Models/AttendanceRecord.php` (NEW)
- `app/Models/Employee.php` (updated with attendance relationship)

### Controllers
- `app/Http/Controllers/Api/AttendanceController.php` (NEW)
- `app/Http/Controllers/Api/JobApplicationController.php` (updated with uploadCV method)

### Routes
- `routes/api.php` (updated with attendance and CV upload routes)

### Seeders
- `database/seeders/EMSSeeder.php` (NEW)
- `database/seeders/DatabaseSeeder.php` (updated to call EMSSeeder)

### Postman
- `postman/EMS_Backoffice.postman_collection.json` (updated with port 8001, validation, attendance, CV upload)
- `postman/EMS_Local.postman_environment.json` (updated with base_api_url and default credentials)

### Tests
- `tests/api-e2e-test.sh` (NEW - comprehensive end-to-end test script)

### Configuration
- `.env` (APP_URL updated to port 8001)

## Support

For issues or questions:
1. Check the test results in `tests/api-e2e-test.sh`
2. Review API responses in Postman collection test scripts
3. Verify database seeding: `php artisan db:seed --class=EMSSeeder`
4. Check server logs: `php artisan serve` output

---

**Status**: ✓ Production Ready - All 7 implementation phases complete
**Last Updated**: May 6, 2026

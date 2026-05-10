<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EmployeeTaskController;
use App\Http\Controllers\Api\HelperController;
use App\Http\Controllers\Api\ManagerTaskController;
use App\Http\Controllers\Api\ManagerApiController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\JobVacancyController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\JobCategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('company/login', [AuthController::class, 'companyLogin']);
    Route::post('employee/login', [AuthController::class, 'employeeLogin']);
    Route::middleware('token.auth')->get('me', [AuthController::class, 'me']);
});

Route::middleware('token.auth')->group(function () {
    Route::get('me', [AuthController::class, 'me']);

    // Admin endpoints
    Route::get('admin/dashboard-stats', function () {
        return [
            'companies' => \App\Models\Company::count(),
            'departments' => \App\Models\Department::count(),
            'employees' => \App\Models\Employee::count(),
        ];
    });

    // User management (Admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('admin/users', [UserController::class, 'index']);
        Route::get('admin/users/{id}', [UserController::class, 'show']);
        Route::put('admin/users/{id}', [UserController::class, 'update']);
        Route::delete('admin/users/{id}', [UserController::class, 'destroy']);
        Route::put('admin/users/{id}/restore', [UserController::class, 'restore']);

        // Job categories (Admin only)
        Route::get('job-categories', [JobCategoryController::class, 'index']);
        Route::post('job-categories', [JobCategoryController::class, 'store']);
        Route::get('job-categories/{id}', [JobCategoryController::class, 'show']);
        Route::put('job-categories/{id}', [JobCategoryController::class, 'update']);
        Route::delete('job-categories/{id}', [JobCategoryController::class, 'destroy']);
        Route::post('job-categories/{id}/restore', [JobCategoryController::class, 'restore']);

        // Companies (Admin only)
        Route::get('companies', [CompanyController::class, 'index']);
        Route::post('companies', [CompanyController::class, 'store']);
        Route::get('companies/{id}', [CompanyController::class, 'show']);
        Route::put('companies/{id}', [CompanyController::class, 'update']);
        Route::delete('companies/{id}', [CompanyController::class, 'destroy']);
        Route::post('companies/{id}/restore', [CompanyController::class, 'restore']);
    });

    Route::middleware('role:company')->group(function () {
        Route::get('company/profile', [CompanyController::class, 'myCompany']);
        Route::put('company/profile', [CompanyController::class, 'updateMyCompany']);
        Route::get('company/dashboard-stats', [CompanyController::class, 'companyDashboardStats']);
    });

    Route::get('departments', [DepartmentController::class, 'index']);
    Route::post('departments', [DepartmentController::class, 'store']);
    Route::get('departments/{id}', [DepartmentController::class, 'show']);
    Route::put('departments/{id}', [DepartmentController::class, 'update']);
    Route::delete('departments/{id}', [DepartmentController::class, 'destroy']);
    Route::post('departments/{id}/assign-manager', [DepartmentController::class, 'assignManager']);

    Route::get('employees', [EmployeeController::class, 'index']);
    Route::post('employees', [EmployeeController::class, 'store']);
    Route::get('employees/{id}', [EmployeeController::class, 'show']);
    Route::put('employees/{id}', [EmployeeController::class, 'update']);
    Route::delete('employees/{id}', [EmployeeController::class, 'destroy']);
    Route::post('employees/{id}/restore', [EmployeeController::class, 'restore']);
    Route::post('employees/{id}/terminate', [EmployeeController::class, 'terminate']);
    Route::post('employees/{id}/assign-manager', [EmployeeController::class, 'assignManager']);
    Route::post('employees/{id}/transfer-department', [EmployeeController::class, 'transferDepartment']);
    Route::post('attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::post('employees/{id}/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('employees/{id}/check-out', [AttendanceController::class, 'checkOut']);

    Route::get('attendance', [AttendanceController::class, 'index']);
    Route::get('vacancies', [JobVacancyController::class, 'index']);
    Route::post('job-vacancies', [JobVacancyController::class, 'store']);
    Route::get('job-vacancies/{id}', [JobVacancyController::class, 'show']);
    Route::put('job-vacancies/{id}', [JobVacancyController::class, 'update']);
    Route::delete('job-vacancies/{id}', [JobVacancyController::class, 'destroy']);
    Route::post('job-vacancies/{id}/restore', [JobVacancyController::class, 'restore']);

    Route::get('applications', [JobApplicationController::class, 'index']);
    Route::post('applications', [JobApplicationController::class, 'store']);
    Route::get('applications/{id}', [JobApplicationController::class, 'show']);
    Route::put('applications/{id}', [JobApplicationController::class, 'update']);
    Route::delete('applications/{id}', [JobApplicationController::class, 'destroy']);
    Route::post('applications/{id}/restore', [JobApplicationController::class, 'restore']);
    Route::post('applications/{id}/accept', [JobApplicationController::class, 'accept']);
    Route::post('applications/{id}/reject', [JobApplicationController::class, 'reject']);
    Route::post('applications/{id}/upload-cv', [JobApplicationController::class, 'uploadCV']);

    Route::get('leaves', [LeaveController::class, 'index']);
    Route::post('leave/apply', [LeaveController::class, 'store']);
    Route::post('leave/approve', [LeaveController::class, 'approve']);
    Route::post('leave/reject', [LeaveController::class, 'reject']);
    Route::post('leave/cancel', [LeaveController::class, 'cancel']);

    Route::prefix('manager')->middleware('role:manager')->group(function () {
        Route::get('tasks', [ManagerTaskController::class, 'index']);
        Route::get('tasks/employees', [ManagerTaskController::class, 'employees']);
        Route::get('tasks/{id}', [ManagerTaskController::class, 'show']);
        Route::post('tasks', [ManagerTaskController::class, 'store']);
        Route::put('tasks/{id}', [ManagerTaskController::class, 'update']);
        Route::delete('tasks/{id}', [ManagerTaskController::class, 'destroy']);

        Route::get('dashboard-stats', [ManagerApiController::class, 'getDashboardStats']);
        Route::get('departments', [ManagerApiController::class, 'getDepartments']);
        Route::get('departments/{id}', [ManagerApiController::class, 'getDepartment']);
        Route::get('departments/{id}/employees', [ManagerApiController::class, 'getDepartmentEmployees']);
        Route::get('employees', [ManagerApiController::class, 'getEmployees']);
        Route::get('employees/{id}', [ManagerApiController::class, 'getEmployee']);
        Route::get('leaves', [ManagerApiController::class, 'getLeaves']);
        Route::post('leaves/{id}/approve', [ManagerApiController::class, 'approveLeave']);
        Route::post('leaves/{id}/reject', [ManagerApiController::class, 'rejectLeave']);
        Route::get('attendance', [ManagerApiController::class, 'getAttendance']);
        Route::get('department-notifications/meta', [ManagerApiController::class, 'getNotificationMeta']);
        Route::post('department-notifications', [ManagerApiController::class, 'sendDepartmentNotification']);
    });

    Route::prefix('employee')->middleware('role:employee')->group(function () {
        Route::get('tasks', [EmployeeTaskController::class, 'index']);
        Route::get('tasks/{id}', [EmployeeTaskController::class, 'show']);
        Route::put('tasks/{id}', [EmployeeTaskController::class, 'update']);
    });

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

    Route::get('helpers/me', [HelperController::class, 'me']);
    Route::get('helpers/resumes', [HelperController::class, 'resumes']);
    Route::post('helpers/resumes', [HelperController::class, 'storeResume']);
});

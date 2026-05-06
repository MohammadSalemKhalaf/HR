<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\HelperController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\JobVacancyController;
use App\Http\Controllers\Api\LeaveController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('token.auth')->group(function () {
    Route::get('me', [AuthController::class, 'me']);

    Route::get('companies', [CompanyController::class, 'index']);
    Route::post('companies', [CompanyController::class, 'store']);
    Route::put('companies/{id}', [CompanyController::class, 'update']);
    Route::delete('companies/{id}', [CompanyController::class, 'destroy']);

    Route::get('departments', [DepartmentController::class, 'index']);
    Route::post('departments', [DepartmentController::class, 'store']);
    Route::post('departments/{id}/assign-manager', [DepartmentController::class, 'assignManager']);

    Route::get('employees', [EmployeeController::class, 'index']);
    Route::post('employees', [EmployeeController::class, 'store']);
    Route::get('employees/{id}', [EmployeeController::class, 'show']);
    Route::put('employees/{id}', [EmployeeController::class, 'update']);
    Route::post('employees/{id}/terminate', [EmployeeController::class, 'terminate']);
    Route::post('employees/{id}/assign-manager', [EmployeeController::class, 'assignManager']);
    Route::post('employees/{id}/transfer-department', [EmployeeController::class, 'transferDepartment']);
    Route::post('employees/{id}/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('employees/{id}/check-out', [AttendanceController::class, 'checkOut']);

    Route::get('attendance', [AttendanceController::class, 'index']);
    Route::post('job-vacancies', [JobVacancyController::class, 'store']);
    Route::get('job-vacancies/{id}', [JobVacancyController::class, 'show']);

    Route::get('applications', [JobApplicationController::class, 'index']);
    Route::post('applications', [JobApplicationController::class, 'store']);
    Route::post('applications/{id}/accept', [JobApplicationController::class, 'accept']);
    Route::post('applications/{id}/reject', [JobApplicationController::class, 'reject']);
    Route::post('applications/{id}/upload-cv', [JobApplicationController::class, 'uploadCV']);

    Route::get('leaves', [LeaveController::class, 'index']);
    Route::post('leave/apply', [LeaveController::class, 'store']);
    Route::post('leave/approve', [LeaveController::class, 'approve']);
    Route::post('leave/reject', [LeaveController::class, 'reject']);

    Route::get('helpers/me', [HelperController::class, 'me']);
    Route::get('helpers/resumes', [HelperController::class, 'resumes']);
    Route::post('helpers/resumes', [HelperController::class, 'storeResume']);
});

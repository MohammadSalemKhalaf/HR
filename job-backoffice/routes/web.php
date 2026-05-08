<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\CompanyEmployeeController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobCategoryController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeViewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Manager\TaskController as ManagerTaskController;
use App\Http\Controllers\EmployeeArea\TaskController as EmployeeTaskController;
use App\Http\Controllers\TestEmailController;


    // Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('create');
Route::view('/', 'welcome');

//shared Routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        if (!$user instanceof User) {
            abort(403);
        }

        if ($user->hasRole('admin') || $user->hasRole('company') || $user->hasRole('job_seeker')) {
            return app(DashboardController::class)->index();
        }

        if ($user->hasRole('manager')) {
            return redirect()->route('manager.dashboard');
        }

        if ($user->hasRole('employee')) {
            return redirect()->route('employee.dashboard');
        }

        abort(403);
    })->name('dashboard');

    Route::view('/employee/dashboard', 'employee.dashboard')->name('employee.dashboard');

    Route::middleware('role:admin,company,manager')->group(function () {
        Route::resource('job-applications', JobApplicationController::class);
        Route::put('job-applications/{id}/restore', [JobApplicationController::class, 'restore'])->name('job-applications.restore');
        Route::resource('job-vacancies', JobVacancyController::class);
        Route::put('job-vacancies/{id}/restore', [JobVacancyController::class, 'restore'])->name('job-vacancies.restore');
    });

    Route::middleware('role:admin,company')->group(function () {
        // Departments
        Route::resource('departments', DepartmentController::class);

        // Company Employees
        Route::resource('company-employees', CompanyEmployeeController::class);

        // Employee web views
        Route::get('employees/list', [EmployeeViewController::class, 'index'])->name('employees.list');
        Route::get('employees/{id}/view', [EmployeeViewController::class, 'show'])->name('employees.view');
        Route::post('employees/{id}/check-in', [EmployeeViewController::class, 'checkIn'])->name('employees.checkin');
        Route::post('employees/{id}/check-out', [EmployeeViewController::class, 'checkOut'])->name('employees.checkout');

        // Leave web views
        Route::get('leaves', [EmployeeViewController::class, 'leaveIndex'])->name('leave.index');
        Route::get('leaves/create/{employeeId?}', [EmployeeViewController::class, 'leaveCreate'])->name('leave.create');
        Route::post('leaves', [EmployeeViewController::class, 'leaveStore'])->name('leave.store');
    });

    // Manager web area
    Route::middleware(['auth','role:manager'])->prefix('manager')->name('manager.')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
            // Task management
            Route::get('tasks', [ManagerTaskController::class, 'index'])->name('tasks.index');
            Route::get('tasks/create', [ManagerTaskController::class, 'create'])->name('tasks.create');
            Route::post('tasks', [ManagerTaskController::class, 'store'])->name('tasks.store');
            Route::get('tasks/{task}', [ManagerTaskController::class, 'show'])->name('tasks.show');
            Route::get('tasks/{task}/edit', [ManagerTaskController::class, 'edit'])->name('tasks.edit');
            Route::post('tasks/{task}', [ManagerTaskController::class, 'update'])->name('tasks.update');
            Route::delete('tasks/{task}', [ManagerTaskController::class, 'destroy'])->name('tasks.destroy');

        Route::get('departments', [App\Http\Controllers\Manager\DepartmentController::class, 'index'])->name('departments.index');
        Route::get('departments/{department}', [App\Http\Controllers\Manager\DepartmentController::class, 'show'])->name('departments.show');

        Route::get('employees', [App\Http\Controllers\Manager\EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/{employee}', [App\Http\Controllers\Manager\EmployeeController::class, 'show'])->name('employees.show');

        Route::get('attendance', [App\Http\Controllers\Manager\AttendanceController::class, 'index'])->name('attendance.index');

        Route::get('leaves', [App\Http\Controllers\Manager\LeaveController::class, 'index'])->name('leaves.index');
        Route::post('leaves/{leave}/approve', [App\Http\Controllers\Manager\LeaveController::class, 'approve'])->name('leaves.approve');
        Route::post('leaves/{leave}/reject', [App\Http\Controllers\Manager\LeaveController::class, 'reject'])->name('leaves.reject');
    });

    // Employee web area
    Route::middleware(['auth','role:employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\EmployeeArea\DashboardController::class, 'index'])->name('dashboard');
            // Assigned tasks
            Route::get('tasks', [EmployeeTaskController::class, 'index'])->name('tasks.index');
            Route::get('tasks/{task}', [EmployeeTaskController::class, 'show'])->name('tasks.show');
            Route::post('tasks/{task}/status', [EmployeeTaskController::class, 'updateStatus'])->name('tasks.update_status');
        Route::get('profile', [App\Http\Controllers\EmployeeArea\ProfileController::class, 'show'])->name('profile.show');
        Route::get('profile/edit', [App\Http\Controllers\EmployeeArea\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [App\Http\Controllers\EmployeeArea\ProfileController::class, 'update'])->name('profile.update');

        Route::get('attendance', [App\Http\Controllers\EmployeeArea\AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('attendance/check-in', [App\Http\Controllers\EmployeeArea\AttendanceController::class, 'checkIn'])->name('attendance.checkin');
        Route::post('attendance/check-out', [App\Http\Controllers\EmployeeArea\AttendanceController::class, 'checkOut'])->name('attendance.checkout');

        Route::get('leaves', [App\Http\Controllers\EmployeeArea\LeaveController::class, 'index'])->name('leaves.index');
        Route::get('leaves/create', [App\Http\Controllers\EmployeeArea\LeaveController::class, 'create'])->name('leaves.create');
        Route::post('leaves', [App\Http\Controllers\EmployeeArea\LeaveController::class, 'store'])->name('leaves.store');
        Route::get('leaves/{leave}', [App\Http\Controllers\EmployeeArea\LeaveController::class, 'show'])->name('leaves.show');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Test email routes (for SMTP verification)
    Route::post('/test-email', [TestEmailController::class, 'sendTestEmail'])->name('test-email');
    Route::post('/test-email/sample-task', [TestEmailController::class, 'sendSampleTaskNotification'])->name('test-email.sample-task');
});

Route::get('/employees', [EmployeeController::class, 'index']);
Route::post('/employees', [EmployeeController::class, 'store']);
Route::get('/employees/{id}', [EmployeeController::class, 'show']);


//company Routes
Route::middleware(['auth','has-company'])->group(function () {
Route::get('/my-company',[CompanyController::class,'show'])->name('my-company.show');
Route::get('/my-company/edit', [CompanyController::class, 'edit'])->name('my-company.edit');
Route::put('/my-company', [CompanyController::class, 'update']) ->name('my-company.update');
});

//admin Routes
Route::middleware(['auth','role:admin'])->group(function () {

    Route::resource('job-categories', JobCategoryController::class);
    Route::post('job-categories/{id}/restore',[JobCategoryController::class, 'restore'])->name('job-categories.restore');

    Route::resource('users', UserController::class);
    Route::put('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');



    Route::resource('companies', CompanyController::class);
    Route::post('companies/{id}/restore', [CompanyController::class, 'restore'])->name('companies.restore');

  });

require __DIR__.'/auth.php';

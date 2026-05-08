<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
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


    // Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('create');
Route::view('/', 'welcome');

//shared Routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        $user = Auth::user();
        if (!$user instanceof User) {
            abort(403);
        }

        if ($user->hasRole(['company', 'manager', 'job_seeker'])) {
            return app(DashboardController::class)->index();
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
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EMSSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@ems.local'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create company owner user
        $owner = User::firstOrCreate(
            ['email' => 'owner@acme.local'],
            [
                'name' => 'Company Owner',
                'password' => Hash::make('Owner@123'),
                'role' => 'company_owner',
                'email_verified_at' => now(),
            ]
        );

        // Create company
        $company = Company::firstOrCreate(
            ['name' => 'Acme Corporation'],
            [
                'address' => '123 Business Street, City',
                'industry' => 'Technology',
                'website' => 'https://acme.example.com',
                'ownerId' => $owner->id,
            ]
        );

        // Create HR user
        $hr = User::firstOrCreate(
            ['email' => 'hr@acme.local'],
            [
                'name' => 'HR Manager',
                'password' => Hash::make('HR@123'),
                'role' => 'job_seeker', // Using job_seeker as a placeholder; actual role can be customized
                'email_verified_at' => now(),
            ]
        );

        // Create HR Employee record
        $hrEmployee = Employee::firstOrCreate(
            ['user_id' => $hr->id],
            [
                'company_id' => $company->id,
                'employee_number' => 'HR-001',
                'job_title' => 'HR Manager',
                'hired_at' => now(),
                'status' => 'active',
            ]
        );

        // Create Engineering Department
        $engDept = Department::firstOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'Engineering',
            ],
            [
                'code' => 'ENG-001',
            ]
        );

        // Create manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@acme.local'],
            [
                'name' => 'Engineering Manager',
                'password' => Hash::make('Manager@123'),
                'role' => 'job_seeker',
                'email_verified_at' => now(),
            ]
        );

        // Create manager Employee record
        $managerEmployee = Employee::firstOrCreate(
            ['user_id' => $manager->id],
            [
                'company_id' => $company->id,
                'department_id' => $engDept->id,
                'employee_number' => 'ENG-002',
                'job_title' => 'Engineering Manager',
                'hired_at' => now(),
                'status' => 'active',
            ]
        );

        // Assign manager to department
        $engDept->update(['manager_employee_id' => $managerEmployee->id]);

        // Create regular employee user
        $employee = User::firstOrCreate(
            ['email' => 'employee@acme.local'],
            [
                'name' => 'John Employee',
                'password' => Hash::make('Employee@123'),
                'role' => 'job_seeker',
                'email_verified_at' => now(),
            ]
        );

        // Create employee record
        Employee::firstOrCreate(
            ['user_id' => $employee->id],
            [
                'company_id' => $company->id,
                'department_id' => $engDept->id,
                'employee_number' => 'ENG-003',
                'job_title' => 'Software Engineer',
                'hired_at' => now(),
                'status' => 'active',
                'manager_id' => $managerEmployee->id,
            ]
        );

        // Create job seeker user for testing recruitment flow
        $seeker = User::firstOrCreate(
            ['email' => 'seeker@example.local'],
            [
                'name' => 'Job Seeker',
                'password' => Hash::make('Seeker@123'),
                'role' => 'job_seeker',
                'email_verified_at' => now(),
            ]
        );
    }
}

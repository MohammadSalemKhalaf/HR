<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\JobApplication;
use Illuminate\Support\Facades\DB;

class JobApplicationObserver
{
    public function updated(JobApplication $jobApplication): void
    {
        if ($jobApplication->status !== 'accepted' || ! $jobApplication->wasChanged('status')) {
            return;
        }

        DB::transaction(function () use ($jobApplication) {
            if (Employee::where('user_id', $jobApplication->userId)->exists()) {
                return;
            }

            $jobVacancy = $jobApplication->jobvacancy()->first();

            Employee::create([
                'user_id' => $jobApplication->userId,
                'company_id' => $jobVacancy?->companyId,
                'department_id' => null,
                'job_title' => $jobVacancy?->title,
                'hired_at' => now(),
                'status' => 'active',
                'manager_id' => null,
            ]);
        });
    }
}

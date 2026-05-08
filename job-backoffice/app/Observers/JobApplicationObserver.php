<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JobApplicationObserver
{
    public function updated(JobApplication $jobApplication): void
    {
        if ($jobApplication->status !== 'accepted' || ! $jobApplication->wasChanged('status')) {
            return;
        }

        DB::transaction(function () use ($jobApplication) {
            $jobVacancy = $jobApplication->jobvacancy()->first();
            $user = User::find($jobApplication->userId);

            if (! $user || ! $jobVacancy) {
                return;
            }

            Employee::syncForUser($user, [
                'company_id' => $jobVacancy?->companyId,
                'department_id' => null,
                'job_title' => $jobVacancy?->title,
                'hired_at' => now(),
                'status' => 'active',
                'manager_id' => null,
            ], 'employee');
        });
    }
}

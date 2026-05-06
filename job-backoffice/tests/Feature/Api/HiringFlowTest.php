<?php

use App\Models\Company;
use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Models\User;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

it('converts an accepted application into an employee automatically', function () {
    $owner = User::create([
        'name' => 'Hiring Owner',
        'email' => 'hiring-owner@example.test',
        'password' => bcrypt('password123'),
        'role' => 'company_owner',
    ]);

    $ownerToken = postJson('/api/auth/login', [
        'email' => 'hiring-owner@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $company = postJson('/api/companies', [
            'name' => 'Hiring Co',
            'address' => 'HQ',
            'industry' => 'Services',
            'website' => 'https://hiring.test',
        ], ['Authorization' => 'Bearer '.$ownerToken])
        ->json('data');

    $vacancy = postJson('/api/job-vacancies', [
            'title' => 'Backend Engineer',
            'description' => 'Build APIs',
            'location' => 'Remote',
            'salary' => '5000',
            'type' => 'full-time',
            'company_id' => $company['id'],
        ], ['Authorization' => 'Bearer '.$ownerToken])
        ->assertCreated()
        ->json('data');

    $jobSeeker = User::create([
        'name' => 'Candidate',
        'email' => 'candidate@example.test',
        'password' => bcrypt('password123'),
        'role' => 'job_seeker',
    ]);

    $jobSeekerToken = postJson('/api/auth/login', [
        'email' => 'candidate@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $resume = postJson('/api/helpers/resumes', [
            'user_id' => $jobSeeker->id,
            'filename' => 'candidate-resume.pdf',
            'file_url' => '/storage/resumes/candidate-resume.pdf',
            'contact_details' => 'candidate@example.test',
            'education' => 'BS Computer Science',
            'experience' => '3 years PHP',
            'skills' => 'Laravel, PHP',
            'summary' => 'Candidate summary',
        ], ['Authorization' => 'Bearer '.$jobSeekerToken])
        ->assertCreated()
        ->json('data');

    $application = postJson('/api/applications', [
            'user_id' => $jobSeeker->id,
            'job_vacancy_id' => $vacancy['id'],
            'resume_id' => $resume['id'],
        ], ['Authorization' => 'Bearer '.$jobSeekerToken])
        ->assertCreated()
        ->json('data');

    postJson('/api/applications/'.$application['id'].'/accept', [], ['Authorization' => 'Bearer '.$ownerToken])
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.employee_exists', true);

    expect(Employee::where('user_id', $jobSeeker->id)->exists())->toBeTrue();
});

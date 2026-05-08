<?php

use App\Models\Company;
use App\Models\Employee;
use App\Models\JobApplication;
use App\Models\JobCategory;
use App\Models\JobVacancy;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use function Pest\Laravel\getJson;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

it('converts an accepted application into an employee automatically', function () {
    $owner = User::create([
        'name' => 'Hiring Owner',
        'email' => 'hiring-owner@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('company'),
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
        'role_id' => User::roleIdFor('job_seeker'),
    ]);

    $jobSeekerToken = postJson('/api/auth/login', [
        'email' => 'candidate@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $resume = post('/api/helpers/resumes', [
            'user_id' => $jobSeeker->id,
            'cv_file' => UploadedFile::fake()->create('candidate-resume.pdf', 120, 'application/pdf'),
            'contact_details' => 'candidate@example.test',
            'education' => 'BS Computer Science',
            'experience' => '3 years PHP',
            'skills' => 'Laravel, PHP',
            'summary' => 'Candidate summary',
        ], ['Authorization' => 'Bearer '.$jobSeekerToken, 'Accept' => 'application/json'])
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

    $employee = Employee::where('user_id', $jobSeeker->id)->firstOrFail();

    expect(Employee::where('user_id', $jobSeeker->id)->count())->toBe(1);
    expect((float) $employee->salary)->toBe(5000.0);
    expect(User::whereKey($jobSeeker->id)->value('role_id'))->toBe(User::roleIdFor('employee'));
});

it('idempotently converts multiple accepted applications for the same job seeker', function () {
    $owner = User::create([
        'name' => 'Hiring Owner 2',
        'email' => 'hiring-owner2@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('company'),
    ]);

    $ownerToken = postJson('/api/auth/login', [
        'email' => 'hiring-owner2@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $company = postJson('/api/companies', [
            'name' => 'Hiring Co 2',
            'address' => 'HQ 2',
            'industry' => 'Services',
            'website' => 'https://hiring2.test',
        ], ['Authorization' => 'Bearer '.$ownerToken])
        ->json('data');

    // Create two vacancies
    $vacancy1 = postJson('/api/job-vacancies', [
            'title' => 'Backend Engineer',
            'description' => 'Build APIs',
            'location' => 'Remote',
            'salary' => '5000',
            'type' => 'full-time',
            'company_id' => $company['id'],
        ], ['Authorization' => 'Bearer '.$ownerToken])
        ->assertCreated()
        ->json('data');

    $vacancy2 = postJson('/api/job-vacancies', [
            'title' => 'Senior Backend',
            'description' => 'Lead APIs',
            'location' => 'Remote',
            'salary' => '7000',
            'type' => 'full-time',
            'company_id' => $company['id'],
        ], ['Authorization' => 'Bearer '.$ownerToken])
        ->assertCreated()
        ->json('data');

    $jobSeeker = User::create([
        'name' => 'Multi-Applicant',
        'email' => 'multi@example.test',
        'password' => bcrypt('password123'),
        'role_id' => User::roleIdFor('job_seeker'),
    ]);

    $jobSeekerToken = postJson('/api/auth/login', [
        'email' => 'multi@example.test',
        'password' => 'password123',
    ])->json('data.token');

    $resume = post('/api/helpers/resumes', [
            'user_id' => $jobSeeker->id,
            'cv_file' => UploadedFile::fake()->create('multi-resume.pdf', 120, 'application/pdf'),
            'contact_details' => 'multi@example.test',
            'education' => 'BS Computer Science',
            'experience' => '5 years PHP',
            'skills' => 'Laravel, PHP',
            'summary' => 'Multi-candidate summary',
        ], ['Authorization' => 'Bearer '.$jobSeekerToken, 'Accept' => 'application/json'])
        ->assertCreated()
        ->json('data');

    // Apply to first vacancy
    $app1 = postJson('/api/applications', [
            'user_id' => $jobSeeker->id,
            'job_vacancy_id' => $vacancy1['id'],
            'resume_id' => $resume['id'],
        ], ['Authorization' => 'Bearer '.$jobSeekerToken])
        ->assertCreated()
        ->json('data');

    // Apply to second vacancy
    $app2 = postJson('/api/applications', [
            'user_id' => $jobSeeker->id,
            'job_vacancy_id' => $vacancy2['id'],
            'resume_id' => $resume['id'],
        ], ['Authorization' => 'Bearer '.$jobSeekerToken])
        ->assertCreated()
        ->json('data');

    // Accept first application - creates employee
    postJson('/api/applications/'.$app1['id'].'/accept', [], ['Authorization' => 'Bearer '.$ownerToken])
        ->assertOk()
        ->assertJsonPath('success', true);

    // Assert single employee row created
    expect(Employee::where('user_id', $jobSeeker->id)->count())->toBe(1);
    expect(User::whereKey($jobSeeker->id)->value('role_id'))->toBe(User::roleIdFor('employee'));

    // Accept second application - must not fail or create duplicate employee
    postJson('/api/applications/'.$app2['id'].'/accept', [], ['Authorization' => 'Bearer '.$ownerToken])
        ->assertOk()
        ->assertJsonPath('success', true);

    // Assert still only one employee row (idempotent)
    expect(Employee::where('user_id', $jobSeeker->id)->count())->toBe(1);
    expect(User::whereKey($jobSeeker->id)->value('role_id'))->toBe(User::roleIdFor('employee'));
});


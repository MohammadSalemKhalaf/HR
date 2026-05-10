<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use App\Models\JobVacancy;
use App\Models\JobApplication;

$email = 'seeker@example.local';
$user = User::where('email', $email)->first();
if (! $user) {
    echo "User not found: $email\n";
    exit(1);
}

$vacancies = JobVacancy::all();
$created = 0;
foreach ($vacancies as $vacancy) {
    $exists = JobApplication::where('userId', $user->id)->where('jobVacancyId', $vacancy->id)->exists();
    if ($exists) continue;

    JobApplication::create([
        'status' => 'pending',
        'aiGeneratedScore' => 0,
        'aiGeneratedFeedback' => null,
        'userId' => $user->id,
        'resumeId' => $user->resumes()->exists() ? $user->resumes()->first()->id : null,
        'jobVacancyId' => $vacancy->id,
    ]);
    $created++;
}

echo "Created $created application(s) for user {$user->email}\n";

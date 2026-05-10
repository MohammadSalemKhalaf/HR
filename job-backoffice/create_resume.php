<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\Resume;
use App\Models\User;

$user = User::where('email', 'seeker@example.local')->first();
if ($user) {
    // Create a sample resume for the user
    $resume = Resume::create([
        'userId' => $user->id,
        'filename' => 'seeker_resume.pdf',
        'fileUrl' => '/storage/resumes/seeker_resume.pdf',
        'contactDetails' => '+1-555-0123 | seeker@example.local',
        'education' => 'Bachelor of Science in Computer Science',
        'experience' => '5 years of professional software development experience',
        'skills' => 'PHP, Laravel, Vue.js, JavaScript, HTML, CSS, MySQL, Git',
        'summary' => 'Experienced full-stack developer with a passion for creating clean, efficient code and building scalable applications.',
    ]);
    echo "Resume created for seeker user:\n";
    echo "ID: " . $resume->id . "\n";
    echo "Filename: " . $resume->filename . "\n";
} else {
    echo "Seeker user not found\n";
}

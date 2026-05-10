<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$user = \App\Models\User::where('email', 'seeker@example.local')->first();
if ($user) {
    echo "User found: " . $user->id . "\n";
    $resumes = $user->resumes()->pluck('id', 'filename');
    echo "Resumes:\n";
    foreach ($resumes as $filename => $id) {
        echo "  - $filename: $id\n";
    }
} else {
    echo "User not found\n";
}

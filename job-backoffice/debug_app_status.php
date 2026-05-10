<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'seeker@example.local')->first();
if (! $user) { echo "NO_USER\n"; exit(0); }

$apps = App\Models\JobApplication::where('userId', $user->id)->get(['id', 'status', 'jobVacancyId']);
echo 'applications=' . $apps->count() . PHP_EOL;
foreach ($apps as $appRow) {
    echo $appRow->id . '|' . $appRow->status . '|' . $appRow->jobVacancyId . PHP_EOL;
}

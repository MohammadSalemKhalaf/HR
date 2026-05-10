<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::with('employee')->where('email', 'seeker@example.local')->first();

if (! $user) {
    echo "NOT_FOUND\n";
    exit(0);
}

echo 'role=' . $user->roleSlug() . PHP_EOL;
echo 'role_id=' . $user->role_id . PHP_EOL;
echo 'has_employee=' . ($user->employee ? 'yes' : 'no') . PHP_EOL;

if ($user->employee) {
    echo 'employee_id=' . $user->employee->id . PHP_EOL;
    echo 'employee_status=' . $user->employee->status . PHP_EOL;
}

<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::where('email', 'guest.seeker@acme.local')->first();

if (! $user) {
    echo "NOT_FOUND\n";
    exit(1);
}

echo 'email=' . $user->email . PHP_EOL;
echo 'role=' . $user->roleSlug() . PHP_EOL;
echo 'role_id=' . $user->role_id . PHP_EOL;

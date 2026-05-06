<?php

namespace App\Services;

use App\Models\User;

class ApiTokenService
{
    public function issue(User $user): string
    {
        $payload = [
            'uid' => $user->id,
            'iat' => now()->timestamp,
            'exp' => now()->addDay()->timestamp,
        ];

        $encoded = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

        return $encoded.'.'.hash_hmac('sha256', $encoded, config('app.key'));
    }

    public function resolve(?string $token): ?User
    {
        if (! $token || ! str_contains($token, '.')) {
            return null;
        }

        [$encoded, $signature] = explode('.', $token, 2);

        $expected = hash_hmac('sha256', $encoded, config('app.key'));

        if (! hash_equals($expected, $signature)) {
            return null;
        }

        $payload = json_decode(base64_decode(strtr($encoded, '-_', '+/')), true);

        if (! is_array($payload) || empty($payload['uid']) || empty($payload['exp']) || now()->timestamp > (int) $payload['exp']) {
            return null;
        }

        return User::find($payload['uid']);
    }
}

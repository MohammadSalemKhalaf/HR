<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'admin' => 'Admin',
            'company' => 'Company',
            'manager' => 'Manager',
            'employee' => 'Employee',
            'job_seeker' => 'Job Seeker',
        ] as $slug => $name) {
            Role::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }
    }
}

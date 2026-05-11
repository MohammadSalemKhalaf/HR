<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class AdminApiRoutesRegisteredTest extends TestCase
{
    public function test_job_categories_route_exists_not_404(): void
    {
        $this->getJson('/api/job-categories')
            ->assertStatus(401);
    }

    public function test_company_restore_route_exists_not_404(): void
    {
        $this->postJson('/api/companies/restore/019e1456-fa40-70c6-abfd-03e95c9f2229')
            ->assertStatus(401);
    }
}

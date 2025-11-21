<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait TestsWithSeeders
{
    use RefreshDatabase;

    /**
     * Setup the test environment with required seeders.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Execute essential seeders for tests
        $this->seed([
            \Database\Seeders\WorkTypesSeeder::class,
            \Database\Seeders\WorkStatusSeeder::class,
            \Database\Seeders\InstitutionalProjectTypeSeeder::class,
            \Database\Seeders\OrganizationalUnitSeeder::class,
            \Database\Seeders\RolesAndPermissionsSeeder::class,
        ]);
    }
}
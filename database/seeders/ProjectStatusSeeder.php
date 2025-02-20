<?php

namespace Database\Seeders;

use App\Models\ProjectStatus;
use Illuminate\Database\Seeder;

class ProjectStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Active',
                'slug' => 'active',
                'color' => '#28a745',
                'description' => 'Project is currently in progress',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'On Hold',
                'slug' => 'on-hold',
                'color' => '#ffc107',
                'description' => 'Project is temporarily paused',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Completed',
                'slug' => 'completed',
                'color' => '#17a2b8',
                'description' => 'Project has been completed successfully',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Cancelled',
                'slug' => 'cancelled',
                'color' => '#dc3545',
                'description' => 'Project has been cancelled',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            ProjectStatus::create($status);
        }
    }
}

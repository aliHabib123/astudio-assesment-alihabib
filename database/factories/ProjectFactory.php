<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' ' . fake()->randomElement(['Website', 'App', 'Platform', 'System']),
            'status_id' => ProjectStatus::inRandomOrder()->first()->id,
        ];
    }
}

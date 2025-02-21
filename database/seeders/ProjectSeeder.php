<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Create 10 projects
        Project::factory(10)->create()->each(function ($project) {
            // Assign random users (1-3) to each project
            $users = User::inRandomOrder()->take(rand(1, 3))->get();
            $project->users()->attach($users);

            // Add attribute values for each project
            Attribute::all()->each(function ($attribute) use ($project) {
                AttributeValue::create([
                    'project_id' => $project->id,
                    'attribute_id' => $attribute->id,
                    'value' => $this->generateAttributeValue($attribute->key),
                ]);
            });
        });
    }

    private function generateAttributeValue(string $key): string
    {
        return match($key) {
            'department' => fake()->randomElement(['Marketing', 'IT', 'Sales', 'HR', 'Finance']),
            'start_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'user_role' => fake()->randomElement(['Developer', 'Designer', 'Project Manager', 'QA', 'Business Analyst']),
            default => fake()->word(),
        };
    }
}

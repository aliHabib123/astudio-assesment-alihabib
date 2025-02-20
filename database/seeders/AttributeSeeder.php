<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Department',
                'key' => 'department',
                'type' => 'select',
                'options' => ['IT', 'HR', 'Finance', 'Marketing', 'Operations'],
                'description' => 'Department responsible for the project',
            ],
            [
                'name' => 'Start Date',
                'key' => 'start_date',
                'type' => 'date',
                'description' => 'Project start date',
            ],
            [
                'name' => 'End Date',
                'key' => 'end_date',
                'type' => 'date',
                'description' => 'Expected project end date',
            ],
        ];

        foreach ($attributes as $attribute) {
            Attribute::create($attribute);
        }
    }
}

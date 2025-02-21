<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ProjectStatusSeeder::class,
            AttributeSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class, // Add projects last since they depend on other models
        ]);
    }
}

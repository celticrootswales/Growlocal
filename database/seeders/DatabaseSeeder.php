<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Role::firstOrCreate(['name' => 'grower', 'guard_name' => 'web']);

        $this->call(GrowerSeeder::class);
        $this->call(CropPlanSeeder::class);
    }
}

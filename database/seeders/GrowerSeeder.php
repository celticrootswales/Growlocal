<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class GrowerSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->count(5)
            ->create()
            ->each(function ($user) {
                $user->assignRole('grower');
            });
    }
}
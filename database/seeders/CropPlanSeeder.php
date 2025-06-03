<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CropPlan;
use App\Models\User;

class CropPlanSeeder extends Seeder
{
    public function run(): void
    {
        $distributor = User::where('email', 'castlehowell@example.com')->first();
        $grower = User::where('email', 'deancjenko@gmail.com')->first();

        if (!$distributor || !$grower) {
            $this->command->error('Distributor or Grower not found. Please ensure they exist.');
            return;
        }

        $weeks = [
            '2025-07-14', '2025-07-21', '2025-07-28',
            '2025-08-04', '2025-08-11', '2025-08-18', '2025-08-25',
            '2025-09-01', '2025-09-08'
        ];

        $crops = [
            ['name' => 'Carrots', 'unit' => 'kg', 'price_per_unit' => 1.5],
            ['name' => 'Broccoli', 'unit' => 'kg', 'price_per_unit' => 2.0],
            ['name' => 'Cucumbers', 'unit' => 'unit', 'price_per_unit' => 0.5],
        ];

        foreach ($weeks as $week) {
            foreach ($crops as $crop) {
                CropPlan::create([
                    'week' => $week,
                    'crop_name' => $crop['name'],
                    'unit' => $crop['unit'],
                    'expected_quantity' => rand(100, 200),
                    'price_per_unit' => $crop['price_per_unit'],
                    'distributor_id' => $distributor->id,
                    'grower_id' => $grower->id,
                ]);
            }
        }

        $this->command->info('Crop plan seeded successfully!');
    }
}
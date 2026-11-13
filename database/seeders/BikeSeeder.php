<?php

namespace Database\Seeders;

use App\Models\Bike;
use Illuminate\Database\Seeder;

class BikeSeeder extends Seeder
{
    public function run(): void
    {
        $marcas  = ['Trek', 'Specialized', 'Orbea', 'Giant', 'Cannondale', 'BH', 'Scott', 'Cube'];
        $modelos = ['Marlin', 'Allez', 'Urban', 'Explorer', 'Quick', 'Trail', 'Speed', 'Comfort'];

        for ($i = 0; $i < 20; $i++) {
            Bike::create([
                'serial_number'  => 'BK-' . fake()->unique()->numerify('######'),
                'model'          => fake()->randomElement($marcas) . ' ' . fake()->randomElement($modelos) . ' ' . fake()->numberBetween(1, 9),
                'price_per_hour' => fake()->randomFloat(2, 2, 15),
                'status'         => fake()->randomElement(['available', 'available', 'available', 'maintenance', 'retired']),
            ]);
        }
    }
}
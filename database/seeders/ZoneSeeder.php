<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Zone::create([
            'name' => 'Kiota school',
            'latitude' => -1.27270000,
            'longitude' => 36.81450000,
            'radius' => 100, // in meters
            'type' => 'safe_zone',
        ]);

        Zone::create([
            'name' => 'Home',
            'latitude' => -1.30000000,
            'longitude' => 36.70000000,
            'radius' => 150, // in meters
            'type' => 'safe_zone',
        ]);

        Zone::create([
            'name' => 'Highway',
            'latitude' => -1.25000000,
            'longitude' => 36.80000000,
            'radius' => 100, // in meters
            'type' => 'restricted',
        ]);

    }
}

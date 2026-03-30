<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;
use App\Models\Dependant;

class DeviceSimulatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dependant = Dependant::where('name', 'like', '%Ryan%')->first();

        if ($dependant) {

        // create a tag for ryan
           $tag = Tag::updateOrCreate(
           ['device_id' => 'IOT-RYAN-001'], 
           [
                'dependant_id' => $dependant->id,
                'status' => 'active',
                'battery_level' => 100,
            ]);
            $this->command->info("Tag created for {$dependant->name} with device_id: {$tag->device_id}");
        } else {
            $this->command->error("Dependant with name like 'Ryan' not found. Please create a dependant named 'Ryan' before running this seeder.");
        }
    }
}

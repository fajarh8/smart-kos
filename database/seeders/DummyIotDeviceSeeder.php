<?php

namespace Database\Seeders;

use App\Models\IotDevice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyIotDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $deviceData = [
            [
                'name' => 'Kamar 1 Kos Bunga Raya',
                'token' => bcrypt('bunga157'),
                'roomId' => 1
            ],
            [
                'name' => 'Kamar 2 Kos Merpati',
                'token' => bcrypt('merpati157'),
                'roomId' => 2
            ],
        ];

        foreach($deviceData as $dummy => $data){
            IotDevice::create($data);
        }
    }
}

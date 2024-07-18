<?php

namespace Database\Seeders;

use App\Models\SensorData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummySensorData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensorData = [
            // Bunga Raya
            [
                'category' => 'temperature',
                'data' => 28,
                'deviceId' => 1,
            ],
            [
                'category' => 'humidity',
                'data' => 87,
                'deviceId' => 1,
            ],
            [
                'category' => 'ldr',
                'data' => 43,
                'deviceId' => 1,
            ],
            [
                'category' => 'pir',
                'data' => 1,
                'deviceId' => 1,
            ],
            [
                'category' => 'pzem-voltage',
                'data' => 220,
                'deviceId' => 1,
            ],
            [
                'category' => 'pzem-current',
                'data' => 1.4,
                'deviceId' => 1,
            ],
            [
                'category' => 'pzem-power',
                'data' => 308,
                'deviceId' => 1,
            ],
            [
                'category' => 'pzem-freq',
                'data' => 50,
                'deviceId' => 1,
            ],
            [
                'category' => 'pzem-powerFactor',
                'data' => 0.7,
                'deviceId' => 1,
            ],
            [
                'category' => 'pzem-activePower',
                'data' => 280,
                'deviceId' => 1,
            ],
            [
                'category' => 'pzem-reactivePower',
                'data' => 300,
                'deviceId' => 1,
            ],
            [
                'category' => 'pzem-energy',
                'data' => 107,
                'deviceId' => 1,
            ],
            // Merpati
            [
                'category' => 'temperature',
                'data' => 21,
                'deviceId' => 2,
            ],
            [
                'category' => 'humidity',
                'data' => 79,
                'deviceId' => 2,
            ],
            [
                'category' => 'ldr',
                'data' => 51,
                'deviceId' => 2,
            ],
            [
                'category' => 'pir',
                'data' => 1,
                'deviceId' => 2,
            ],
            [
                'category' => 'pzem-voltage',
                'data' => 220,
                'deviceId' => 2,
            ],
            [
                'category' => 'pzem-current',
                'data' => 2.3,
                'deviceId' => 2,
            ],
            [
                'category' => 'pzem-power',
                'data' => 506,
                'deviceId' => 2,
            ],
            [
                'category' => 'pzem-freq',
                'data' => 50,
                'deviceId' => 2,
            ],
            [
                'category' => 'pzem-powerFactor',
                'data' => 0.8,
                'deviceId' => 2,
            ],
            [
                'category' => 'pzem-activePower',
                'data' => 310,
                'deviceId' => 2,
            ],
            [
                'category' => 'pzem-reactivePower',
                'data' => 120,
                'deviceId' => 2,
            ],
            [
                'category' => 'pzem-energy',
                'data' => 207,
                'deviceId' => 2,
            ],
        ];

        foreach($sensorData as $dummy => $data){
            SensorData::create($data);
        }
    }
}

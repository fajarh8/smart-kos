<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userData = [
          [
            'name' => 'Eko Heri Nurdi',
            'email' => 'ekoheri@email.com',
            'role' => 'admin',
            'password' => bcrypt('eko157')
          ],
          [
            'name' => 'Ied Fajar Heryan',
            'email' => 'iedfajar@email.com',
            'role' => 'user',
            'password' => bcrypt('ied157')
          ],
          [
            'name' => 'Sri Maryani',
            'email' => 'srimaryani@email.com',
            'role' => 'admin',
            'password' => bcrypt('sri157')
          ],
          [
            'name' => 'Zein Fahad Heryan',
            'email' => 'zeinfahad@email.com',
            'role' => 'user',
            'password' => bcrypt('zein157')
          ],
        ];

        foreach($userData as $dummy => $data){
            User::create($data);
        }
    }
}

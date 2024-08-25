<?php

namespace Database\Seeders;

use App\Models\Apotek;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApotekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Apotek::create([
            'name_of_apotek' => 'Apotek Jaya Abadi',
            'email' => 'my@gmail.com',
            'contact_phone' => 123456789,
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'zip_code' => '12345',
            'address' => 'Jl. Raya Jakarta No. 123',
            'bio' => 'Apotek Jaya Abadi adalah apotek yang menyediakan berbagai macam obat-obatan',
        ]);
    }
}

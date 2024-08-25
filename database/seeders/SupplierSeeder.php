<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'supplier_name' => 'Supplier Obat Sehat',
            'email' => 'my@gmail.com',
            'contact_phone' => 123456789,
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'zip_code' => '12345',
            'address' => 'Jl. Raya Jakarta No. 123',
            'description' => 'Supplier Obat Sehat adalah supplier yang menyediakan berbagai macam obat-obatan',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\CategoryProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CategoryProduct::create([
            'name' => 'Obat Demam',
            'isActive' => true,
        ]);

        CategoryProduct::create([
            'name' => 'Obat Batuk',
            'isActive' => true,
        ]);
    }
}

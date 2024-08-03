<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        for ($i=0; $i <= 100; $i++) { 
            Product::create([
                'product_code' => 'A-' . $faker->numberBetween(4, 100),
                'name' => 'Product' . $faker->name,
                'category_product_id' => 1,
                'unit_product_id' => 1,
                'img_product' => 'komik.jpg',
                'unit_price' => $faker->randomNumber(7, true),
                'stock' => $faker->numberBetween(10, 100),
                'minimum_stock_level' => $faker->numberBetween(1, 20),
            ]);
        }
    }
}

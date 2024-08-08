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
        
        $productCreated = Product::create([
            'product_code' => 'A-001',
            'name' => 'Product A',
            'category_product_id' => 1,
            'unit_product_id' => 1,
            'img_product' => 'komik.jpg',
            'unit_price' => 1000,
            'profit_margin' => 20,
            'unit_selling_price' => 1200,
        ]);
        
        $productCreated->stock()->create([
            'product_id' => $productCreated->id,
            'stock' => $faker->numberBetween(10, 100),
            'minimum_stock_level' => $faker->numberBetween(1, 20),
        ]);
    }
}

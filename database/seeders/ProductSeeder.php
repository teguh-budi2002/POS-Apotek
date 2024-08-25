<?php

namespace Database\Seeders;

use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\UnitProduct;
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
        $categoryProduct = CategoryProduct::first();
        $unitProduct = UnitProduct::first();

        if (is_null($categoryProduct)) {
            $categoryProduct = CategoryProduct::create([
                'name' => 'Obat Panas',
                'isActive' => 1
            ]);
        }

        if (is_null($unitProduct)) {
            $unitProduct = UnitProduct::create([
                'name' => 'Botol',
                'isActive' => 1
            ]);
        }

        $productCreated = Product::create([
            'product_code' => 'A-001',
            'name' => 'Product A',
            'category_product_id' => $categoryProduct->id ?? 1,
            'unit_product_id' => $unitProduct->id ?? 1,
            'img_product' => 'komik.jpg',
            'unit_price' => 1000,
            'profit_margin' => 20,
            'unit_selling_price' => 1200,
        ]);

        $productCreated2 = Product::create([
            'product_code' => 'A-002',
            'name' => 'Product B',
            'category_product_id' => $categoryProduct->id ?? 1,
            'unit_product_id' => $unitProduct->id ?? 1,
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

        $productCreated2->stock()->create([
            'product_id' => $productCreated2->id,
            'stock' => $faker->numberBetween(10, 100),
            'minimum_stock_level' => $faker->numberBetween(1, 20),
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_product_id',
        'unit_product_id',
        'name',
        'price',
        'description',
        'additional_description',
        'img_product'
    ];

    public function category() {
        return $this->belongsTo(CategoryProduct::class);
    }

    public function unit() {
        return $this->belongsTo(UnitProduct::class);
    }

    public function orderedProducts() {
        return $this->belongsToMany(PurchaseProduct::class, 'ordered_purchase_products', 'product_id', 'purchase_product_id')
                    ->as('product_ordered')
                    ->withPivot('qty')
                    ->withTimestamps();
    }

    public function salesProducts() {
        return $this->belongsToMany(PurchaseProduct::class, 'ordered_sales_products', 'product_id', 'sales_product_id')
                    ->as('saled_product')
                    ->withPivot('qty')
                    ->withTimestamps();
    }
}

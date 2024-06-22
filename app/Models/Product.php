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
        'product_code',
        'name',
        'unit_price',
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

    public function stock() {
        return $this->hasOne(StockProduct::class);
    }

    public function orderedProducts() {
        return $this->belongsToMany(PurchaseProduct::class, 'ordered_purchase_products', 'product_id', 'purchase_product_id')
                    ->as('product_ordered')
                    ->withPivot('qty', 'price_after_discount', 'selling_price', 'profit_margin', 'discount', 'tax', 'expired_date_product')
                    ->withTimestamps();
    }

    public function salesProducts() {
        return $this->belongsToMany(PurchaseProduct::class, 'ordered_sales_products', 'product_id', 'sales_product_id')
                    ->as('saled_product')
                    ->withPivot('qty')
                    ->withTimestamps();
    }
}

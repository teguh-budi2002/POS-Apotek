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
        return $this->belongsToMany(PurchaseProduct::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'isActive'
    ];

    public function products() {
        return $this->hasMany(Product::class, 'category_product_id');
    }
}

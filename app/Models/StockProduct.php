<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'stock',
        'minimum_stock_level',
        'maximum_stock_level',
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function scopeFilterStockByProduct($query, $filter) {
        return $query->when($filter ?? false, function($query, $filter) {
            return $query->whereHas('product', function ($query) use($filter) {
                $query->where('product.name', 'like', '%' . $filter . '%');
            })->orderByDesc("name");
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'stock',
        'minimum_stock_level',
        'maximum_stock_level',
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedProductReturned extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'product_id',
        'return_product_id',
        'qty',
        'total_price',
        'batch_number',
        'expired_date'
    ];
}

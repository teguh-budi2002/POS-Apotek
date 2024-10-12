<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderedPurchaseProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'purchase_prodcut_id',
        'qty',
        'price_after_discount',
        'selling_price',
        'profit_margin',
        'discount',
        'tax',
        'expired_date_product',
        'batch_number'
    ];
}

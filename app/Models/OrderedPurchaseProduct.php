<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderedPurchaseProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'purchase_prodcut_id'
    ];
}

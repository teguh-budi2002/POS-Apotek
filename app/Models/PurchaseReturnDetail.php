<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnDetail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'purchase_return_id',
        'purchase_product_ref_number'
    ];
}

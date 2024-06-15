<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderedSalesProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'sales_product_id',
        'qty'
    ];
}

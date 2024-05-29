<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'apotek_id',
        'supplier_id',
        'product_id',
        'invoice',
        'grand_total',
        'sub_total',
        'selling_price',
        'profit_margin',
        'discount',
        'payment_method',
        'status_order',
        'status_payment',
        'qty',
        'order_date',
        'paid_on',
        'tax_amount',
        'shipping_fee',
        'shipping_details',
        'order_note',
        'proof_of_payment',
    ];
}

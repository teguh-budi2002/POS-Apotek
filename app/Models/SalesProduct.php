<?php

namespace App\Models;

use App\PaymentMethod;
use App\StatusOrder;
use App\StatusPayment;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesProduct extends Model
{
    use HasFactory;

     protected $fillable = [
        'apotek_id',
        'customer_id',
        'invoice',
        'grand_total',
        'sub_total',
        'selling_price',
        'profit_margin',
        'discount',
        'payment_method',
        'status_order',
        'status_payment',
        'order_date',
        'paid_on',
        'tax',
        'shipping_cost',
        'shipping_details',
        'order_note',
        'proof_of_payment',
        'action_by'
    ];

    protected function casts()
    {
        return [
            'status_order' => StatusOrder::class,
            'status_payment' => StatusPayment::class,
            'payment_method' => PaymentMethod::class
        ];
    }

    public function apotek() {
        return $this->belongsTo(Apotek::class);
    }

    public function customer() {
        return $this->belongsTo(Customer::class);
    }

    public function salesProduct() {
        return $this->belongsToMany(Product::class, 'ordered_sales_products', 'sales_product_id', 'product_id')
                    ->withPivot('qty')
                    ->as('product')
                    ->withTimestamps();
    }

    public function setInvoiceAttribute($value) {
        do {
            $time = Carbon::now()->format('YmdHis');
            $randString = strtoupper(Str::random(8));
            $invoice = "INV-{$randString}.{$time}";
        } while (PurchaseProduct::where('invoice', $invoice)->exists());

        return $this->attributes['invoice'] = $invoice;
    }
}

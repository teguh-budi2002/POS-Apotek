<?php

namespace App\Models;

use App\PaymentMethod;
use App\StatusOrder;
use App\StatusPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PurchaseProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'apotek_id',
        'supplier_id',
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
        'tax_amount',
        'shipping_fee',
        'shipping_details',
        'order_note',
        'proof_of_payment',
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

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function purchasedProducts() {
        return $this->belongsToMany(Product::class, 'ordered_purchase_products', 'purchase_product_id', 'product_id')
                    ->as('product')
                    ->withPivot('qty')
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

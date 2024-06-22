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
        'reference_number',
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
        'action_by',
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
                    ->withPivot('qty', 'price_after_discount', 'selling_price', 'profit_margin', 'discount', 'tax', 'expired_date_product')
                    ->withTimestamps();
    }

    public function setReferenceNumberAttribute($value) {
        do {
            $time = Carbon::now()->format('Y');
            $randString = strtoupper(Str::random(8));
            $reference_number = "REF-{$randString}.{$time}";
        } while (PurchaseProduct::where('reference_number', $reference_number)->exists());

        return $this->attributes['reference_number'] = $reference_number;
    }
}

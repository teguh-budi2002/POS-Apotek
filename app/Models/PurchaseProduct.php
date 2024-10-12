<?php

namespace App\Models;

use App\Http\Requests\ReturnPurchaseProductRequest;
use App\PaymentMethod;
use App\StatusOrder;
use App\StatusPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

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
        'status_order',
        'order_date',
        'tax',
        'termin_payment',
        'format_termin_date',
        'termin_date',
        'shipping_cost',
        'shipping_details',
        'order_note',
        'purchase_invoice',
        'action_by',
        'updated_by'
    ];

    protected function casts()
    {
        return [
            'status_order' => StatusOrder::class,
            'status_payment' => StatusPayment::class,
            'payment_method' => PaymentMethod::class
        ];
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }

    public function apotek() {
        return $this->belongsTo(Apotek::class);
    }

    public function supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function purchasedProducts() {
        return $this->belongsToMany(Product::class, 'ordered_purchase_products', 'purchase_product_id', 'product_id')
                    ->as('productDetail')
                    ->withPivot('qty', 'price_after_discount', 'selling_price', 'profit_margin', 'discount', 'tax', 'batch_number', 'expired_date_product', 'sub_total');
    }

    public function purchasedProductsWithoutDetail() {
        return $this->belongsToMany(Product::class, 'ordered_purchase_products', 'purchase_product_id', 'product_id');
    }

    public function orderedProductDetailsForReturn() {
        return $this->belongsToMany(Product::class, 'ordered_purchase_products', 'purchase_product_id', 'product_id')
                    ->as('productDetail')
                    ->withPivot('qty', 'sub_total', 'batch_number', 'expired_date_product');
    }

    public function orderedProductReturned() {
        return $this->belongsToMany(ReturnPurchasedProduct::class, 'purchase_return_details', 'purchase_product_ref_number', 'purchase_return_id', 'reference_number', 'id');
    }

    public function setReferenceNumberAttribute($value) {
        if (!empty($value)) {
            if (str_starts_with($value, 'REF-')) {
                return $this->attributes['reference_number'] = $value;
            } else {
                return $this->attributes['reference_number'] = "REF-{$value}";
            }
        }

        if (empty($value)) {
            do {
                $time = Carbon::now()->format('Y');
                $randString = strtoupper(Str::substr(Uuid::uuid4()->toString(), 0, 8));
                $reference_number = "REF-{$randString}-{$time}";
            } while (PurchaseProduct::where('reference_number', $reference_number)->exists());
    
            return $this->attributes['reference_number'] = $reference_number;
        }
    }

    public function setOrderDateAttribute($value) {
        return $this->attributes['order_date'] = Carbon::parse($value)->setTimezone('Asia/Jakarta')->toDateTimeString();
    }

    public function getTerminDateAttribute($value) {
        return Carbon::parse($value)->isoFormat('dddd, D MMMM Y');
    }

    public function scopeFilterOrderedProducts($query, $filters) {
        if (!empty($filters)) {
            $query->when(!empty($filters['search']), function ($query) use ($filters) {
                $query->where('reference_number', 'like', '%' . $filters['search'] . '%');
            });

            $query->when(!empty($filters['apotek']), function ($query) use ($filters) {
                $query->where('apotek_id', $filters['apotek']);
            });

            $query->when(!empty($filters['supplier']), function ($query) use ($filters) {
                $query->where('supplier_id', $filters['supplier']);
            });

            $query->when(!empty($filters['status_order']), function ($query) use ($filters) {
                $query->where('status_order', $filters['status_order']);
            });

            $query->when(!empty($filters['status_payment']), function ($query) use ($filters) {
                $query->whereHas('payment', function ($query) use ($filters) {
                    $query->where('status_payment', $filters['status_payment']);
                });
            });

            $query->when(!empty($filters['user']), function ($query) use ($filters) {
                $query->where('action_by', $filters['user']);
            });

            $query->when(!empty($filters['start_date']) && !empty($filters['end_date']),
                function ($query) use ($filters) {
                    $startDate = Carbon::parse($filters['start_date'])->startOfDay();
                    $endDate = Carbon::parse($filters['end_date'])->endOfDay();

                    $query->whereBetween('order_date', [$startDate, $endDate]);
                }
            );

            $query->orderBy('order_date', 'asc');
        }
    }
}

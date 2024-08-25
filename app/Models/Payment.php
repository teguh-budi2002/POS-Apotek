<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_product_id',
        'cash_paid',
        'payment_method',
        'status_payment',
        'paid_on',
        'payment_notes',
        'payment_type',
        'proof_of_payment',
        'is_the_nominal_cost_more_or_less',
        'nominal_cost_more_or_less'
    ];

    public function purchaseProduct() {
        return $this->belongsTo(PurchaseProduct::class);
    }

    public function setPaidOnAttribute($value) {
        if (!empty($value)) {
            $formattedValue = Carbon::parse($value)->setTimezone('Asia/Jakarta')->toDateTimeString();
            return $this->attributes['paid_on'] = $formattedValue;
        }
    }
}

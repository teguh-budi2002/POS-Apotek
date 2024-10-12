<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ReturnPurchasedProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'return_reference_number',
        'supplier_id',
        'apotek_id',
        'total_returned_items',
        'return_amount',
        'return_date',
        'return_status',
        'return_note',
        'additional_note',
        'action_by',
        'updated_by'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function apotek()
    {
        return $this->belongsTo(Apotek::class);
    }

    public function returnDetails()
    {
        return $this->belongsToMany(PurchaseProduct::class, 'purchase_return_details', 'purchase_return_id', 'purchase_product_ref_number', 'id', 'reference_number');
    }

    public function setReturnDateAttribute($value) {
        return $this->attributes['return_date'] = Carbon::parse($value)->setTimezone('Asia/Jakarta')->toDateTimeString();
    }

    public function setReturnReferenceNumberAttribute($value) {
        if (!empty($value)) {
            if (str_starts_with($value, 'RET-')) {
                return $this->attributes['return_reference_number'] = $value;
            } else {
                return $this->attributes['return_reference_number'] = "REF-{$value}";
            }
        }

        if (empty($value)) {
            do {
                $time = Carbon::now()->format('Y');
                $randString = strtoupper(Str::substr(Uuid::uuid4()->toString(), 0, 8));
                $return_reference_number = "RET-{$randString}-{$time}";
            } while (ReturnPurchasedProduct::where('return_reference_number', $return_reference_number)->exists());

            return $this->attributes['return_reference_number'] = $return_reference_number;
        }
    }

     public function scopeFilterReturnPurchasedProducts($query, $filters) {
        if (!empty($filters)) {
            $query->when(!empty($filters['search']), function ($query) use ($filters) {
                $query->where('return_reference_number', 'like', '%' . $filters['search'] . '%');
            });

            $query->when(!empty($filters['apotek']), function ($query) use ($filters) {
                $query->where('apotek_id', $filters['apotek']);
            });

            $query->when(!empty($filters['supplier']), function ($query) use ($filters) {
                $query->where('supplier_id', $filters['supplier']);
            });

            $query->when(!empty($filters['return_status']), function ($query) use ($filters) {
                $query->where('return_status', $filters['return_status']);
            });

            $query->when(!empty($filters['user']), function ($query) use ($filters) {
                $query->where('action_by', $filters['user']);
            });

            $query->when(!empty($filters['start_date']) && !empty($filters['end_date']),
                function ($query) use ($filters) {
                    $startDate = Carbon::parse($filters['start_date'])->startOfDay();
                    $endDate = Carbon::parse($filters['end_date'])->endOfDay();

                    $query->whereBetween('return_date', [$startDate, $endDate]);
                }
            );

            $query->orderBy('return_date', 'asc');
        }
    }
}

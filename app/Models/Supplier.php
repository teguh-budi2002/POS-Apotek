<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'supplier_name',
        'email',
        'contact_phone',
        'city',
        'province',
        'zip_code',
        'address',
        'description'
    ];

    public function scopeFilterSupplier($query, $filter) {
        return $query->when($filter ?? false, function($query, $filter) {
            return $query->where('supplier_name', 'like', '%' . $filter . '%')
                         ->orderByDesc("supplier_name");
        });
    }
}

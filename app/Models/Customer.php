<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'contact_phone',
        'gender',
        'address'
    ];

    public function scopeFilterCustomer($query, $filter) {
        return $query->when($filter ?? false, function($query, $filter) {
            return $query->where('name', 'like', '%' . $filter . '%')
                         ->orderByDesc("name");
        });
    }
}

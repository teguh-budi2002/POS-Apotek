<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Apotek extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name_of_apotek',
        'email',
        'contact_phone',
        'city',
        'province',
        'zip_code',
        'address',
        'bio'
    ];

    public function scopeFilterApotek($query, $filter) {
        return $query->when($filter ?? false, function($query, $filter) {
            return $query->where('name_of_apotek', 'like', '%' . $filter . '%')
                         ->orderByDesc("name_of_apotek");
        });
    }
}

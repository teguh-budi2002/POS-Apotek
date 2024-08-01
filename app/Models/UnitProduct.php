<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'isActive'
    ];

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function scopeIsActive($query) {
        return $query->where('isActive', true);
    }

    public function scopeFilterUnit($query, $filter) {
        return $query->when($filter ?? false, function($query, $filter) {
            return $query->where('name', 'like', '%' . $filter . '%')
                         ->orderByDesc("name");
        });
    }
}

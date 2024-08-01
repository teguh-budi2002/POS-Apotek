<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'isActive'
    ];

    public function products() {
        return $this->hasMany(Product::class, 'category_product_id');
    }

    public function scopeIsActive($query) {
        return $query->where('isActive', true);
    }

    public function scopeFilterCategory($query, $filter) {
        return $query->when($filter ?? false, function($query, $filter) {
            return $query->where('name', 'like', '%' . $filter . '%')
                         ->orderByDesc("name");
        });
    }
}

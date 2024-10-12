<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_product_id',
        'unit_product_id',
        'product_code',
        'name',
        'unit_price',
        'profit_margin',
        'unit_selling_price',
        'description',
        'img_product',
        'isActive'
    ];

    public function category() {
        return $this->belongsTo(CategoryProduct::class, 'category_product_id');
    }

    public function unit() {
        return $this->belongsTo(UnitProduct::class, 'unit_product_id');
    }

    public function stock() {
        return $this->hasOne(StockProduct::class);
    }

    public function orderedProducts() {
        return $this->belongsToMany(PurchaseProduct::class, 'ordered_purchase_products', 'product_id', 'purchase_product_id')
                    ->as('order_detail')
                    ->withPivot('qty', 'price_after_discount', 'selling_price', 'profit_margin', 'discount', 'tax', 'expired_date_product', 'sub_total')
                    ->withTimestamps();
    }

    public function orderedProductsForReturn() {
        return $this->belongsToMany(PurchaseProduct::class, 'ordered_purchase_products', 'product_id', 'purchase_product_id')
                    ->as('order_detail')
                    ->withPivot('qty', 'sub_total', 'batch_number', 'expired_date_product')
                    ->withTimestamps();
    }

    public function salesProducts() {
        return $this->belongsToMany(PurchaseProduct::class, 'ordered_sales_products', 'product_id', 'sales_product_id')
                    ->as('saled_product')
                    ->withPivot('qty')
                    ->withTimestamps();
    }

    public function scopeIsActive($query) {
        return $query->where('isActive', 1);
    }

    public function scopeFilterProduct($query, $filter) {
        $query->when($filter ?? false, function($query, $filter) {
            return $query->where(function($query) use($filter) {
                $query->where('product_code', 'like', '%' . $filter . '%')
                    ->orWhere('name', 'like', '%' . $filter . '%');  
            })->orWhereRaw('MATCH(description) AGAINST (? IN NATURAL LANGUAGE MODE)', [$filter])->orderByDesc("name");
        });
    }

    public function scopeFilterProductWithoutDescription($query, $filter) {
        $query->when($filter ?? false, function($query, $search) {
            return $query->where(function($query) use($search) {
                $query->where('product_code', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%');  
            })->orderBy("name", "asc");
        });
    }
}

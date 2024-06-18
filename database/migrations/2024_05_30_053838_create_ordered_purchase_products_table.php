<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ordered_purchase_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('purchase_product_id')->constrained();
            $table->integer('qty');
            $table->integer('price_after_discount')->nullable();
            $table->integer('selling_price');
            $table->double('profit_margin');
            $table->integer('discount')->nullable();
            $table->integer('tax')->nullable();
            $table->date('expired_date_product');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordered_purchase_products');
    }
};

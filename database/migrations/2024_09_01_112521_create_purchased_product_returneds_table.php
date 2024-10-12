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
        Schema::create('purchased_product_returneds', function (Blueprint $table) {  
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('return_product_id')->constrained('return_purchased_products');
            $table->integer('qty');
            $table->float('total_price');
            $table->string('batch_number');
            $table->date('expired_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchased_product_returneds');
    }
};

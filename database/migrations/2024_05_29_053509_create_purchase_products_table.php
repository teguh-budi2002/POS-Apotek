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
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apotek_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->string('invoice');
            $table->integer('grand_total')->comment('Total harga include pajak, diskon(jika ada), dll.');
            $table->integer('sub_total')->comment('Total harga exclude pajak, diskon(jika ada), dll.');
            $table->integer('selling_price');
            $table->double('profit_margin');
            $table->double('discount')->nullable();
            $table->enum('payment_method', ["Bank Transfer", "Credit", "Cash"]);
            $table->enum("status_order", ["Pending", "Shipping", "Delivered", "Cancelled"]);
            $table->enum("status_payment", ["Paid", "Due", "Late"])->comment('Paid=dibayar, Due=tempo, Late=terlambat');
            $table->integer('qty');
            $table->date('order_date');
            // $table->date('expired_date');
            $table->date('paid_on')->nullable();
            $table->integer('tax_amount')->nullable();
            $table->integer('shipping_fee')->nullable();
            $table->text('shipping_details')->nullable();
            $table->text('order_note')->nullable();
            $table->string('proof_of_payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_products');
    }
};

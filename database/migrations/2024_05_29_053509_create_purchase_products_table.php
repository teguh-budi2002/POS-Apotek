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
            $table->string('reference_number');
            $table->integer('grand_total')->comment('Total harga include pajak, diskon(jika ada), dll.');
            $table->integer('sub_total')->comment('Total harga exclude pajak, diskon(jika ada), dll.');
            $table->enum('payment_method', ["Bank Transfer", "Credit", "Cash"]);
            $table->enum("status_order", ["Pending", "Shipping", "Delivered", "Cancelled"]);
            $table->enum("status_payment", ["Paid", "Due", "Late"])->comment('Paid=dibayar, Due=tempo, Late=terlambat');
            $table->dateTime('order_date');
            $table->date('paid_on')->nullable();
            $table->integer('shipping_cost')->nullable();
            $table->text('shipping_details')->nullable();
            $table->text('order_note')->nullable();
            $table->string('proof_of_payment')->nullable();
            $table->integer('termin_payment')->comment("masa berakhir pembayaran")->nullable();
            $table->enum('format_termin_date', ['Day', 'Month'])->nullable();
            $table->string('payment_invoice')->comment('invoice dari pihak supplier');
            $table->string('action_by')->comment('Nama admin yang melakukan aksi.');
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

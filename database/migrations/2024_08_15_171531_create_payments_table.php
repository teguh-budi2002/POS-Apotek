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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_product_id')->constrained('purchase_products')->onUpdate('cascade');
            $table->integer('cash_paid')->nullable();
            $table->enum('payment_method', ["Bank Transfer", "Credit", "Cash"]);
            $table->enum("status_payment", ["Paid", "Due", "Late"])->comment('Paid=dibayar, Due=tempo, Late=terlambat');
            $table->dateTime('paid_on')->nullable();
            $table->text('payment_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

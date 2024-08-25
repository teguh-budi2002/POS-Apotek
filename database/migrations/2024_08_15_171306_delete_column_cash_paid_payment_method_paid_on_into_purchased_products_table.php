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
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->dropColumn('cash_paid');
            $table->dropColumn('payment_method');
            $table->dropColumn('paid_on');
            $table->dropColumn('status_payment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->integer('cash_paid')->nullable();
            $table->enum('payment_method', ["Bank Transfer", "Credit", "Cash"]);
            $table->dateTime('paid_on')->nullable();
            $table->enum("status_payment", ["Paid", "Due", "Late"])->comment('Paid=dibayar, Due=tempo, Late=terlambat');
        });
    }
};

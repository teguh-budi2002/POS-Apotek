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
       Schema::table('purchase_products', function(Blueprint $table) {
        $table->enum("status_payment", ["Paid", "Due", "Late"])
            ->nullable()
            ->comment('Paid=dibayar, Due=tempo, Late=terlambat')
            ->change();
        $table->dateTime('paid_on')->nullable()->change();
       }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_products', function(Blueprint $table) {
            $table->enum("status_payment", ["Paid", "Due", "Late"])
                ->comment('Paid=dibayar, Due=tempo, Late=terlambat')
                ->change();
            $table->date('paid_on')->nullable()->change();
        });
    }
};

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
        Schema::table('ordered_purchase_products', function(Blueprint $table) {
            $table->dropColumn('sub_total');
        });
        Schema::table('ordered_purchase_products', function(Blueprint $table) {
            $table->integer('sub_total')->after('selling_price')->comment('Total harga qty * harga jual (satuan)');
        });

        Schema::table('purchase_products', function(Blueprint $table) {
            $table->dropColumn('sub_total');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordered_purchase_products', function(Blueprint $table) {
            $table->dropColumn('sub_total')->comment('Total harga qty * harga jual (satuan)');
        });

        Schema::table('purchase_products', function(Blueprint $table) {
            $table->integer('sub_total')->comment('Total harga qty * harga jual (satuan)');
        });
    }
};

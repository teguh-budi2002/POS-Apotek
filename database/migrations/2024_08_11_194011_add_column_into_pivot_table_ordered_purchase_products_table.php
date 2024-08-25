<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Nette\Schema\Schema as SchemaSchema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_products', function(Blueprint $table) {
            if (Schema::hasColumn('purchase_products', 'sub_total')) {
                $table->dropColumn('sub_total');
            }
        });

        Schema::table('ordered_purchase_products', function(Blueprint $table) {
            if (!Schema::hasColumn('ordered_purchase_products', 'sub_total')) {
                $table->integer('sub_total')->after('selling_price')->comment('Total harga qty * harga jual (satuan)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('ordered_purchase_products', 'sub_total')) {
            Schema::table('ordered_purchase_products', function(Blueprint $table) {
                $table->dropColumn('sub_total');$table->dropColumn('sub_total')->comment('Total harga qty * harga jual (satuan)');
            });
        }
    }
};

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
        Schema::table('ordered_purchase_products', function (Blueprint $table) {
            $table->string('batch_number')->nullable()->after('tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordered_purchased_products', function (Blueprint $table) {
            $table->dropColumn('batch_number');
        });
    }
};

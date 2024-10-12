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
        Schema::table('return_purchased_products', function (Blueprint $table) {
            $table->string('action_by')->nullable();
            $table->string('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_purchased_products', function (Blueprint $table) {
            $table->dropColumn('action_by');
            $table->dropColumn('updated_by');
        });
    }
};

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
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('is_the_nominal_cost_more_or_less', ['more', 'less'])->after('cash_paid')->nullable();
            $table->integer('nominal_cost_more_or_less')->after('is_the_nominal_cost_more_or_less')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('is_the_nominal_cost_more_or_less');
            $table->dropColumn('nominal_cost_more_or_less');
        });
    }
};

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
            if (Schema::hasColumn('purchase_products', 'proof_of_payment')) {
                $table->dropColumn('proof_of_payment');
            }

            $table->string('purchase_invoice')
                    ->after('format_termin_date')
                    ->nullable()
                    ->comment('file/gambar faktur pembelian');
        });

        Schema::table('payments', function(Blueprint $table) {
             $table->string('proof_of_payment')->after('payment_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_products', function(Blueprint $table) {
            if (!Schema::hasColumn('purchase_products', 'proof_of_payment')) {
                $table->string('proof_of_payment')->nullable();
            }

            $table->dropColumn('purchase_invoice');
        });

        Schema::table('payments', function(Blueprint $table) {
             $table->dropColumn('proof_of_payment');
        });
    }
};

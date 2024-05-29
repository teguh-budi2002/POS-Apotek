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
        Schema::table('apoteks', function(Blueprint $table) {
            $table->string('contact_phone', 14)->change();
        });

        Schema::table('suppliers', function(Blueprint $table) {
            $table->string('contact_phone', 14)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

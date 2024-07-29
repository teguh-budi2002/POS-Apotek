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
        Schema::create('apoteks', function (Blueprint $table) {
            $table->id();
            $table->string('name_of_apotek');
            $table->string('email', 55);
            $table->integer('contact_phone');
            $table->string('city');
            $table->string('province');
            $table->string('zip_code');
            $table->text('address');
            $table->text('bio');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apoteks');
    }
};

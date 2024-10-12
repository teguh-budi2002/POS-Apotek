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
        Schema::create('return_purchased_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('apotek_id')->constrained('apoteks');
            $table->integer('total_returned_items');
            $table->float('return_amount');
            $table->dateTime('return_date');
            $table->enum('return_status', ['Pending', 'Completed', 'Rejected']);
            $table->string('return_note')->nullable();
            $table->string('additional_note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_purchased_products');
    }
};
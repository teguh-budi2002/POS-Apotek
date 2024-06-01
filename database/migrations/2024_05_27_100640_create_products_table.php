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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_product_id')
                  ->constrained()
                  ->onUpdate('cascade');
            $table->string('name');
            $table->integer('price');
            $table->text('description');
            $table->text('additional_description')->nullable();
            $table->string('img_product', 80);
            $table->enum('status_publish', ['Pending', 'Published'])->defaultValue('Pending');
            $table->boolean('isActive')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

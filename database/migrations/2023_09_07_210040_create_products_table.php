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
            $table->text('name');
            $table->text('description');
            $table->float('price');
            $table->integer('stock');
            $table->json('images');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->json('colors');
            $table->boolean('is_active')->default(true);
            $table->integer('discount')->nullable();

            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('category_id')->references('id')->on('categories');
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

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->float('total_amount');
            $table->float('shipping_fee');
            $table->string('card_number');
            $table->string('card_holder_name');
            $table->unsignedBigInteger('order_status_id');
            $table->unsignedBigInteger('address_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->timestamps();

            $table->foreign('order_status_id')->references('id')->on('order_statuses');
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

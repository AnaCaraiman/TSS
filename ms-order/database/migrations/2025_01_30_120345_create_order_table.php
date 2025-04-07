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
            $table->id()->autoIncrement();
            $table->Integer('user_id');
            $table->string('name');
            $table->string('street');
            $table->string('number');
            $table->string('additional_info')->nullable();
            $table->string('city');
            $table->string('county');
            $table->Integer('postcode');
            $table->string('status')->default('pending');
            $table->string('payment_type')->default('credit_card');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};

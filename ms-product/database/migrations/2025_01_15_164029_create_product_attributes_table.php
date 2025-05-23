<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('attribute_name');
            $table->string('attribute_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};

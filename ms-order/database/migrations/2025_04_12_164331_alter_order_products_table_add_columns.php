<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('order_product', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('image_url')->nullable();
        });
    }

    public function down(): void {
        Schema::table('order_product', function (Blueprint $table) {
            $table->dropColumn(['name', 'price', 'image_url']);
        });
    }
};

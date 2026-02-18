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
            $table->id()->autoIncrement();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->decimal('price_trade', 15, 2);   // سعر تجاري
            $table->decimal('price_customer', 15, 2); // سعر عميل
            $table->decimal('price_technician', 15, 2); // سعر فني
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // العمود + FK في سطر واحد
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

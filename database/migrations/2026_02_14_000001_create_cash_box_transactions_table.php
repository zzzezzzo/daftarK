<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cash_box_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_box_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out']);
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_box_transactions');
    }
};

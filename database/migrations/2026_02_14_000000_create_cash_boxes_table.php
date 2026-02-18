<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cash_boxes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->decimal('total_in', 10, 2)->default(0);
            $table->decimal('total_out', 10, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'closed'])->default('active');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_boxes');
    }
};

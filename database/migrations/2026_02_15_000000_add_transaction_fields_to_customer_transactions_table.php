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
        Schema::table('customer_transactions', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('customer_transactions', 'description')) {
                $table->string('description')->nullable();
            }
            
            if (!Schema::hasColumn('customer_transactions', 'transaction_date')) {
                $table->dateTime('transaction_date')->nullable();
            }
            
            if (!Schema::hasColumn('customer_transactions', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable();
            }
            
            if (!Schema::hasColumn('customer_transactions', 'reference_type')) {
                $table->string('reference_type')->nullable();
            }
            
            if (!Schema::hasColumn('customer_transactions', 'method')) {
                $table->string('method')->nullable();
            }
            
            // Set default values for existing records
            if (Schema::hasColumn('customer_transactions', 'transaction_date')) {
                \DB::statement('UPDATE customer_transactions SET transaction_date = created_at WHERE transaction_date IS NULL');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'transaction_date', 
                'reference_id',
                'reference_type',
                'method'
            ]);
        });
    }
};

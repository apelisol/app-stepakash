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
        Schema::create('ledger', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_id');
            $table->string('transaction_id');
            $table->decimal('amount', 15, 2);
            $table->enum('cr_dr', ['CR', 'DR']);
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Add index first
            $table->index('wallet_id');
            
            // Then add foreign key constraint
            $table->foreign('wallet_id')
                  ->references('wallet_id')
                  ->on('customers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger');
    }
};

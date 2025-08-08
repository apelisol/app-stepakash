<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            
            // Indexes
            $table->index('wallet_id');
            $table->index('transaction_id');
            $table->index(['wallet_id', 'cr_dr', 'status']);
            $table->index('created_at');
            
            // Foreign key will be added in a separate migration
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger');
    }
};

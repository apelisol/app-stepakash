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
        /*
        * Create indexes for faster queries
        */
        Schema::table('ledger', function (Blueprint $table) {
            $table->index('wallet_id');
            $table->index('transaction_id');
            $table->index(['wallet_id', 'cr_dr', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger', function (Blueprint $table) {
            $table->dropIndex(['wallet_id']);
            $table->dropIndex(['transaction_id']);
            $table->dropIndex(['wallet_id', 'cr_dr', 'status']);
        });
    }
};

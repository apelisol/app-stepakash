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
        Schema::table('ledger', function (Blueprint $table) {
            // Add foreign key constraint
            $table->foreign('wallet_id')
                  ->references('wallet_id')
                  ->on('customers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger', function (Blueprint $table) {
            $table->dropForeign(['wallet_id']);
        });
    }
};

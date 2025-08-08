<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add foreign key to ledger table
        Schema::table('ledger', function (Blueprint $table) {
            $table->foreign('wallet_id')
                  ->references('wallet_id')
                  ->on('customers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        // Add foreign key to forgot_password table
        Schema::table('forgot_password', function (Blueprint $table) {
            $table->foreign('wallet_id')
                  ->references('wallet_id')
                  ->on('customers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        // Add foreign key to login_sessions table
        Schema::table('login_sessions', function (Blueprint $table) {
            $table->foreign('wallet_id')
                  ->references('wallet_id')
                  ->on('customers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        // Add foreign key to money_transfers table
        Schema::table('money_transfers', function (Blueprint $table) {
            $table->foreign('sender_wallet_id')
                  ->references('wallet_id')
                  ->on('customers')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ledger', function (Blueprint $table) {
            $table->dropForeign(['wallet_id']);
        });

        Schema::table('forgot_password', function (Blueprint $table) {
            $table->dropForeign(['wallet_id']);
        });

        Schema::table('login_sessions', function (Blueprint $table) {
            $table->dropForeign(['wallet_id']);
        });

        Schema::table('money_transfers', function (Blueprint $table) {
            $table->dropForeign(['sender_wallet_id']);
        });
    }
};

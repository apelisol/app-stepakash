<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('money_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id', 20)->unique();
            $table->string('transaction_number', 20)->unique();
            $table->string('sender_wallet_id', 50);
            $table->string('recipient_wallet_id', 50)->nullable();
            $table->string('recipient_phone', 20);
            $table->string('recipient_name')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('transaction_cost', 10, 2)->default(0);
            $table->enum('transfer_type', ['internal', 'external_mpesa', 'external_airtel']);
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'reversed'])->default('pending');
            $table->string('conversationID')->nullable();
            $table->string('OriginatorConversationID')->nullable();
            $table->string('MpesaReceiptNumber')->nullable();
            $table->string('ResponseCode')->nullable();
            $table->integer('result_code')->nullable();
            $table->text('result_desc')->nullable();
            $table->timestamp('transactionCompletedDateTime')->nullable();
            $table->timestamps();

            $table->index(['sender_wallet_id', 'created_at']);
            $table->index(['recipient_phone', 'created_at']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('money_transfers');
    }
};
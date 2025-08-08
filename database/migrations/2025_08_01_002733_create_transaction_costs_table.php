<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaction_costs', function (Blueprint $table) {
            $table->id();
            $table->enum('transfer_type', ['internal', 'external_mpesa', 'external_airtel']);
            $table->decimal('min_amount', 10, 2);
            $table->decimal('max_amount', 10, 2);
            $table->decimal('fee', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['transfer_type', 'min_amount', 'max_amount']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_costs');
    }
};
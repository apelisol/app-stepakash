<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forgot_password', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_id');
            $table->string('phone');
            $table->string('otp');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forgot_password');
    }
};

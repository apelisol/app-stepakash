<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('forgot_password', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_id');
            $table->string('phone');
            $table->string('otp');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('forgot_password');
    }
};

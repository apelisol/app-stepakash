<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_id')->unique();
            $table->string('account_number')->nullable();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('fullname')->nullable();
            $table->boolean('deriv_account')->default(false);
            $table->string('deriv_token')->nullable();
            $table->string('deriv_email')->nullable();
            $table->string('deriv_login_id')->nullable();
            $table->string('deriv_account_number')->nullable()->unique();
            $table->boolean('deriv_verified')->default(false);
            $table->timestamp('deriv_verification_date')->nullable();
            $table->timestamp('deriv_last_sync')->nullable();
            $table->boolean('agent')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};

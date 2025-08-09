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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('otp', 6)->nullable()->after('deriv_last_sync');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
            $table->timestamp('email_verified_at')->nullable()->after('otp_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['otp', 'otp_expires_at', 'email_verified_at']);
        });
    }
};

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
            // Add currency column if it doesn't exist
            if (!Schema::hasColumn('ledger', 'currency')) {
                $table->string('currency', 3)->default('KES')->after('amount');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('ledger', 'status')) {
                $table->boolean('status')->default(true)->after('currency');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger', function (Blueprint $table) {
            if (Schema::hasColumn('ledger', 'currency')) {
                $table->dropColumn('currency');
            }
            
            if (Schema::hasColumn('ledger', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};

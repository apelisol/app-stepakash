<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddLedgerColumns extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add currency column if it doesn't exist
        DB::statement("ALTER TABLE ledger ADD COLUMN IF NOT EXISTS currency VARCHAR(3) DEFAULT 'KES' AFTER amount");
        
        // Add status column if it doesn't exist
        DB::statement('ALTER TABLE ledger ADD COLUMN IF NOT EXISTS status TINYINT(1) DEFAULT 1 AFTER currency');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns to prevent data loss in production
    }
}

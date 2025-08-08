<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixLedgerIndexes extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the columns exist before trying to add them
        $columns = DB::select("SHOW COLUMNS FROM ledger");
        $columns = array_column($columns, 'Field');
        
        // Add currency column if it doesn't exist
        if (!in_array('currency', $columns)) {
            DB::statement("ALTER TABLE ledger ADD COLUMN currency VARCHAR(3) DEFAULT 'KES' AFTER amount");
        }
        
        // Add status column if it doesn't exist
        if (!in_array('status', $columns)) {
            DB::statement("ALTER TABLE ledger ADD COLUMN status TINYINT(1) DEFAULT 1 AFTER currency");
        }
        
        // Add indexes if they don't exist
        $indexes = [
            'wallet_id' => 'ledger_wallet_id_index',
            'transaction_id' => 'ledger_transaction_id_index',
            'receipt_no' => 'ledger_receipt_no_index'
        ];
        
        foreach ($indexes as $column => $indexName) {
            $indexExists = DB::select("SHOW INDEX FROM ledger WHERE Key_name = ?", [$indexName]);
            if (empty($indexExists)) {
                DB::statement("CREATE INDEX {$indexName} ON ledger ({$column})");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        $indexes = [
            'ledger_wallet_id_index',
            'ledger_transaction_id_index',
            'ledger_receipt_no_index'
        ];
        
        foreach ($indexes as $index) {
            $indexExists = DB::select("SHOW INDEX FROM ledger WHERE Key_name = ?", [$index]);
            if (!empty($indexExists)) {
                DB::statement("DROP INDEX {$index} ON ledger");
            }
        }
        
        // Don't drop columns to avoid data loss in production
    }
}

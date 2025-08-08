<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FinalLedgerFix extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to add columns if they don't exist
        $this->runSafe("
            ALTER TABLE ledger 
            ADD COLUMN IF NOT EXISTS currency VARCHAR(3) DEFAULT 'KES' AFTER amount,
            ADD COLUMN IF NOT EXISTS status TINYINT(1) DEFAULT 1 AFTER currency
        ");
        
        // Add indexes if they don't exist
        $this->addIndex('ledger', 'wallet_id', 'idx_ledger_wallet_id');
        $this->addIndex('ledger', 'transaction_id', 'idx_ledger_transaction_id');
        $this->addIndex('ledger', 'receipt_no', 'idx_ledger_receipt_no');
    }
    
    /**
     * Safely execute SQL with error handling
     */
    protected function runSafe(string $sql): void
    {
        try {
            DB::statement($sql);
        } catch (\Exception $e) {
            // Log the error but don't stop execution
            \Illuminate\Support\Facades\Log::warning("Migration SQL warning: " . $e->getMessage());
        }
    }
    
    /**
     * Add an index if it doesn't exist
     */
    protected function addIndex(string $table, string $column, string $indexName): void
    {
        $this->runSafe("
            CREATE INDEX IF NOT EXISTS {$indexName} 
            ON {$table} ({$column})
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns to prevent data loss
        
        // Drop indexes if they exist
        $indexes = [
            'idx_ledger_wallet_id',
            'idx_ledger_transaction_id',
            'idx_ledger_receipt_no'
        ];
        
        foreach ($indexes as $index) {
            $this->runSafe("DROP INDEX IF EXISTS {$index} ON ledger");
        }
    }
}

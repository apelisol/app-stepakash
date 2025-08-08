<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SimplifyLedgerFix extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to avoid any potential schema builder issues
        $this->addColumnIfNotExists('ledger', 'currency', "VARCHAR(3) DEFAULT 'KES' AFTER amount");
        $this->addColumnIfNotExists('ledger', 'status', 'TINYINT(1) DEFAULT 1 AFTER currency');
        
        // Add indexes
        $this->addIndexIfNotExists('ledger', 'wallet_id', 'ledger_wallet_idx');
        $this->addIndexIfNotExists('ledger', 'transaction_id', 'ledger_transaction_idx');
        $this->addIndexIfNotExists('ledger', 'receipt_no', 'ledger_receipt_idx');
    }
    
    /**
     * Add a column if it doesn't exist
     */
    protected function addColumnIfNotExists(string $table, string $column, string $definition): void
    {
        $columns = DB::select("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        if (empty($columns)) {
            DB::statement("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
        }
    }
    
    /**
     * Add an index if it doesn't exist
     */
    protected function addIndexIfNotExists(string $table, string $column, string $indexName): void
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        if (empty($indexes)) {
            DB::statement("CREATE INDEX {$indexName} ON {$table}({$column})");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to drop columns to prevent data loss
        
        // Drop indexes if they exist
        $indexes = ['ledger_wallet_idx', 'ledger_transaction_idx', 'ledger_receipt_idx'];
        
        foreach ($indexes as $index) {
            DB::statement("DROP INDEX IF EXISTS {$index} ON ledger");
        }
    }
}

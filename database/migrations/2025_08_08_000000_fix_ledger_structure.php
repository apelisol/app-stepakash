<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixLedgerStructure extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if we're using SQLite
        $usingSqlite = config('database.default') === 'sqlite';
        
        // Add columns if they don't exist
        if (!Schema::hasColumn('ledger', 'currency')) {
            DB::statement("ALTER TABLE ledger ADD COLUMN currency VARCHAR(3) DEFAULT 'KES' AFTER amount");
        }
        
        if (!Schema::hasColumn('ledger', 'status')) {
            DB::statement('ALTER TABLE ledger ADD COLUMN status TINYINT(1) DEFAULT 1 AFTER currency');
        }
        
        // Add indexes if they don't exist
        $indexes = [
            'wallet_id' => 'ledger_wallet_id_idx',
            'transaction_id' => 'ledger_transaction_id_idx',
            'receipt_no' => 'ledger_receipt_no_idx'
        ];
        
        foreach ($indexes as $column => $indexName) {
            if (!$this->indexExists('ledger', $indexName)) {
                try {
                    DB::statement("CREATE INDEX {$indexName} ON ledger({$column})");
                } catch (\Exception $e) {
                    // Log the error but don't fail the migration
                    \Illuminate\Support\Facades\Log::error("Failed to create index {$indexName}: " . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Check if an index exists
     */
    protected function indexExists(string $table, string $indexName): bool
    {
        try {
            $result = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
            return count($result) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns to prevent data loss
        
        // Drop indexes if they exist
        $indexes = [
            'ledger_wallet_id_idx',
            'ledger_transaction_id_idx',
            'ledger_receipt_no_idx'
        ];
        
        foreach ($indexes as $index) {
            try {
                DB::statement("DROP INDEX IF EXISTS {$index} ON ledger");
            } catch (\Exception $e) {
                // Continue even if drop fails
                continue;
            }
        }
    }
}

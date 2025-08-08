<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes one by one with raw SQL to avoid migration issues
        $indexes = [
            'wallet_id' => 'ledger_wallet_id_index',
            'transaction_id' => 'ledger_transaction_id_index',
            'receipt_no' => 'ledger_receipt_no_index'
        ];

        foreach ($indexes as $column => $indexName) {
            if (!$this->indexExists('ledger', $indexName)) {
                DB::statement("CREATE INDEX {$indexName} ON `ledger` (`{$column}`)");
            }
        }
    }

    /**
     * Check if an index exists on a table
     */
    protected function indexExists(string $table, string $indexName): bool
    {
        $connection = Schema::getConnection();
        $dbSchemaManager = $connection->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails($table);
        
        return $doctrineTable->hasIndex($indexName);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = [
            'ledger_wallet_id_index',
            'ledger_transaction_id_index',
            'ledger_receipt_no_index'
        ];

        foreach ($indexes as $index) {
            if ($this->indexExists('ledger', $index)) {
                DB::statement("DROP INDEX `{$index}` ON `ledger`");
            }
        }
    }
};

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
        // 1. Add missing columns to ledger table
        Schema::table('ledger', function (Blueprint $table) {
            if (!Schema::hasColumn('ledger', 'currency')) {
                $table->string('currency', 3)->default('KES')->after('amount');
            }
            
            if (!Schema::hasColumn('ledger', 'status')) {
                $table->boolean('status')->default(true)->after('currency');
            }
        });

        // 2. Add indexes one by one with error handling
        $this->addIndexIfNotExists('ledger', 'ledger_wallet_id_index', 'wallet_id');
        $this->addIndexIfNotExists('ledger', 'ledger_transaction_id_index', 'transaction_id');
        $this->addIndexIfNotExists('ledger', 'ledger_receipt_no_index', 'receipt_no');
    }

    /**
     * Add index to a table if it doesn't exist
     */
    protected function addIndexIfNotExists(string $table, string $indexName, string $column): void
    {
        $indexes = DB::select("SHOW INDEX FROM $table");
        $indexExists = collect($indexes)->contains(function ($index) use ($indexName) {
            return $index->Key_name === $indexName;
        });

        if (!$indexExists) {
            Schema::table($table, function (Blueprint $table) use ($indexName, $column) {
                $table->index([$column], $indexName);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes if they exist
        Schema::table('ledger', function (Blueprint $table) {
            $indexes = ['ledger_wallet_id_index', 'ledger_transaction_id_index', 'ledger_receipt_no_index'];
            
            foreach ($indexes as $index) {
                if (Schema::hasIndex('ledger', $index)) {
                    $table->dropIndex($index);
                }
            }
            
            // Drop columns if they exist
            if (Schema::hasColumn('ledger', 'currency')) {
                $table->dropColumn('currency');
            }
            
            if (Schema::hasColumn('ledger', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
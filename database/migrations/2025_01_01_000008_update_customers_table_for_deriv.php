<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Add new columns for Deriv integration
            $table->string('deriv_currency', 3)->nullable()->after('deriv_account_number');
            $table->json('all_deriv_accounts')->nullable()->after('deriv_currency');
            $table->string('user_id', 50)->nullable()->after('deriv_verified');
            $table->string('email', 255)->nullable()->after('user_id');
            $table->string('country', 100)->nullable()->after('email');
            $table->string('landing_company_name', 100)->nullable()->after('country');
            $table->string('landing_company_fullname', 255)->nullable()->after('landing_company_name');
            $table->json('scopes')->nullable()->after('landing_company_fullname');
            $table->boolean('is_virtual')->default(false)->after('scopes');
            $table->json('account_list')->nullable()->after('is_virtual');
            $table->string('first_name', 100)->nullable()->after('account_list');
            $table->string('last_name', 100)->nullable()->after('first_name');
            $table->date('date_of_birth')->nullable()->after('last_name');
            $table->string('place_of_birth', 100)->nullable()->after('date_of_birth');
            $table->string('address_line_1', 255)->nullable()->after('place_of_birth');
            $table->string('address_line_2', 255)->nullable()->after('address_line_1');
            $table->string('address_city', 100)->nullable()->after('address_line_2');
            $table->string('address_state', 100)->nullable()->after('address_city');
            $table->string('address_postcode', 20)->nullable()->after('address_state');
            $table->string('tax_identification_number', 50)->nullable()->after('address_postcode');
            $table->string('tax_residence', 100)->nullable()->after('tax_identification_number');
            $table->boolean('has_secret_answer')->default(false)->after('tax_residence');
            $table->boolean('email_consent')->default(false)->after('has_secret_answer');
            
            // Add index for frequently queried fields
            $table->index('email');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop the columns we added
            $table->dropColumn([
                'deriv_currency',
                'all_deriv_accounts',
                'user_id',
                'email',
                'country',
                'landing_company_name',
                'landing_company_fullname',
                'scopes',
                'is_virtual',
                'account_list',
                'first_name',
                'last_name',
                'date_of_birth',
                'place_of_birth',
                'address_line_1',
                'address_line_2',
                'address_city',
                'address_state',
                'address_postcode',
                'tax_identification_number',
                'tax_residence',
                'has_secret_answer',
                'email_consent'
            ]);

            // Drop indexes
            $table->dropIndex(['email']);
            $table->dropIndex(['user_id']);
        });
    }
};

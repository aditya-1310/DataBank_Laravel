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
        Schema::table('market_data', function (Blueprint $table) {
            if (!Schema::hasColumn('market_data', 'date')) {
                $table->date('date')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('market_data', 'symbol')) {
                $table->string('symbol', 10)->nullable()->after('date');
            }
            
            if (!Schema::hasColumn('market_data', 'open')) {
                $table->decimal('open', 10, 2)->nullable()->after('symbol');
            }
            
            if (!Schema::hasColumn('market_data', 'high')) {
                $table->decimal('high', 10, 2)->nullable()->after('open');
            }
            
            if (!Schema::hasColumn('market_data', 'low')) {
                $table->decimal('low', 10, 2)->nullable()->after('high');
            }
            
            if (!Schema::hasColumn('market_data', 'close')) {
                $table->decimal('close', 10, 2)->nullable()->after('low');
            }
            
            if (!Schema::hasColumn('market_data', 'volume')) {
                $table->unsignedBigInteger('volume')->nullable()->after('close');
            }
        });
        
        // Try to add the unique index if it doesn't exist
        try {
            Schema::table('market_data', function (Blueprint $table) {
                $table->unique(['date', 'symbol'], 'market_data_date_symbol_unique');
            });
        } catch (\Exception $e) {
            // Index might already exist, ignore errors
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_data', function (Blueprint $table) {
            // Drop columns if they exist
            $columns = ['date', 'symbol', 'open', 'high', 'low', 'close', 'volume'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('market_data', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Try to drop the unique index if it exists
            try {
                $table->dropUnique('market_data_date_symbol_unique');
            } catch (\Exception $e) {
                // Index might not exist, ignore errors
            }
        });
    }
};

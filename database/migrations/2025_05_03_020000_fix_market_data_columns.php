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
        // First let's make all the original columns nullable
        if (Schema::hasColumn('market_data', 'symbol')) {
            DB::statement('ALTER TABLE market_data MODIFY symbol VARCHAR(10) NULL');
        }
        
        if (Schema::hasColumn('market_data', 'open')) {
            DB::statement('ALTER TABLE market_data MODIFY open DECIMAL(10,2) NULL');
        }
        
        if (Schema::hasColumn('market_data', 'high')) {
            DB::statement('ALTER TABLE market_data MODIFY high DECIMAL(10,2) NULL');
        }
        
        if (Schema::hasColumn('market_data', 'low')) {
            DB::statement('ALTER TABLE market_data MODIFY low DECIMAL(10,2) NULL');
        }
        
        if (Schema::hasColumn('market_data', 'close')) {
            DB::statement('ALTER TABLE market_data MODIFY close DECIMAL(10,2) NULL');
        }
        
        if (Schema::hasColumn('market_data', 'volume')) {
            DB::statement('ALTER TABLE market_data MODIFY volume BIGINT UNSIGNED NULL');
        }
        
        if (Schema::hasColumn('market_data', 'is_approved')) {
            DB::statement('ALTER TABLE market_data MODIFY is_approved TINYINT(1) NULL DEFAULT 0');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't need a down migration as this is a fix
    }
}; 
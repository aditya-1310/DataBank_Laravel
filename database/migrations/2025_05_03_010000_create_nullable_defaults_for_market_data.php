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
        // First make sure both fields exist
        Schema::table('market_data', function (Blueprint $table) {
            if (!Schema::hasColumn('market_data', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
            
            if (!Schema::hasColumn('market_data', 'submitted_by')) {
                $table->unsignedBigInteger('submitted_by')->nullable();
            }
        });

        // Change to allow null values for both columns
        // This is a direct SQL query because Schema doesn't support modifying existing columns properly
        DB::statement('ALTER TABLE market_data MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE market_data MODIFY submitted_by BIGINT UNSIGNED NULL');
        
        // Add a default value for both columns
        DB::statement('ALTER TABLE market_data ALTER user_id SET DEFAULT 1');
        DB::statement('ALTER TABLE market_data ALTER submitted_by SET DEFAULT 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove defaults
        DB::statement('ALTER TABLE market_data ALTER user_id DROP DEFAULT');
        DB::statement('ALTER TABLE market_data ALTER submitted_by DROP DEFAULT');
    }
}; 
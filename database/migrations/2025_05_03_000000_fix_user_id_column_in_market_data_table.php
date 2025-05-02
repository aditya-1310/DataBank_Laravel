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
        Schema::table('market_data', function (Blueprint $table) {
            // Check if user_id column doesn't exist but submitted_by does
            if (!Schema::hasColumn('market_data', 'user_id') && Schema::hasColumn('market_data', 'submitted_by')) {
                // Add user_id with nullable constraint
                $table->unsignedBigInteger('user_id')->nullable();
                
                // Update the data - copy submitted_by values to user_id
                DB::statement('UPDATE market_data SET user_id = submitted_by WHERE submitted_by IS NOT NULL');
            }
            
            // If submitted_by exists but no user_id, then we need to add the column
            if (Schema::hasColumn('market_data', 'submitted_by') && !Schema::hasColumn('market_data', 'user_id')) {
                // Make user_id nullable 
                $table->unsignedBigInteger('user_id')->nullable();
            }
            
            // If no submitted_by column exists
            if (!Schema::hasColumn('market_data', 'submitted_by')) {
                // Add the submitted_by column
                $table->unsignedBigInteger('submitted_by')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_data', function (Blueprint $table) {
            // No need to drop anything as this is a fix migration
        });
    }
}; 
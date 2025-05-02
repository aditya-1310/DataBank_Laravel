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
            // Add new columns that don't exist
            if (!Schema::hasColumn('market_data', 'market_name')) {
                $table->string('market_name')->nullable();
            }
            if (!Schema::hasColumn('market_data', 'product_name')) {
                $table->string('product_name')->nullable();
            }
            if (!Schema::hasColumn('market_data', 'price')) {
                $table->decimal('price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('market_data', 'quantity')) {
                $table->decimal('quantity', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('market_data', 'source')) {
                $table->string('source')->nullable();
            }
            if (!Schema::hasColumn('market_data', 'status')) {
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->nullable();
            }
            if (!Schema::hasColumn('market_data', 'submitted_by') && Schema::hasColumn('market_data', 'user_id')) {
                // Create submitted_by as a separate column
                $table->foreignId('submitted_by')->nullable()->constrained('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_data', function (Blueprint $table) {
            // Drop columns if they exist
            $columns = ['market_name', 'product_name', 'price', 'quantity', 'source', 'status', 'submitted_by'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('market_data', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

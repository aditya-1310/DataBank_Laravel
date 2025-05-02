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
        Schema::create('market_data_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_data_id')->constrained('market_data')->onDelete('cascade');
            $table->foreignId('changed_by')->nullable()->constrained('users');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_data_status_history');
    }
}; 
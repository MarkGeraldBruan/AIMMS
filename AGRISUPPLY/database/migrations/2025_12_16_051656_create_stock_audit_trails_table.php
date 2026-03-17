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
        Schema::create('stock_audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade');
            $table->foreignId('stock_movement_id')->nullable()->constrained('stock_movements')->onDelete('set null');
            $table->string('action_type', 50); // ← CHANGED from default to 50 characters
            $table->integer('quantity');
            $table->integer('balance_before')->default(0);
            $table->integer('balance_after')->default(0);
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_audit_trails');
    }
};
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
        Schema::create('announcement_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade');
            $table->integer('quantity_needed')->default(0);
            $table->integer('quantity_used')->default(0);
            $table->enum('status', ['pending', 'reserved', 'used', 'returned'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
            
            // Ensure unique supply per announcement
            $table->unique(['announcement_id', 'supply_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_supplies');
    }
};
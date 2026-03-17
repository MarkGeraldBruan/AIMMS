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
        Schema::create('stockcard', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supply_id')->constrained('supplies')->onDelete('cascade');
            $table->integer('quantity_in')->default(0);
            $table->integer('quantity_out')->default(0);
            $table->integer('balance')->default(0);
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockcard');
    }
};
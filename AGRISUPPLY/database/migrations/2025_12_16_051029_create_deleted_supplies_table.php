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
        Schema::create('deleted_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('supply_id'); // Original ID from supplies table
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('unit')->default('pcs');
            $table->string('category')->nullable();
            $table->string('supplier')->nullable();
            $table->date('purchase_date')->nullable();
            $table->integer('minimum_stock')->default(0);
            $table->text('notes')->nullable();
            $table->decimal('total_value', 10, 2)->default(0);
            $table->text('reason')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes(); // For permanent deletion feature
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_supplies');
    }
};
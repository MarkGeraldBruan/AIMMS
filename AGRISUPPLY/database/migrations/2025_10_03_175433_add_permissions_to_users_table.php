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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_create')->default(false)->after('status');
            $table->boolean('can_read')->default(true)->after('can_create');
            $table->boolean('can_update')->default(false)->after('can_read');
            $table->boolean('can_delete')->default(false)->after('can_update');
            $table->boolean('can_stock_in')->default(false)->after('can_delete');
            $table->boolean('can_stock_out')->default(false)->after('can_stock_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if columns exist before dropping
            if (Schema::hasColumn('users', 'can_stock_out')) {
                $table->dropColumn('can_stock_out');
            }
            if (Schema::hasColumn('users', 'can_stock_in')) {
                $table->dropColumn('can_stock_in');
            }
            if (Schema::hasColumn('users', 'can_delete')) {
                $table->dropColumn('can_delete');
            }
            if (Schema::hasColumn('users', 'can_update')) {
                $table->dropColumn('can_update');
            }
            if (Schema::hasColumn('users', 'can_read')) {
                $table->dropColumn('can_read');
            }
            if (Schema::hasColumn('users', 'can_create')) {
                $table->dropColumn('can_create');
            }
        });
    }
};
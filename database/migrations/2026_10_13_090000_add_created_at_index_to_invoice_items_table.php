<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index for the invoice detail list, which filters invoice items by a created_at date range.
 */
return new class extends Migration
{
    public function up(): void
    {
        // The sale tables come from the legacy database, so skip when they don't exist (e.g. tests).
        if (Schema::hasTable('invoice_items') && ! Schema::hasIndex('invoice_items', 'invoice_items_created_at_index')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->index('created_at', 'invoice_items_created_at_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('invoice_items', 'invoice_items_created_at_index')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropIndex('invoice_items_created_at_index');
            });
        }
    }
};

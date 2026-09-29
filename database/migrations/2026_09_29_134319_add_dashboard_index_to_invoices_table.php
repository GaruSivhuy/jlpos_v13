<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexes for the dashboard widgets, which filter invoices by status + payment_date
 * and sum invoice item quantity per product.
 */
return new class extends Migration
{
    public function up(): void
    {
        // The sale tables come from the legacy database, so skip when they don't exist (e.g. tests).
        if (Schema::hasTable('invoices') && ! Schema::hasIndex('invoices', 'invoices_status_payment_date_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['status', 'payment_date', 'branch_id'], 'invoices_status_payment_date_index');
            });
        }

        if (Schema::hasTable('invoice_items') && ! Schema::hasIndex('invoice_items', 'invoice_items_invoice_product_qty_index')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->index(['invoice_id', 'deleted_at', 'inventory_id', 'quantity'], 'invoice_items_invoice_product_qty_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('invoices', 'invoices_status_payment_date_index')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex('invoices_status_payment_date_index');
            });
        }

        if (Schema::hasIndex('invoice_items', 'invoice_items_invoice_product_qty_index')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropIndex('invoice_items_invoice_product_qty_index');
            });
        }
    }
};
